import { clsx, type ClassValue } from 'clsx'
import { twMerge } from 'tailwind-merge'

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

/* ── Number / currency formatters ── */

const NF0 = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 })
const NF2 = new Intl.NumberFormat('id-ID', {
  minimumFractionDigits: 2,
  maximumFractionDigits: 2,
})

export function fmtNum(n: number | undefined | null, dec = 0): string {
  if (n == null || Number.isNaN(n)) return '—'
  const abs = Math.abs(n)
  const formatted = dec === 0 ? NF0.format(abs) : NF2.format(abs)
  return n < 0 ? `(${formatted})` : formatted
}

export function fmtCompact(n: number | undefined | null): string {
  if (n == null || Number.isNaN(n)) return '—'
  const abs = Math.abs(n)
  let formatted: string
  if (abs >= 1_000_000_000) formatted = (abs / 1_000_000_000).toFixed(1) + 'B'
  else if (abs >= 1_000_000) formatted = (abs / 1_000_000).toFixed(1) + 'M'
  else if (abs >= 1_000) formatted = (abs / 1_000).toFixed(1) + 'K'
  else formatted = NF0.format(abs)
  return n < 0 ? `(${formatted})` : formatted
}

const symbolFor: Record<string, string> = {
  IDR: 'Rp',
  USD: '$',
  EUR: '€',
  GBP: '£',
  SGD: 'S$',
  MYR: 'RM',
  AUD: 'A$',
}

export function curSymbol(currency?: string): string {
  if (!currency) return 'Rp'
  return symbolFor[currency] ?? currency + ' '
}

export function fmtMoney(n: number | undefined | null, currency = 'IDR'): string {
  if (n == null || Number.isNaN(n)) return '—'
  const sym = curSymbol(currency)
  const isIDR = currency === 'IDR'
  const abs = Math.abs(n)
  const formatted = isIDR ? NF0.format(abs) : NF2.format(abs)
  return n < 0 ? `${sym} (${formatted})` : `${sym} ${formatted}`
}

export function fmtPct(n: number | undefined | null, dec = 2): string {
  if (n == null || Number.isNaN(n)) return '—'
  return n.toFixed(dec) + '%'
}

/* ── Date formatters ── */

export function fmtDate(d?: string | Date | null): string {
  if (!d) return '—'
  const dt = typeof d === 'string' ? new Date(d) : d
  if (Number.isNaN(dt.getTime())) return '—'
  return dt.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

export function fmtDateTime(d?: string | Date | null): string {
  if (!d) return '—'
  const dt = typeof d === 'string' ? new Date(d) : d
  if (Number.isNaN(dt.getTime())) return '—'
  return dt.toLocaleString('id-ID', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

export function ymd(d: Date): string {
  return d.toISOString().slice(0, 10)
}

/**
 * Path domisili ringkas dari nama wilayah lengkap.
 * "RT 02 RW 03 Bendul Merisi" + "RW 03 Bendul Merisi" + "Kelurahan Bendul Merisi"
 * → "RT 02 · RW 03 · Kel. Bendul Merisi" (tanpa redundansi nama kelurahan).
 */
export function domisiliRingkas(rt?: string | null, rw?: string | null, kelurahan?: string | null): string {
  const m = (rt ?? '').match(/^RT\s+(\d+)\s+RW\s+(\d+)\s+(.+)$/)
  if (m) {
    const kel = (kelurahan ?? m[3]).replace(/^Kelurahan\s+/i, '')
    return `RT ${m[1]} · RW ${m[2]} · Kel. ${kel}`
  }
  return [rt, rw, kelurahan].filter(Boolean).join(' · ')
}

export function daysAgo(n: number): Date {
  const d = new Date()
  d.setDate(d.getDate() - n)
  return d
}

/** deterministic fallback accent color from an id. */
const PALETTE = ['#4dabf7', '#34e8a0', '#f5c842', '#ff6b7a', '#a78bfa', '#fb923c', '#22d3ee']
export function colorFor(id: string, fallback?: string): string {
  if (fallback && /^#?[0-9a-fA-F]{3,8}$/.test(fallback)) {
    return fallback.startsWith('#') ? fallback : '#' + fallback
  }
  let h = 0
  for (let i = 0; i < id.length; i++) h = (h * 31 + id.charCodeAt(i)) >>> 0
  return PALETTE[h % PALETTE.length]
}

/* ── Honorific (Bpk./Bu) prefix for Contact Person ── */

/**
 * Prefix a person's name with honorific based on gender.
 * - gender 'L' → "Bpk. {name}"
 * - gender 'P' → "Bu {name}"
 * - null/undefined/other → returns name as-is
 *
 * Returns null when name is empty/null (so caller can fallback).
 */
export function withHonorific(
  name: string | null | undefined,
  gender: 'L' | 'P' | null | undefined,
): string | null {
  if (!name || !name.trim()) return null
  const trimmed = name.trim()
  if (gender === 'L') return `Bpk. ${trimmed}`
  if (gender === 'P') return `Bu ${trimmed}`
  return trimmed
}
