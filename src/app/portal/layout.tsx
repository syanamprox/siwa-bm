import type { ReactNode } from 'react'
import Link from 'next/link'

export default function PortalLayout({ children }: { children: ReactNode }) {
  return (
    <div className="min-h-screen bg-canvas">
      <header className="border-b border-line bg-white/85 backdrop-blur-md">
        <div className="mx-auto flex h-16 max-w-5xl items-center justify-between px-6">
          <Link href="/portal" className="flex items-center gap-2.5">
            <span className="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-sm font-extrabold text-white">SW</span>
            <div>
              <p className="text-sm font-bold text-slate-900">Portal SIWA</p>
              <p className="text-[10px] text-slate-400">Sistem Informasi Warga</p>
            </div>
          </Link>
          <Link href="/login" className="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
            Login Petugas
          </Link>
        </div>
      </header>

      <main className="mx-auto max-w-3xl px-6 py-12">{children}</main>

      <footer className="border-t border-line py-6 text-center text-xs text-slate-400">
        © 2026 Kelurahan — SIWA · Portal Publik
      </footer>
    </div>
  )
}
