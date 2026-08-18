import Link from 'next/link'
import { Coins, Users, Home, ShieldCheck, Lock, Zap } from 'lucide-react'

const SERVICES = [
  {
    href: '/portal/cek-iuran',
    icon: Coins,
    title: 'Cek Iuran',
    desc: 'Lihat status tagihan & tunggakan iuran keluarga Anda 12 bulan terakhir.',
    accent: 'bg-amber-50 text-amber-600',
  },
  {
    href: '/portal/cek-warga',
    icon: Users,
    title: 'Cek Data Warga',
    desc: 'Verifikasi data kependudukan Anda (NIK atau nama lengkap).',
    accent: 'bg-blue-50 text-blue-600',
  },
  {
    href: '/portal/cek-keluarga',
    icon: Home,
    title: 'Cek Kartu Keluarga',
    desc: 'Lihat komposisi anggota keluarga berdasarkan nomor KK.',
    accent: 'bg-emerald-50 text-emerald-600',
  },
]

export default function PortalPage() {
  return (
    <div>
      {/* Hero */}
      <div className="animate-fade-up text-center">
        <span className="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-brand-700">
          <ShieldCheck size={12} /> Layanan Publik
        </span>
        <h1 className="mt-4 text-4xl font-extrabold tracking-tight text-slate-900">
          Cek data Anda,
          <span className="bg-gradient-to-r from-brand-600 to-purple-500 bg-clip-text text-transparent"> tanpa antre.</span>
        </h1>
        <p className="mx-auto mt-3 max-w-lg text-[15px] leading-relaxed text-slate-500">
          Akses informasi kependudukan dan status iuran kapan saja.
          Data sensitif otomatis disensor untuk melindungi privasi Anda.
        </p>
      </div>

      {/* Service cards */}
      <div className="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-3">
        {SERVICES.map((s, i) => (
          <Link key={s.href} href={s.href} className="group animate-fade-up" style={{ animationDelay: `${0.1 + i * 0.08}s` }}>
            <div className="card-hover flex h-full flex-col rounded-2xl border border-line bg-white p-6 shadow-card">
              <span className={`flex h-12 w-12 items-center justify-center rounded-2xl ${s.accent}`}>
                <s.icon size={22} />
              </span>
              <h2 className="mt-4 text-[15px] font-bold text-slate-900">{s.title}</h2>
              <p className="mt-1.5 flex-1 text-[13px] leading-relaxed text-slate-500">{s.desc}</p>
              <span className="mt-4 text-[13px] font-semibold text-brand-600 transition-transform group-hover:translate-x-0.5">
                Buka →
              </span>
            </div>
          </Link>
        ))}
      </div>

      {/* Trust badges */}
      <div className="mt-12 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-xs text-slate-400">
        <span className="inline-flex items-center gap-1.5"><Lock size={13} /> Data tersensor otomatis</span>
        <span className="inline-flex items-center gap-1.5"><ShieldCheck size={13} /> Tanpa login, tanpa data pribadi disimpan</span>
        <span className="inline-flex items-center gap-1.5"><Zap size={13} /> Hasil instan</span>
      </div>
    </div>
  )
}
