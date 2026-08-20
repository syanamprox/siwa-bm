'use client'

import { useState } from 'react'
import Link from 'next/link'
import { Users, Loader2, Search, AlertCircle } from 'lucide-react'
import { api, ApiError } from '@/lib/api-client'
import { domisiliRingkas } from '@/lib/utils'
import { QueryError } from '@/components/QueryError'

interface PortalWarga {
  nama_lengkap: string
  nik: string
  tempat_lahir: string | null
  tanggal_lahir: string | null
  jenis_kelamin: 'L' | 'P'
  agama: string
  status_perkawinan: string
  pekerjaan: string
  hubungan_keluarga: string
  no_telepon: string | null
  keluarga: { no_kk: string; alamat: string; rt: string | null; rw: string | null; kelurahan: string | null; status_domisili: string } | null
}

export default function CekWargaPage() {
  const [search, setSearch] = useState('')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null) // 404 — data tidak ditemukan
  const [serverError, setServerError] = useState<string | null>(null) // 429/5xx/network
  const [result, setResult] = useState<PortalWarga | null>(null)

  async function run() {
    setLoading(true)
    setError(null)
    setServerError(null)
    setResult(null)
    try {
      const res = await api.post<{ data: PortalWarga }>('/portal/cek-warga', { search })
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
        <span className="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"><Users size={20} /></span>
        <div>
          <h1 className="text-2xl font-extrabold tracking-tight text-slate-900">Cek Data Warga</h1>
          <p className="text-sm text-slate-500">Masukkan NIK (16 digit) atau nama lengkap Anda</p>
        </div>
      </div>

      <form onSubmit={submit} className="mt-6 flex gap-2">
        <input
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          placeholder="mis. 3578… atau Nama Lengkap"
          className="h-12 flex-1 rounded-xl border border-slate-200 bg-white px-4 text-sm shadow-sm placeholder:text-slate-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/15"
        />
        <button type="submit" disabled={loading || search.length < 3}
          className="flex h-12 items-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700 disabled:opacity-50">
          {loading ? <Loader2 size={16} className="animate-spin" /> : <Search size={16} />} Cari
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
            <p className="text-lg font-bold text-slate-900">{result.nama_lengkap}</p>
            <p className="font-mono text-xs text-slate-400">{result.nik} · {result.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</p>
          </div>
          <dl className="divide-y divide-line text-[13px]">
            {[
              ['Tempat / Tgl Lahir', `${result.tempat_lahir ?? '—'}, ${result.tanggal_lahir ?? '—'}`],
              ['Agama', result.agama],
              ['Status Perkawinan', result.status_perkawinan],
              ['Pekerjaan', result.pekerjaan],
              ['Hubungan Keluarga', result.hubungan_keluarga],
              ['Telepon', result.no_telepon ?? '—'],
              ['Kartu Keluarga', result.keluarga ? `${result.keluarga.no_kk} (${result.keluarga.status_domisili})` : 'Belum terdaftar'],
              ['Domisili', result.keluarga ? domisiliRingkas(result.keluarga.rt, result.keluarga.rw, result.keluarga.kelurahan) : '—'],
              ['Alamat', result.keluarga?.alamat ?? '—'],
            ].map(([label, value]) => (
              <div key={label} className="flex gap-4 px-6 py-2.5">
                <dt className="w-40 shrink-0 text-slate-400">{label}</dt>
                <dd className="font-medium text-slate-800">{value}</dd>
              </div>
            ))}
          </dl>
        </div>
      )}
    </div>
  )
}
