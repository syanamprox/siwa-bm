'use client'

import { Bell, LayoutGrid } from 'lucide-react'
import Link from 'next/link'
import { useAuth } from '@/stores/auth-store'

const ROLE_LABEL: Record<string, string> = {
  admin: 'Admin',
  lurah: 'Lurah',
  rw: 'Ketua RW',
  rt: 'Ketua RT',
}

export function Topbar() {
  const user = useAuth((s) => s.user)

  // Lurah/RW/RT: tampilkan kelurahan scope-nya · camat/admin: level kecamatan
  const scopedKelurahan = user?.wilayah?.kelurahan_nama?.replace(/^Kelurahan\s+/i, '')
  const wilayahLabel = scopedKelurahan ? `Kelurahan ${scopedKelurahan}` : 'Kecamatan Wonocolo'

  return (
    <header className="sticky top-0 z-20 flex h-16 flex-shrink-0 items-center justify-between border-b border-line bg-white/85 px-6 backdrop-blur-md">
      <div className="flex items-center gap-2.5">
        <span className="text-[15px] font-bold text-slate-900">
          {wilayahLabel}
        </span>
        {user?.role && (
          <span className="rounded-full bg-brand-50 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-brand-700">
            {ROLE_LABEL[user.role] ?? user.role}
          </span>
        )}
      </div>

      <div className="flex items-center gap-3">
        {/* dashboard launcher */}
        <Link
          href="/"
          className="flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
          title="Dashboard"
        >
          <LayoutGrid size={18} />
        </Link>

        {/* notifications */}
        <button
          className="relative flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
          title="Notifikasi"
        >
          <Bell size={18} />
        </button>
      </div>
    </header>
  )
}
