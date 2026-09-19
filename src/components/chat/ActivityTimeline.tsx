'use client'

import { useEffect, useState, type ReactNode } from 'react'
import { Search, Database, Lightbulb, PencilLine, BarChart3, ChevronDown } from 'lucide-react'
import { cn } from '@/lib/utils'

/**
 * The work timeline shown inside an assistant message, above the answer.
 * Live: expanded, current step pulsing, "Sedang bekerja Ns" ticking.
 * Done: collapsed to "Selesai dalam Ns ⌄", expandable.
 *
 * Ported from ~/starlabs/intelligence (read-only source) and mapped onto this
 * app's NDJSON stream — see `activityFor()` in use-chat.ts. Nothing new is
 * persisted server-side; every step is derived from the events as they arrive.
 */

export type Activity = {
  kind: 'think' | 'query' | 'write' | 'compose' | 'viz'
  label: string
  detail?: string
}

const ICON: Record<Activity['kind'], typeof Search> = {
  think: Lightbulb,
  query: Database,
  write: PencilLine,
  compose: PencilLine,
  viz: BarChart3,
}

const fmtSecs = (ms: number) => `${Math.max(1, Math.round(ms / 1000))} detik`

function StepRow({ item, current }: { item: Activity; current: boolean }) {
  const Icon = ICON[item.kind] || Lightbulb
  return (
    <div className="relative pl-6">
      <span
        className={cn(
          'absolute left-0 top-[3px] flex h-4 w-4 items-center justify-center text-slate-400',
          current && 'animate-pulse text-brand-500',
        )}
      >
        <Icon size={13} strokeWidth={2} />
      </span>
      <div className={cn('text-[12.5px] leading-5 text-slate-500', current && 'animate-pulse text-slate-600')}>
        {item.label}
        {item.detail && <span className="ml-1.5 font-mono text-[11px] text-slate-400">{item.detail}</span>}
      </div>
    </div>
  )
}

export function ActivityTimeline({
  items, live, startedAt, workedMs,
}: {
  items: Activity[]
  live: boolean
  startedAt?: number
  workedMs?: number
}) {
  const [open, setOpen] = useState(false)
  const [, tick] = useState(0)

  // live seconds counter
  useEffect(() => {
    if (!live) return
    const t = setInterval(() => tick((n) => n + 1), 1000)
    return () => clearInterval(t)
  }, [live])

  if (!items.length) return null
  const elapsed = live && startedAt ? Date.now() - startedAt : workedMs || 0

  const timeline = (
    <div className="relative space-y-2.5">
      {/* thin connector line through the icons */}
      <span aria-hidden className="absolute bottom-1 left-[7.5px] top-1 w-px bg-slate-200" />
      {items.map((it, i) => (
        <StepRow key={i} item={it} current={live && i === items.length - 1} />
      ))}
    </div>
  )

  if (live) {
    return (
      <div className="mb-4">
        {timeline}
        <div className="mt-2 pl-6 text-[11.5px] font-medium text-slate-400">
          <Shimmer>Sedang bekerja {fmtSecs(elapsed)}</Shimmer>
        </div>
      </div>
    )
  }

  return (
    <div className={cn('mb-3', open && 'pb-1')}>
      <button
        onClick={() => setOpen((v) => !v)}
        className="inline-flex items-center gap-1 rounded-md px-1 py-0.5 text-[11.5px] font-medium text-slate-400 transition hover:bg-slate-50 hover:text-slate-600"
      >
        Selesai dalam {fmtSecs(elapsed)}
        <ChevronDown size={12} className={cn('transition-transform', open && 'rotate-180')} />
      </button>
      {open && <div className="mt-2">{timeline}</div>}
    </div>
  )
}

/** a soft left-to-right shimmer over the working label */
function Shimmer({ children }: { children: ReactNode }) {
  return (
    <span className="inline-block animate-shimmer bg-[linear-gradient(90deg,#94a3b8_35%,#cbd5e1_50%,#94a3b8_65%)] bg-[length:200%_100%] bg-clip-text text-transparent">
      {children}
    </span>
  )
}
