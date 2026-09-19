import { useRef, useState, type ReactNode } from 'react'
import ReactMarkdown from 'react-markdown'
import remarkGfm from 'remark-gfm'
import remarkBreaks from 'remark-breaks'
import { Copy, Check, Download } from 'lucide-react'
import { rowsToCsv, rowsToTsv, fileSlug, tableRows, downloadCsv } from '@/lib/table-export'

/**
 * Chat renderer — react-markdown + GFM (the approach OLA+ proved out in
 * production), with our additions: positional ```chart blocks rendered as
 * inline SVG charts, code blocks with a language label + copy button, and
 * first-class tables (brand-tinted header, striping, horizontal scroll, and a
 * hover toolbar: Copy TSV / Export CSV named after the chat).
 *
 * Streaming safety: the whole accumulated text re-renders per delta;
 * a still-open ```chart block is stripped until it completes (OLA+ pattern)
 * so raw JSON never flashes, and a malformed chart block degrades to a
 * plain code block — never a crash.
 */

/* ---- inline SVG charts — brand-anchored, no chart lib ---- */
type ChartSpec = { type: 'bar' | 'pie' | 'line'; title?: string; data: { label: string; value: number }[] }
const PALETTE = ['#2563eb', '#60a5fa', '#93c5fd', '#f59e0b', '#10b981', '#f43f5e', '#a855f7', '#64748b']
/** Indonesian magnitude short-form — rupiah figures run to the billions here,
 *  so the upstream "k"-only formatter printed things like "2097972.7k". */
const fmt = (n: number) => {
  const a = Math.abs(n)
  const trim = (v: number) => (Math.round(v * 10) / 10).toString()
  if (a >= 1e12) return trim(n / 1e12) + ' T'
  if (a >= 1e9) return trim(n / 1e9) + ' M'
  if (a >= 1e6) return trim(n / 1e6) + ' jt'
  if (a >= 1e3) return trim(n / 1e3) + ' rb'
  return String(n)
}

export function BarChart({ data }: { data: ChartSpec['data'] }) {
  const max = Math.max(1, ...data.map((d) => d.value))
  return (
    <div className="space-y-1.5">
      {data.map((d, i) => (
        <div key={i} className="flex items-center gap-2">
          <div className="w-[34%] shrink-0 truncate text-right text-[11px] font-medium text-slate-500" title={d.label}>{d.label}</div>
          <div className="relative h-5 flex-1 overflow-hidden rounded-md bg-slate-100">
            <div className="h-full rounded-md" style={{ width: `${Math.max(3, (d.value / max) * 100)}%`, background: `linear-gradient(90deg,${PALETTE[i % PALETTE.length]}cc,${PALETTE[i % PALETTE.length]})` }} />
            <span className="absolute right-1.5 top-1/2 -translate-y-1/2 text-[10.5px] font-bold text-slate-600">{fmt(d.value)}</span>
          </div>
        </div>
      ))}
    </div>
  )
}
function PieChart({ data }: { data: ChartSpec['data'] }) {
  const total = Math.max(1, data.reduce((s, d) => s + d.value, 0))
  let a = -Math.PI / 2
  const R = 46, C = 56
  const arcs = data.map((d, i) => {
    const frac = d.value / total, a0 = a, a1 = a + frac * Math.PI * 2; a = a1
    const large = a1 - a0 > Math.PI ? 1 : 0
    const x0 = C + R * Math.cos(a0), y0 = C + R * Math.sin(a0), x1 = C + R * Math.cos(a1), y1 = C + R * Math.sin(a1)
    return { d: `M${C},${C} L${x0.toFixed(1)},${y0.toFixed(1)} A${R},${R} 0 ${large} 1 ${x1.toFixed(1)},${y1.toFixed(1)} Z`, color: PALETTE[i % PALETTE.length], pct: Math.round(frac * 100) }
  })
  return (
    <div className="flex items-center gap-3">
      <svg viewBox="0 0 112 112" className="h-28 w-28 shrink-0">
        {arcs.map((s, i) => <path key={i} d={s.d} fill={s.color} stroke="#fff" strokeWidth="1.5" />)}
        <circle cx={C} cy={C} r="20" fill="#fff" />
      </svg>
      <div className="min-w-0 flex-1 space-y-1">
        {data.map((d, i) => (
          <div key={i} className="flex items-center gap-1.5 text-[11px]">
            <span className="h-2.5 w-2.5 shrink-0 rounded-sm" style={{ background: PALETTE[i % PALETTE.length] }} />
            <span className="truncate text-slate-600">{d.label}</span>
            <span className="ml-auto shrink-0 font-bold text-slate-700">{arcs[i].pct}%</span>
          </div>
        ))}
      </div>
    </div>
  )
}
function LineChart({ data }: { data: ChartSpec['data'] }) {
  const W = 300, H = 120, PADX = 24, PT = 10, PB = 22
  const max = Math.max(1, ...data.map((d) => d.value)), min = Math.min(0, ...data.map((d) => d.value))
  const xs = (i: number) => PADX + (i / Math.max(1, data.length - 1)) * (W - 2 * PADX)
  const ys = (v: number) => H - PB - ((v - min) / Math.max(1, max - min)) * (H - PT - PB)
  const pts = data.map((d, i) => `${xs(i).toFixed(1)},${ys(d.value).toFixed(1)}`).join(' ')
  return (
    <svg viewBox={`0 0 ${W} ${H}`} className="w-full" style={{ maxWidth: W }}>
      <polyline points={`${xs(0)},${H - PB} ${pts} ${xs(data.length - 1)},${H - PB}`} fill="#2563eb14" stroke="none" />
      <polyline points={pts} fill="none" stroke="#2563eb" strokeWidth="2" strokeLinejoin="round" strokeLinecap="round" />
      {data.map((d, i) => (
        <g key={i}>
          <circle cx={xs(i)} cy={ys(d.value)} r="2.6" fill="#2563eb" stroke="#fff" strokeWidth="1.2" />
          <text x={xs(i)} y={H - 7} textAnchor="middle" className="fill-slate-400" style={{ fontSize: 8 }}>{d.label.length > 6 ? d.label.slice(0, 6) : d.label}</text>
          <text x={xs(i)} y={ys(d.value) - 5} textAnchor="middle" className="fill-slate-500" style={{ fontSize: 8, fontWeight: 700 }}>{fmt(d.value)}</text>
        </g>
      ))}
    </svg>
  )
}
function Chart({ spec }: { spec: ChartSpec }) {
  const data = (spec.data || []).filter((d) => d && typeof d.value === 'number' && !Number.isNaN(d.value)).slice(0, 8)
  if (!data.length) return null
  return (
    <div className="my-4 rounded-xl border border-slate-200 bg-white p-3.5">
      {spec.title && <div className="mb-2 text-[11.5px] font-bold text-slate-700">{spec.title}</div>}
      {spec.type === 'pie' ? <PieChart data={data} /> : spec.type === 'line' ? <LineChart data={data} /> : <BarChart data={data} />}
    </div>
  )
}

/* ---- split message text into markdown + chart segments (position preserved) ---- */
type Seg = { kind: 'md'; text: string } | { kind: 'chart'; spec: ChartSpec }

export function segment(text: string): Seg[] {
  const segs: Seg[] = []
  const re = /```chart[^\S\n]*\n([\s\S]*?)```/g
  let last = 0
  let m: RegExpExecArray | null
  while ((m = re.exec(text)) !== null) {
    if (m.index > last) segs.push({ kind: 'md', text: text.slice(last, m.index) })
    try {
      const o = JSON.parse(m[1].trim())
      const data = Array.isArray(o?.data)
        ? o.data
            .filter((d: unknown) => d && (d as { value?: unknown }).value != null && !Number.isNaN(Number((d as { value: unknown }).value)))
            .map((d: { label?: unknown; value: unknown }) => ({ label: String(d.label ?? ''), value: Number(d.value) }))
        : []
      if (data.length) segs.push({ kind: 'chart', spec: { type: o?.type === 'pie' ? 'pie' : o?.type === 'line' ? 'line' : 'bar', title: typeof o?.title === 'string' ? o.title : undefined, data } })
      else segs.push({ kind: 'md', text: '```json\n' + m[1].trim() + '\n```' }) // empty/odd → show as code
    } catch {
      segs.push({ kind: 'md', text: '```json\n' + m[1].trim() + '\n```' }) // malformed → degrade to code block
    }
    last = m.index + m[0].length
  }
  let rest = text.slice(last)
  // a chart block still streaming (unclosed) → hold it back so raw JSON never flashes
  rest = rest.replace(/```chart[\s\S]*$/, '')
  if (rest) segs.push({ kind: 'md', text: rest })
  return segs
}

/* ---- code block with language label + copy ---- */
function textOf(node: ReactNode): string {
  if (node == null || typeof node === 'boolean') return ''
  if (typeof node === 'string' || typeof node === 'number') return String(node)
  if (Array.isArray(node)) return node.map(textOf).join('')
  if (typeof node === 'object' && 'props' in node) return textOf((node as { props: { children?: ReactNode } }).props.children)
  return ''
}

async function toClipboard(text: string): Promise<void> {
  try { await navigator.clipboard.writeText(text) } catch {
    // clipboard API blocked (http, permissions) → the OLA+ fallback
    const ta = document.createElement('textarea')
    ta.value = text
    document.body.appendChild(ta)
    ta.select()
    document.execCommand('copy')
    document.body.removeChild(ta)
  }
}

function CodeBlock({ children }: { children?: ReactNode }) {
  const [copied, setCopied] = useState(false)
  const codeEl = (Array.isArray(children) ? children[0] : children) as { props?: { className?: string; children?: ReactNode } } | null
  const lang = /language-([\w+-]+)/.exec(codeEl?.props?.className || '')?.[1] || ''
  const raw = textOf(codeEl?.props?.children).replace(/\n$/, '')
  return (
    <div className="my-4 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
      <div className="flex items-center justify-between border-b border-slate-200/70 bg-slate-100/70 py-1 pl-3 pr-1.5">
        <span className="text-[10.5px] font-bold uppercase tracking-wide text-slate-400">{lang || 'code'}</span>
        <button
          onClick={() => { toClipboard(raw); setCopied(true); setTimeout(() => setCopied(false), 1600) }}
          className="inline-flex items-center gap-1 rounded-md px-1.5 py-1 text-[10.5px] font-semibold text-slate-400 transition hover:bg-white hover:text-slate-600"
        >
          {copied ? <><Check size={11} className="text-emerald-500" /> Tersalin</> : <><Copy size={11} /> Salin</>}
        </button>
      </div>
      <pre className="overflow-x-auto p-3 text-[12px] leading-relaxed text-slate-700">{raw}</pre>
    </div>
  )
}

/* ---- table with its own toolbar row: Copy (TSV) / Export CSV ---- */
function TableBlock({ children, exportName }: { children?: ReactNode; exportName?: string }) {
  const wrapRef = useRef<HTMLDivElement>(null)
  const [copied, setCopied] = useState(false)
  const rows = () => {
    const t = wrapRef.current?.querySelector('table')
    return t ? tableRows(t) : []
  }
  return (
    // my-7 = clearly banded sections; the toolbar takes REAL height inside the
    // border (above the table) so it can never overlap a heading or header row
    <div className="group/table my-7">
      <div className="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div className="flex h-8 items-center justify-end gap-1 border-b border-slate-100 bg-canvas/50 px-1.5">
          <button
            onClick={() => { toClipboard(rowsToTsv(rows())); setCopied(true); setTimeout(() => setCopied(false), 1600) }}
            className="inline-flex items-center gap-1 rounded-full px-2 py-1 text-[10.5px] font-semibold text-slate-400 opacity-0 transition hover:bg-white hover:text-brand-600 focus:opacity-100 group-hover/table:opacity-100"
            title="Salin tabel (langsung tempel ke Excel/Sheets)"
          >
            {copied ? <><Check size={11} className="text-emerald-500" /> Tersalin</> : <><Copy size={11} /> Salin</>}
          </button>
          <button
            onClick={() => downloadCsv(rowsToCsv(rows()), `${fileSlug(exportName)}.csv`)}
            className="inline-flex items-center gap-1 rounded-full px-2 py-1 text-[10.5px] font-semibold text-slate-400 opacity-0 transition hover:bg-white hover:text-brand-600 focus:opacity-100 group-hover/table:opacity-100"
            title="Unduh sebagai CSV"
          >
            <Download size={11} /> CSV
          </button>
        </div>
        <div ref={wrapRef} className="overflow-x-auto">
          <table className="w-full border-collapse text-left text-[13px]">{children}</table>
        </div>
      </div>
    </div>
  )
}

/* ---- the renderer ---- */
export function Markdown({ text, exportName }: { text: string; exportName?: string }) {
  const segs = segment(text)
  return (
    <div className="space-y-0.5 [&>*:first-child]:mt-0 [&>*:last-child]:mb-0">
      {segs.map((s, i) =>
        s.kind === 'chart' ? (
          <Chart key={i} spec={s.spec} />
        ) : (
          <ReactMarkdown
            key={i}
            remarkPlugins={[remarkGfm, remarkBreaks]}
            components={{
              p: (p) => <p className="mb-4 text-[14px] leading-relaxed text-slate-600 last:mb-0" {...p} />,
              strong: (p) => <strong className="font-bold text-slate-800" {...p} />,
              em: (p) => <em className="italic" {...p} />,
              a: ({ children, href }) => <a href={href} target="_blank" rel="noreferrer" className="font-medium text-brand-600 underline decoration-brand-300 hover:text-brand-700">{children}</a>,
              ul: (p) => <ul className="mb-4 list-disc space-y-1.5 pl-5 text-[14px] text-slate-600" {...p} />,
              ol: (p) => <ol className="mb-4 list-decimal space-y-1.5 pl-5 text-[14px] text-slate-600" {...p} />,
              li: (p) => <li className="pl-0.5 leading-relaxed marker:text-brand-500" {...p} />,
              h1: (p) => <h3 className="mb-4 mt-10 text-[16.5px] font-bold text-slate-800" {...p} />,
              h2: (p) => <h3 className="mb-4 mt-9 text-[15.5px] font-bold text-slate-800" {...p} />,
              h3: (p) => <h4 className="mb-3 mt-7 text-[14.5px] font-bold text-slate-800" {...p} />,
              h4: (p) => <h5 className="mb-2.5 mt-5 text-[14px] font-semibold text-slate-700" {...p} />,
              blockquote: (p) => <blockquote className="my-4 border-l-2 border-brand-300 pl-3 text-[13.5px] italic text-slate-500" {...p} />,
              hr: () => <hr className="my-5 border-line" />,
              // inline code — block code is unwrapped and restyled by the `pre` renderer
              code: (p) => <code className="rounded bg-brand-50 px-1 py-0.5 font-mono text-[12.5px] text-brand-700" {...p} />,
              pre: ({ children }) => <CodeBlock>{children}</CodeBlock>,
              table: ({ children }) => <TableBlock exportName={exportName}>{children}</TableBlock>,
              thead: (p) => <thead className="bg-brand-50/70" {...p} />,
              tbody: (p) => <tbody className="[&>tr:nth-child(even)]:bg-slate-50/60" {...p} />,
              th: (p) => <th className="whitespace-nowrap border-b border-slate-200 px-3.5 py-2.5 text-[12.5px] font-bold text-slate-600" {...p} />,
              td: (p) => <td className="border-t border-slate-100 px-3.5 py-2 text-slate-700" {...p} />,
            }}
          >
            {s.text}
          </ReactMarkdown>
        ),
      )}
    </div>
  )
}
