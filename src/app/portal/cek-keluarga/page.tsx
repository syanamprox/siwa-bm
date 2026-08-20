'use client'

import { useState } from 'react'
import Link from 'next/link'
import { Home, Loader2, Search, AlertCircle } from 'lucide-react'
import { api, ApiError } from '@/lib/api-client'
import { domisiliRingkas } from '@/lib/utils'
import { QueryError } from '@/components/QueryError'

interface PortalKeluarga {
  no_kk: string
  alamat: string | null
  rt: string | null
  rw: string | null
  kelurahan: string | null
  kepala_keluarga: string | null
  jumlah_anggota: number
  anggota: { nama: string; hubungan: string; jenis_kelamin: 'L' | 'P' }[]
}

export default function CekKeluargaPage() {
  const [noKk, setNoKk] = useState('')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null) // 404 — data tidak ditemukan
  const [serverError, setServerError] = useState<string | null>(null) // 429/5xx/network
  const [result, setResult] = useState<PortalKeluarga | null>(null)

  async function run() {
    setLoading(true)
    setError(null)
    setServerError(null)
    setResult(null)
    try {
      const res = await api.post<{ data: PortalKeluarga }>('/portal/cek-keluarga', { no_kk: noKk })
      setResult(res.data)
    } catch (err) {
      if (err instanceof ApiError && err.status === 404) setError(err.message)
      else if (err instanceof ApiError && err.status === 429) setServerError('Terlalu banyak permintaan. Coba lagi dalam 1 menit.')
      else setServerError(err instanceof ApiError ? err.message : 'Tidak dapat menghubungi server.')
    } finally {
      setLoading(false)
    }
  }

  async function submit(e: React.FormEvent) {
    e.preventDefault()
    run()
  }

  return (
    <div className="animate-fade-up">
      <Link href="/portal" className="text-[13px] font-medium text-slate-400 hover:text-slate-600">← Kembali</Link>
      <div className="mt-3 flex items-center gap-3">
        <span className="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600"><Home size={20} /></span>
        <div>
          <h1 className="text-2xl font-extrabold tracking-tight text-slate-900">Cek Kartu Keluarga</h1>
          <p className="text-sm text-slate-500">Masukkan 16 digit nomor KK Anda</p>
        </div>
      </div>

      <form onSubmit={submit} className="mt-6 flex gap-2">
        <input
          value={noKk}
          onChange={(e) => setNoKk(e.target.value.replace(/\D/g, '').slice(0, 16))}
          placeholder="16 digit nomor KK"
          inputMode="numeric"
          className="h-12 flex-1 rounded-xl border border-slate-200 bg-white px-4 font-mono text-sm tracking-widest shadow-sm placeholder:font-sans placeholder:tracking-normal focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/15"
        />
        <button type="submit" disabled={loading || noKk.length !== 16}
          className="flex h-12 items-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700 disabled:opacity-50">
          {loading ? <Loader2 size={16} className="animate-spin" /> : <Search size={16} />} Cek
        </button>
      </form>

      {error && (
        <div className="mt-4 flex items-start gap-2.5 rounded-xl bg-rose-50 p-4 text-sm text-rose-700 ring-1 ring-rose-600/10">
          <AlertCircle size={16} className="mt-0.5 shrink-0" /> {error}
        </div>
      )}

      {serverError && (
        <div className="mt-6 overflow-hidden rounded-2xl border border-line bg-white shadow-card">
          <QueryError message={serverError} onRetry={run} />
        </div>
      )}

      {result && (
        <div className="mt-6 overflow-hidden rounded-2xl border border-line bg-white shadow-card">
          <div className="border-b border-line bg-slate-50 px-6 py-4">
            <p className="font-mono text-sm font-bold text-slate-900">{result.no_kk}</p>
            <p className="mt-0.5 text-[13px] text-slate-500">
              Kepala: <strong className="text-slate-800">{result.kepala_keluarga ?? '—'}</strong> · {result.jumlah_anggota} anggota
            </p>
            <p className="text-xs text-slate-400">{domisiliRingkas(result.rt, result.rw, result.kelurahan)}{result.alamat ? ` — ${result.alamat}` : ''}</p>
          </div>
          <ul className="divide-y divide-line">
            {result.anggota.map((a, i) => (
              <li key={i} className="flex items-center gap-3 px-6 py-2.5">
                <span className={`flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-[11px] font-bold ${a.jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700'}`}>
                  {a.jenis_kelamin}
                </span>
                <span className="flex-1 text-[13px] font-medium text-slate-800">{a.nama}</span>
                <span className="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-500">{a.hubungan}</span>
              </li>
            ))}
          </ul>
        </div>
      )}
    </div>
  )
}
