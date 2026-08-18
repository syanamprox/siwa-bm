import type { ReactNode } from 'react'
import { ArrowDownRight, ArrowUpRight } from 'lucide-react'
import { Card } from '@/components/ui/primitives'
import { cn } from '@/lib/utils'

export function KpiCard({
  label,
  value,
  sub,
  icon,
  accent = '#059669',
  delta,
  deltaGood = 'up',
}: {
  label: string
  value: ReactNode
  sub?: ReactNode
  icon?: ReactNode
  accent?: string
  /** signed % change vs previous period; omit to hide */
  delta?: number | null
  /** which direction is "good" (green) — 'up' for clicks, 'down' for CPC */
  deltaGood?: 'up' | 'down'
}) {
  const hasDelta = delta != null && Number.isFinite(delta)
  const up = (delta ?? 0) >= 0
  const good = hasDelta && (deltaGood === 'up' ? up : !up)

  return (
    <Card className="group card-hover p-5">
      <div className="flex items-start justify-between">
        <span className="text-[11px] font-semibold uppercase tracking-wider text-slate-500">
          {label}
        </span>
        {icon && (
          <span
            className="flex h-9 w-9 items-center justify-center rounded-xl"
            style={{ background: `${accent}14`, color: accent }}
          >
            {icon}
          </span>
        )}
      </div>
      <div className="mt-3 text-[28px] font-extrabold leading-none tracking-tight tabular-nums text-slate-900">
        {value}
      </div>
      <div className="mt-2 flex items-center gap-2">
        {hasDelta && (
          <span
            className={cn(
              'inline-flex items-center gap-0.5 rounded-full px-1.5 py-0.5 text-[11px] font-bold',
              good ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-600',
            )}
          >
            {up ? <ArrowUpRight size={12} /> : <ArrowDownRight size={12} />}
            {Math.abs(delta as number).toFixed(1)}%
          </span>
        )}
        {sub && <span className="text-xs text-slate-400">{sub}</span>}
      </div>
    </Card>
  )
}
