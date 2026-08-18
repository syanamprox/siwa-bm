'use client'

import { useState } from 'react'
import Link from 'next/link'
import { Coins, Loader2, Search, AlertCircle, CheckCircle2, Clock } from 'lucide-react'
import { api, ApiError } from '@/lib/api-client'

interface PortalIuran {
  nama: string
  nik: string
  ringkasan: { jumlah_tagihan: number; jumlah_tunggakan: number; total_tunggakan: number; jumlah_lunas: number }
  detail: { jenis: string; periode: string; nominal: number; status: string; jatuh_tempo: string | null; dibayar: number }[]
}

const STATUS_UI: Record<string, { label: string; cls: string }> = {
  lunas: { label: 'Lunas', cls: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' },
  belum_bayar: { label: 'Belum Bayar', cls: 'bg-amber-50 text-amber-700 ring-amber-600/20' },
  sebagian: { label: 'Sebagian', cls: 'bg-sky-50 text-sky-700 ring-sky-600/20' },
}

const rupiah = (n: number) => 'Rp ' + n.toLocaleString('id-ID')

export default function CekIuranPage() {
  const [nik, setNik] = useState('')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [result, setResult] = useState<PortalIuran | null>(null)

  async function submit(e: React.FormEvent) {
    e.preventDefault()
    setLoading(true)
    setError(null)
    setResult(null)
    try {
      const res = await api.post<{ data: PortalIuran }>('/portal/cek-iuran', { nik })
      setResult(res.data)
    } catch (err) {
      if (err instanceof ApiError && err.status === 429) setError('Terlalu banyak percobaan. Tunggu 1 menit.')
      else setError(err instanceof ApiError ? err.message : 'Terjadi kesalahan')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="animate-fade-up">
      <Link href="/portal" className="text-[13px] font-medium text-slate-400 hover:text-slate-600">← Kembali</Link>
      <div className="mt-3 flex items-center gap-3">
        <span className="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-600"><Coins size={20} /></span>
        <div>
          <h1 className="text-2xl font-extrabold tracking-tight text-slate-900">Cek Status Iuran</h1>
          <p className="text-sm text-slate-500">Masukkan NIK Anda — data keluarga akan dicari otomatis</p>
        </div>
      </div>

      <form onSubmit={submit} className="mt-6 flex gap-2">
        <input
          value={nik}
          onChange={(e) => setNik(e.target.value.replace(/\D/g, '').slice(0, 16))}
          placeholder="16 digit NIK"
          inputMode="numeric"
          className="h-12 flex-1 rounded-xl border border-slate-200 bg-white px-4 font-mono text-sm tracking-widest shadow-sm placeholder:font-sans placeholder:tracking-normal focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/15"
        />
        <button type="submit" disabled={loading || nik.length !== 16}
          className="flex h-12 items-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700 disabled:opacity-50">
          {loading ? <Loader2 size={16} className="animate-spin" /> : <Search size={16} />} Cek
        </button>
      </form>

      {error && (
        <div className="mt-4 flex items-start gap-2.5 rounded-xl bg-rose-50 p-4 text-sm text-rose-700 ring-1 ring-rose-600/10">
          <AlertCircle size={16} className="mt-0.5 shrink-0" /> {error}
        </div>
      )}

      {result && (
        <div className="mt-6 space-y-5">
          {/* Identity + summary */}
          <div className="rounded-2xl border border-line bg-white p-6 shadow-card">
            <div className="flex items-start justify-between">
              <div>
                <p className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Atas Nama</p>
                <p className="mt-1 text-lg font-bold text-slate-900">{result.nama}</p>
                <p className="font-mono text-xs text-slate-400">{result.nik}</p>
              </div>
              <span className={`inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold ring-1 ring-inset ${
                result.ringkasan.jumlah_tunggakan > 0 ? 'bg-amber-50 text-amber-700 ring-amber-600/20' : 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
              }`}>
                {result.ringkasan.jumlah_tunggakan > 0 ? <Clock size={13} /> : <CheckCircle2 size={13} />}
                {result.ringkasan.jumlah_tunggakan > 0 ? `${result.ringkasan.jumlah_tunggakan} tunggakan` : 'Tidak ada tunggakan'}
              </span>
            </div>

            <div className="mt-5 grid grid-cols-3 gap-3">
              <div className="rounded-xl bg-slate-50 p-3 text-center">
                <p className="text-xl font-extrabold tabular-nums text-slate-900">{result.ringkasan.jumlah_tagihan}</p>
                <p className="text-[11px] font-medium text-slate-500">tagihan setahun</p>
              </div>
              <div className="rounded-xl bg-amber-50 p-3 text-center">
                <p className="text-xl font-extrabold tabular-nums text-amber-700">{rupiah(result.ringkasan.total_tunggakan)}</p>
                <p className="text-[11px] font-medium text-amber-600">total tunggakan</p>
              </div>
              <div className="rounded-xl bg-emerald-50 p-3 text-center">
                <p className="text-xl font-extrabold tabular-nums text-emerald-700">{result.ringkasan.jumlah_lunas}</p>
                <p className="text-[11px] font-medium text-emerald-600">sudah lunas</p>
              </div>
            </div>
          </div>

          {/* Detail table */}
          <div className="overflow-hidden rounded-2xl border border-line bg-white shadow-card">
            <div className="border-b border-line px-5 py-3.5 text-sm font-bold text-slate-900">Rincian Terbaru</div>
            <table className="w-full text-[13px]">
              <tbody className="divide-y divide-line">
                {result.detail.map((d, i) => (
                  <tr key={i}>
                    <td className="px-5 py-3">
                      <p className="font-semibold text-slate-800">{d.jenis}</p>
                      <p className="text-[11px] text-slate-400">{d.periode}{d.jatuh_tempo ? ` · jatuh tempo ${d.jatuh_tempo}` : ''}</p>
                    </td>
                    <td className="px-5 py-3 text-right font-bold tabular-nums text-slate-900">{rupiah(d.nominal)}</td>
                    <td className="px-5 py-3">
                      <span className={`inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold ring-1 ring-inset ${STATUS_UI[d.status]?.cls ?? 'bg-slate-100 text-slate-600'}`}>
                        {STATUS_UI[d.status]?.label ?? d.status}
                      </span>
                    </td>
                  </tr>
                ))}
                {result.detail.length === 0 && (
                  <tr><td colSpan={3} className="px-5 py-8 text-center text-sm text-slate-400">Belum ada tagihan</td></tr>
                )}
              </tbody>
            </table>
          </div>

          <p className="text-center text-xs text-slate-400">
            Ada ketidaksesuaian? Hubungi RT setempat atau petugas kelurahan.
          </p>
        </div>
      )}
    </div>
  )
}
