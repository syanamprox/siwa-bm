import { BadgeCheck, CircleAlert } from 'lucide-react'

/**
 * Banner status verifikasi data untuk portal publik.
 * Variasi terverifikasi: hijau lembut dgn ikon tersegel; belum: amber dgn ikon peringatan.
 * Dipakai di cek-warga & cek-keluarga — satu sumber supaya konsisten.
 */
export function VerifiedBanner({ verified, label }: { verified: boolean; label?: string }) {
  const text = label ?? (verified ? 'Data terverifikasi' : 'Data belum diverifikasi')
  const desc = verified
    ? 'Identitas sudah dicek dan sah menurut catatan petugas.'
    : 'Data masih menunggu pemeriksaan petugas — bisa saja berubah.'

  return (
    <div
      role="status"
      className={`animate-fade-up mt-3 flex items-center gap-3 rounded-xl px-4 py-3 ring-1 ${
        verified
          ? 'bg-gradient-to-r from-emerald-50 to-teal-50/60 ring-emerald-600/15'
          : 'bg-gradient-to-r from-amber-50 to-orange-50/60 ring-amber-600/15'
      }`}
    >
      <span
        className={`flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white shadow-sm ${
          verified ? 'bg-emerald-500 shadow-emerald-500/25' : 'bg-amber-500 shadow-amber-500/25'
        }`}
      >
        {verified ? <BadgeCheck size={18} /> : <CircleAlert size={18} />}
      </span>
      <div className="min-w-0 leading-tight">
        <p className={`text-[13px] font-bold ${verified ? 'text-emerald-800' : 'text-amber-800'}`}>
          {text}
          <span className={`ml-1.5 rounded-full px-1.5 py-px text-[10px] font-semibold uppercase tracking-wide ${verified ? 'bg-emerald-600/10 text-emerald-700' : 'bg-amber-600/10 text-amber-700'}`}>
            {verified ? 'Terverifikasi' : 'Proses'}
          </span>
        </p>
        <p className={`mt-0.5 text-xs ${verified ? 'text-emerald-700/70' : 'text-amber-700/70'}`}>{desc}</p>
      </div>
    </div>
  )
}
