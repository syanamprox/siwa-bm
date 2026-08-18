import Link from 'next/link'
import { FileBarChart, Map, ArrowRight } from 'lucide-react'

const REPORTS = [
  {
    href: '/laporan/kependudukan',
    icon: FileBarChart,
    title: 'Laporan Kependudukan',
    desc: 'Rekap total KK, laki-laki/perempuan, dan total warga per RT dalam scope wilayah Anda. Termasuk export CSV.',
    accent: 'bg-blue-50 text-blue-600',
  },
  {
    href: '/laporan/wilayah',
    icon: Map,
    title: 'Laporan Wilayah',
    desc: 'Struktur Kelurahan → RW → RT beserta sebaran kartu keluarga per RT.',
    accent: 'bg-emerald-50 text-emerald-600',
  },
]

export default function LaporanIndexPage() {
  return (
    <div className="animate-fade-up">
      <div className="mb-5">
        <h1 className="text-[22px] font-extrabold tracking-tight text-slate-900">Laporan</h1>
        <p className="mt-1 text-sm text-slate-500">Pilih jenis laporan</p>
      </div>

      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
        {REPORTS.map((r) => (
          <Link key={r.href} href={r.href} className="group">
            <div className="card-hover flex h-full flex-col rounded-2xl border border-line bg-white p-6 shadow-card">
              <span className={`flex h-12 w-12 items-center justify-center rounded-2xl ${r.accent}`}>
                <r.icon size={22} />
              </span>
              <h2 className="mt-4 text-[15px] font-bold text-slate-900">{r.title}</h2>
              <p className="mt-1.5 flex-1 text-[13px] leading-relaxed text-slate-500">{r.desc}</p>
              <span className="mt-4 inline-flex items-center gap-1 text-[13px] font-semibold text-brand-600">
                Buka <ArrowRight size={14} className="transition-transform group-hover:translate-x-0.5" />
              </span>
            </div>
          </Link>
        ))}
      </div>
    </div>
  )
}
