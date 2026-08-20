'use client'

import { CloudOff, RotateCw } from 'lucide-react'
import { Button } from '@/components/ui/primitives'

/**
 * Error state untuk data yang gagal dimuat — pasangan EmptyState.
 * Dipakai di branch `isError` (sebelum pengecekan data kosong) agar
 * fetch gagal tidak tampil menyesatkan sebagai "belum ada data".
 */
export function QueryError({
  message,
  onRetry,
  compact = false,
}: {
  message?: string
  onRetry?: () => void
  compact?: boolean
}) {
  return (
    <div
      className={`flex flex-col items-center justify-center gap-2.5 text-center ${compact ? 'py-6' : 'py-16'}`}
    >
      <div
        className={`flex items-center justify-center rounded-2xl bg-rose-50 text-rose-400 ${
          compact ? 'h-10 w-10' : 'h-14 w-14'
        }`}
      >
        <CloudOff size={compact ? 20 : 24} />
      </div>
      <p className="text-sm font-semibold text-slate-800">Gagal memuat data</p>
      {message && <p className="max-w-sm text-xs leading-relaxed text-slate-500">{message}</p>}
      {onRetry && (
        <Button variant="secondary" size="sm" className="mt-1.5" onClick={onRetry}>
          <RotateCw size={14} /> Coba Lagi
        </Button>
      )}
    </div>
  )
}
