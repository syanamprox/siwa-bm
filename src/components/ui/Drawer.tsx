'use client'

import { useEffect, useState } from 'react'
import { createPortal } from 'react-dom'
import { cn } from '@/lib/utils'

/**
 * Slide-in panel dari kanan — portal ke body (bebas containing-block).
 */
export function Drawer({
  open,
  onClose,
  children,
  width = 'max-w-2xl',
}: {
  open: boolean
  onClose: () => void
  children: React.ReactNode
  width?: string
}) {
  const [mounted, setMounted] = useState(false)
  useEffect(() => setMounted(true), [])

  useEffect(() => {
    if (!open) return
    const handler = (e: KeyboardEvent) => { if (e.key === 'Escape') onClose() }
    window.addEventListener('keydown', handler)
    return () => window.removeEventListener('keydown', handler)
  }, [open, onClose])

  if (!mounted || !open) return null

  return createPortal(
    <div className="fixed inset-0 z-40">
      <div className="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onClick={onClose} />
      <div className={cn('absolute right-0 top-0 h-full w-full overflow-y-auto bg-surface shadow-pop animate-slide-in-right', width)}>
        {children}
      </div>
    </div>,
    document.body,
  )
}
