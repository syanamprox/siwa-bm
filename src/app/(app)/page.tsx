'use client'

import Link from 'next/link'
import { ArrowRight } from 'lucide-react'
import { useAuth } from '@/stores/auth-store'
import { modulesForRole } from '@/lib/constants'
import { PageHeader } from '@/components/PageHeader'
import { Card } from '@/components/ui/primitives'

const ROLE_LABEL: Record<string, string> = {
  admin: 'Administrator',
  lurah: 'Lurah',
  rw: 'Ketua RW',
  rt: 'Ketua RT',
}

function greeting(): string {
  const h = new Date().getHours()
  if (h < 11) return 'Selamat pagi'
  if (h < 15) return 'Selamat siang'
  if (h < 19) return 'Selamat sore'
  return 'Selamat malam'
}

export default function DashboardPage() {
  const user = useAuth((s) => s.user)
  const modules = modulesForRole(user?.role)

  const firstName = (user?.name ?? 'User').split(' ')[0]
  const today = new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })

  return (
    <div className="animate-fade-up">
      <PageHeader
        title={`${greeting()}, ${firstName}`}
        subtitle={`${ROLE_LABEL[user?.role ?? ''] ?? user?.role} · ${today}${
          user?.wilayah ? ` · ${user.wilayah.nama}` : ''
        }`}
      />

      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {modules.map((m) => (
          <Link key={m.key} href={m.href}>
            <Card className="card-hover group p-6">
              <div className="flex items-center gap-4">
                <span className="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                  <m.icon size={22} />
                </span>
                <div className="flex-1">
                  <h3 className="text-[15px] font-bold text-slate-900">{m.label}</h3>
                  <p className="text-xs text-slate-500">
                    {m.key === 'kependudukan' && 'Data warga & kartu keluarga'}
                    {m.key === 'iuran' && 'Tagihan, pembayaran, tunggakan'}
                    {m.key === 'wilayah' && 'Struktur Kelurahan → RW → RT'}
                    {m.key === 'laporan' && 'Laporan kependudukan & wilayah'}
                    {m.key === 'admin' && 'Pengguna, pengaturan, backup'}
                  </p>
                </div>
                <ArrowRight
                  size={18}
                  className="text-slate-300 transition-all group-hover:translate-x-1 group-hover:text-brand-600"
                />
              </div>
            </Card>
          </Link>
        ))}
      </div>

      <Card className="mt-6 p-6">
        <p className="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
          Status Rework
        </p>
        <p className="mt-2 text-sm text-slate-600">
          Phase 0 — Foundation selesai. Modul akan aktif bertahap:
          P1 Kependudukan → P2 Iuran → P3 Admin & Laporan → P4 Portal Publik.
        </p>
      </Card>
    </div>
  )
}
