'use client'

import * as React from 'react'
import { cn } from '@/lib/utils'

/* ---------------- Button ---------------- */
type BtnVariant = 'primary' | 'secondary' | 'ghost' | 'danger' | 'success' | 'outline'
type BtnSize = 'sm' | 'md' | 'icon'

const btnVariants: Record<BtnVariant, string> = {
  primary: 'bg-brand-600 text-white shadow-sm hover:bg-brand-700 disabled:opacity-50',
  secondary: 'bg-white text-slate-700 border border-line shadow-sm hover:bg-slate-50 hover:text-slate-900 disabled:opacity-50',
  outline: 'bg-transparent text-slate-700 border border-slate-300 hover:border-brand-400 hover:text-brand-700',
  ghost: 'bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900',
  danger: 'bg-danger text-white shadow-sm hover:brightness-95 disabled:opacity-50',
  success: 'bg-success text-white shadow-sm hover:brightness-95 disabled:opacity-50',
}
const btnSizes: Record<BtnSize, string> = {
  sm: 'h-8 px-3 text-xs gap-1.5 rounded-lg',
  md: 'h-10 px-4 text-sm gap-2 rounded-xl',
  icon: 'h-10 w-10 justify-center rounded-xl',
}

export function Button({
  variant = 'primary',
  size = 'md',
  className,
  ...props
}: React.ButtonHTMLAttributes<HTMLButtonElement> & { variant?: BtnVariant; size?: BtnSize }) {
  return (
    <button
      className={cn(
        'inline-flex items-center font-semibold transition-all duration-150 disabled:cursor-not-allowed focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 active:scale-[.98]',
        btnVariants[variant],
        btnSizes[size],
        className,
      )}
      {...props}
    />
  )
}

/* ---------------- Card ---------------- */
export function Card({ className, ...props }: React.HTMLAttributes<HTMLDivElement>) {
  return (
    <div
      className={cn('rounded-2xl border border-line bg-surface shadow-card', className)}
      {...props}
    />
  )
}

/* ---------------- Input / Select / Textarea ---------------- */
const fieldBase =
  'w-full rounded-xl border border-line bg-white text-sm text-slate-900 placeholder:text-slate-400 transition-shadow focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400'

export const Input = React.forwardRef<HTMLInputElement, React.InputHTMLAttributes<HTMLInputElement>>(
  function Input({ className, ...props }, ref) {
    return <input ref={ref} className={cn(fieldBase, 'h-10 px-3.5', className)} {...props} />
  },
)

export { Select } from './Select'

export const Textarea = React.forwardRef<
  HTMLTextAreaElement,
  React.TextareaHTMLAttributes<HTMLTextAreaElement>
>(function Textarea({ className, ...props }, ref) {
  return <textarea ref={ref} className={cn(fieldBase, 'px-3.5 py-2.5', className)} {...props} />
})

export function Label({ className, ...props }: React.LabelHTMLAttributes<HTMLLabelElement>) {
  return (
    <label
      className={cn('mb-1.5 block text-xs font-semibold text-slate-600', className)}
      {...props}
    />
  )
}

/* ---------------- Badge ---------------- */
const statusStyles: Record<string, string> = {
  ACTIVE: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
  active: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
  PAUSED: 'bg-amber-50 text-amber-700 ring-amber-600/20',
  paused: 'bg-amber-50 text-amber-700 ring-amber-600/20',
  DRAFT: 'bg-slate-100 text-slate-600 ring-slate-500/20',
  draft: 'bg-slate-100 text-slate-600 ring-slate-500/20',
  DELETED: 'bg-rose-50 text-rose-700 ring-rose-600/20',
  ARCHIVED: 'bg-slate-100 text-slate-500 ring-slate-500/20',
  archived: 'bg-slate-100 text-slate-500 ring-slate-500/20',
  scheduled: 'bg-sky-50 text-sky-700 ring-sky-600/20',
  published: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
  publishing: 'bg-sky-50 text-sky-700 ring-sky-600/20',
  failed: 'bg-rose-50 text-rose-700 ring-rose-600/20',
  cancelled: 'bg-slate-100 text-slate-500 ring-slate-500/20',
  approved: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
  pending: 'bg-amber-50 text-amber-700 ring-amber-600/20',
  rejected: 'bg-rose-50 text-rose-700 ring-rose-600/20',
  lunas: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
  'belum lunas': 'bg-amber-50 text-amber-700 ring-amber-600/20',
}

export function StatusBadge({ status }: { status?: string }) {
  const s = status ?? '—'
  const dot: Record<string, string> = {
    ACTIVE: 'bg-emerald-500',
    active: 'bg-emerald-500',
    PAUSED: 'bg-amber-500',
    paused: 'bg-amber-500',
    DRAFT: 'bg-slate-400',
    draft: 'bg-slate-400',
    failed: 'bg-rose-500',
    approved: 'bg-emerald-500',
    pending: 'bg-amber-500',
    rejected: 'bg-rose-500',
    lunas: 'bg-emerald-500',
    'belum lunas': 'bg-amber-500',
  }
  return (
    <span
      className={cn(
        'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wide ring-1 ring-inset',
        statusStyles[s] ?? 'bg-slate-100 text-slate-600 ring-slate-500/20',
      )}
    >
      <span className={cn('h-1.5 w-1.5 rounded-full', dot[s] ?? 'bg-slate-400')} />
      {s}
    </span>
  )
}

export function Badge({
  className,
  children,
}: {
  className?: string
  children: React.ReactNode
}) {
  return (
    <span
      className={cn(
        'inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-medium text-slate-600',
        className,
      )}
    >
      {children}
    </span>
  )
}

/* ---------------- Spinner ---------------- */
export function Spinner({ className }: { className?: string }) {
  return (
    <div
      className={cn(
        'animate-spin rounded-full border-2 border-slate-200 border-t-brand-600',
        className ?? 'h-5 w-5',
      )}
    />
  )
}

/* ---------------- Empty / Loading states ---------------- */
export function EmptyState({
  title,
  hint,
  icon,
}: {
  title: string
  hint?: string
  icon?: React.ReactNode
}) {
  return (
    <div className="flex flex-col items-center justify-center gap-2.5 py-16 text-center">
      {icon && (
        <div className="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
          {icon}
        </div>
      )}
      <p className="text-sm font-semibold text-slate-800">{title}</p>
      {hint && <p className="max-w-sm text-xs leading-relaxed text-slate-500">{hint}</p>}
    </div>
  )
}

export function Loading({ label = 'Memuat…' }: { label?: string }) {
  return (
    <div className="flex items-center justify-center gap-3 py-16 text-sm text-slate-500">
      <Spinner />
      {label}
    </div>
  )
}

/* ---------------- Skeleton ---------------- */
export function Skeleton({ className }: { className?: string }) {
  return <div className={cn('animate-pulse rounded-xl border border-line bg-slate-100', className)} />
}

/* ---------------- Avatar ---------------- */
const avatarSizes = {
  sm: 'h-8 w-8 text-[11px]',
  md: 'h-10 w-10 text-xs',
  lg: 'h-14 w-14 text-base',
  xl: 'h-20 w-20 text-2xl',
}

const avatarColors = [
  'bg-blue-100 text-blue-700',
  'bg-emerald-100 text-emerald-700',
  'bg-amber-100 text-amber-700',
  'bg-rose-100 text-rose-700',
  'bg-violet-100 text-violet-700',
  'bg-cyan-100 text-cyan-700',
  'bg-orange-100 text-orange-700',
]

function initials(name: string): string {
  const parts = name.trim().split(/\s+/)
  if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase()
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
}

function colorIndex(name: string): number {
  let h = 0
  for (let i = 0; i < name.length; i++) h = (h * 31 + name.charCodeAt(i)) >>> 0
  return h % avatarColors.length
}

export function Avatar({
  name,
  src,
  size = 'md',
  className,
}: {
  name: string
  src?: string | null
  size?: keyof typeof avatarSizes
  className?: string
}) {
  const cls = cn(
    'flex shrink-0 items-center justify-center rounded-full font-bold',
    avatarSizes[size],
    !src && avatarColors[colorIndex(name)],
    className,
  )
  if (src) {
    return <img src={src} alt={name} className={cls} />
  }
  return <span className={cls}>{initials(name)}</span>
}

/* ---------------- Toggle ---------------- */
export function Toggle({
  checked,
  onChange,
  label,
}: {
  checked: boolean
  onChange: (v: boolean) => void
  label?: string
}) {
  return (
    <button
      type="button"
      role="switch"
      aria-checked={checked}
      onClick={() => onChange(!checked)}
      className="inline-flex items-center gap-2"
    >
      {/* Track: inline-block supaya w/h selalu berlaku di context apa pun.
          Knob pakai `left` eksplisit (2px ↔ 18px), BUKAN translate, biar
          tidak bisa keluar dari track (track 36px, knob 16px → margin 2px). */}
      <span
        className={cn(
          'relative inline-block h-5 w-9 rounded-full transition-colors duration-200',
          checked ? 'bg-brand-600' : 'bg-slate-300',
        )}
      >
        <span
          className={cn(
            'absolute top-0.5 h-4 w-4 rounded-full bg-white shadow transition-all duration-200',
            checked ? 'left-[18px]' : 'left-0.5',
          )}
        />
      </span>
      {label && <span className="text-sm text-slate-700">{label}</span>}
    </button>
  )
}
