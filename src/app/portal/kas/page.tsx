'use client'

import { useMemo, useState } from 'react'
import Link from 'next/link'
import { ArrowLeft, Wallet, ArrowDownToLine, ArrowUpFromLine, Landmark, ShieldCheck } from 'lucide-react'
import { usePortalKasUnits, usePortalKasSummary, type KasUnitItem } from '@/hooks/use-kas'
import { QueryError } from '@/components/QueryError'
import { Skeleton } from '@/components/ui/primitives'

const rupiah = (n: number) => 'Rp ' + n.toLocaleString('id-ID')

const KAT_COLOR: Record<string, string> = {
  Iuran: 'bg-brand-50 text-brand-700',
  Infaq: 'bg-emerald-50 text-emerald-700',
  Donasi: 'bg-teal-50 text-teal-700',
  'Saldo Awal': 'bg-slate-100 text-slate-600',
  'Lain-lain': 'bg-slate-100 text-slate-600',
  Operasional: 'bg-rose-50 text-rose-700',
  Rapat: 'bg-amber-50 text-amber-700',
  Perlengkapan: 'bg-purple-50 text-purple-700',
  Kesehatan: 'bg-emerald-50 text-emerald-700',
  Kegiatan: 'bg-sky-50 text-sky-700',
}

const LEVEL_LABEL: Record<string, string> = { rt: 'RT', rw: 'RW', kelurahan: 'Kelurahan', kecamatan: 'Kecamatan', organisasi: 'Organisasi' }
const ym = (d: Date) => d.toISOString().slice(0, 7)

const selCls = 'h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/15'

/** "RT 02 RW 03 Bendul Merisi" → "RT 02" · "RW 03 Bendul Merisi" → "RW 03" */
const short = (nama: string) => nama.replace(/^(RT\s+\d+)\s+RW\s+\d+\s+.*$/, '$1').replace(/^(RW\s+\d+)\s+.*$/, '$1').replace(/^Kelurahan\s+/, '')
const KEL_SHORT = (nama?: string | null) => (nama ? nama.replace(/^Kelurahan\s+/, '') : '')

export default function KasPublikPage() {
  const [jenis, setJenis] = useState<KasUnitItem['jenis']>('rt')
  const [kelKey, setKelKey] = useState<string>('')
  const [rwKey, setRwKey] = useState<string>('')
  const [unitId, setUnitId] = useState<number | null>(null)
  const [bulan, setBulan] = useState(ym(new Date()))

  const { data: unitsData, isLoading: loadingUnits, isError: errUnits, error: errUnitsObj, refetch: refetchUnits } = usePortalKasUnits()
  const units = useMemo(() => unitsData?.data ?? [], [unitsData])

  const jenisList = [...new Set(units.map((u) => u.jenis))] as KasUnitItem['jenis'][]

  // cascade data per jenis
  const kelurahanOptions = useMemo(
    () => [...new Set(units.filter((u) => u.kelurahan_nama).map((u) => u.kelurahan_nama!))].sort(),
    [units],
  )
  const activeKel = kelKey && kelurahanOptions.includes(kelKey) ? kelKey : kelurahanOptions[0] ?? ''

  const rwOptions = useMemo(
    () => [...new Set(units.filter((u) => u.jenis === 'rw' && u.kelurahan_nama === activeKel).map((u) => u.rw_nama ?? u.nama))].sort(),
    [units, activeKel],
  )
  const activeRw = rwKey && rwOptions.includes(rwKey) ? rwKey : rwOptions[0] ?? ''

  const rtOptions = useMemo(
    () => units.filter((u) => u.jenis === 'rt' && u.kelurahan_nama === activeKel && u.rw_nama === activeRw).sort((a, b) => a.nama.localeCompare(b.nama)),
    [units, activeKel, activeRw],
  )

  const orgOptions = useMemo(() => units.filter((u) => u.jenis === 'organisasi'), [units])
  const kelUnits = useMemo(() => units.filter((u) => u.jenis === 'kelurahan' && u.kelurahan_nama === activeKel), [units, activeKel])
  const kecUnits = useMemo(() => units.filter((u) => u.jenis === 'kecamatan'), [units])

  // unit aktif per jenis (auto-pick pertama)
  const listFor = (j: KasUnitItem['jenis']): KasUnitItem[] =>
    j === 'rt' ? rtOptions : j === 'rw'
      ? units.filter((u) => u.jenis === 'rw' && u.kelurahan_nama === activeKel && (u.rw_nama ?? u.nama) === activeRw)
      : j === 'kelurahan' ? kelUnits : j === 'kecamatan' ? kecUnits : orgOptions

  const current = listFor(jenis)
  const activeUnit = unitId && current.some((u) => u.id === unitId) ? current.find((u) => u.id === unitId)! : current[0] ?? null

  const { data: sumData, isLoading: loadingSum, isError: errSum, error: errSumObj, refetch: refetchSum } = usePortalKasSummary(activeUnit?.id ?? null, bulan)
  const s = sumData?.data

  const btn = (active: boolean) =>
    `rounded-xl border px-3 py-2.5 text-[13px] font-bold transition ${
      active ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-slate-200 bg-white text-slate-500 hover:border-brand-300'
    }`

  const tren = s?.tren ?? []
  const maxTren = Math.max(1, ...tren.flatMap((t) => [t.masuk, t.keluar]))
  const bulanLabel = (key: string) => {
    const [, m] = key.split('-')
    return ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'][Number(m) - 1] ?? key
  }

  return (
    <div>
      <Link href="/portal" className="inline-flex items-center gap-1.5 text-[13px] font-semibold text-slate-500 transition hover:text-brand-600">
        <ArrowLeft size={14} /> Kembali
      </Link>

      <div className="mt-4">
        <h1 className="text-2xl font-extrabold tracking-tight text-slate-900">Transparansi Kas</h1>
        <p className="mt-1 text-sm text-slate-500">Laporan keuangan wilayah &amp; organisasi — terbuka untuk semua warga</p>
      </div>

      {/* Level */}
      {loadingUnits ? (
        <Skeleton className="mt-6 h-11" />
      ) : errUnits ? (
        <div className="mt-6"><QueryError message={errUnitsObj?.message} onRetry={() => refetchUnits()} /></div>
      ) : (
        <div className="mt-6 space-y-2.5">
          <div className="flex flex-wrap gap-2">
            {jenisList.map((j) => (
              <button key={j} onClick={() => { setJenis(j); setUnitId(null) }} className={btn(jenis === j)}>
                {LEVEL_LABEL[j] ?? j}
              </button>
            ))}
          </div>

          {/* Cascade selector */}
          <div className="grid grid-cols-2 gap-2 sm:grid-cols-4">
            {jenis === 'rt' && (
              <>
                <select value={activeKel} onChange={(e) => { setKelKey(e.target.value); setRwKey(''); setUnitId(null) }} className={selCls}>
                  {kelurahanOptions.map((k) => <option key={k} value={k}>{KEL_SHORT(k)}</option>)}
                </select>
                <select value={activeRw} onChange={(e) => { setRwKey(e.target.value); setUnitId(null) }} className={selCls}>
                  {rwOptions.map((r) => <option key={r} value={r}>{short(r)}</option>)}
                </select>
                <select value={activeUnit?.id ?? ''} onChange={(e) => setUnitId(Number(e.target.value))} className={selCls}>
                  {rtOptions.map((u) => <option key={u.id} value={u.id}>{short(u.nama)}</option>)}
                </select>
              </>
            )}
            {jenis === 'rw' && (
              <>
                <select value={activeKel} onChange={(e) => { setKelKey(e.target.value); setRwKey(''); setUnitId(null) }} className={selCls}>
                  {kelurahanOptions.map((k) => <option key={k} value={k}>{KEL_SHORT(k)}</option>)}
                </select>
                <select value={activeRw} onChange={(e) => { setRwKey(e.target.value); setUnitId(null) }} className={selCls}>
                  {rwOptions.map((r) => <option key={r} value={r}>{short(r)}</option>)}
                </select>
              </>
            )}
            {jenis === 'kelurahan' && (
              <select value={activeKel} onChange={(e) => { setKelKey(e.target.value); setUnitId(null) }} className={selCls}>
                {kelurahanOptions.map((k) => <option key={k} value={k}>{KEL_SHORT(k)}</option>)}
              </select>
            )}
            {jenis === 'organisasi' && (
              <select value={activeUnit?.id ?? ''} onChange={(e) => setUnitId(Number(e.target.value))} className={`${selCls} col-span-2 sm:col-span-2`}>
                {orgOptions.map((u) => <option key={u.id} value={u.id}>{u.nama} · {u.parent_label?.replace('under ', '')}</option>)}
              </select>
            )}
            <input type="month" value={bulan} onChange={(e) => setBulan(e.target.value || ym(new Date()))} className={selCls} />
          </div>
          {jenis === 'kecamatan' && <p className="text-[13px] text-slate-500">Kecamatan Wonocolo — agregat kecamatan.</p>}
        </div>
      )}

      {/* Summary */}
      {!activeUnit ? (
        <p className="mt-8 text-center text-sm text-slate-400">Pilih unit kas di atas untuk melihat laporan.</p>
      ) : loadingSum ? (
        <div className="mt-4 space-y-4">
          <Skeleton className="h-44" />
          <Skeleton className="h-64" />
        </div>
      ) : errSum ? (
        <div className="mt-4"><QueryError message={errSumObj?.message} onRetry={() => refetchSum()} /></div>
      ) : s && (
        <>
          <div className="mt-4 overflow-hidden rounded-2xl border border-line bg-white shadow-card">
            <div className="bg-gradient-to-br from-brand-600 to-brand-800 p-6 text-white">
              <p className="text-[11px] font-bold uppercase tracking-wider text-brand-200">{s.periode_label}</p>
              <p className="mt-1 text-[15px] font-bold">
                {s.unit.nama}
                {s.unit.parent_label && <span className="ml-2 text-[12px] font-medium text-brand-200">{s.unit.parent_label.replace('under ', '· ')}</span>}
              </p>
              <div className="mt-4">
                <p className="flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-brand-200"><Landmark size={12} /> Saldo Saat Ini</p>
                <p className="mt-1 text-3xl font-extrabold tabular-nums">{rupiah(s.saldo_akhir)}</p>
              </div>
            </div>

            <div className="grid grid-cols-3 divide-x divide-line border-b border-line">
              <div className="p-4 text-center">
                <p className="flex items-center justify-center gap-1 text-[10px] font-bold uppercase tracking-wider text-slate-400"><Wallet size={11} /> Saldo Awal</p>
                <p className="mt-1.5 text-[15px] font-extrabold tabular-nums text-slate-800">{rupiah(s.saldo_awal)}</p>
              </div>
              <div className="p-4 text-center">
                <p className="flex items-center justify-center gap-1 text-[10px] font-bold uppercase tracking-wider text-emerald-600"><ArrowDownToLine size={11} /> Pemasukan</p>
                <p className="mt-1.5 text-[15px] font-extrabold tabular-nums text-emerald-700">{rupiah(s.pemasukan_iuran + s.pemasukan_lain)}</p>
                <p className="text-[10px] text-slate-400">iuran {rupiah(s.pemasukan_iuran)} · lain {rupiah(s.pemasukan_lain)}</p>
              </div>
              <div className="p-4 text-center">
                <p className="flex items-center justify-center gap-1 text-[10px] font-bold uppercase tracking-wider text-rose-600"><ArrowUpFromLine size={11} /> Pengeluaran</p>
                <p className="mt-1.5 text-[15px] font-extrabold tabular-nums text-rose-700">{rupiah(s.pengeluaran)}</p>
              </div>
            </div>

            {tren.length > 0 && (
              <div className="p-5">
                <p className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tren 3 Bulan</p>
                <div className="mt-3 flex items-end justify-around gap-4">
                  {tren.map((t) => (
                    <div key={t.bulan} className="flex flex-1 flex-col items-center gap-1.5">
                      <div className="flex h-20 w-full items-end justify-center gap-1.5">
                        <div className="w-7 rounded-t-md bg-emerald-400/80" style={{ height: `${(t.masuk / maxTren) * 100}%` }} title={`Masuk ${rupiah(t.masuk)}`} />
                        <div className="w-7 rounded-t-md bg-rose-400/80" style={{ height: `${(t.keluar / maxTren) * 100}%` }} title={`Keluar ${rupiah(t.keluar)}`} />
                      </div>
                      <p className="text-[11px] font-semibold text-slate-500">{bulanLabel(t.bulan)}</p>
                    </div>
                  ))}
                </div>
                <div className="mt-3 flex justify-center gap-4 text-[11px] text-slate-400">
                  <span className="inline-flex items-center gap-1"><span className="h-2 w-2 rounded-full bg-emerald-400" /> Pemasukan</span>
                  <span className="inline-flex items-center gap-1"><span className="h-2 w-2 rounded-full bg-rose-400" /> Pengeluaran</span>
                </div>
              </div>
            )}
          </div>

          {/* Buku kas */}
          <div className="mt-4 overflow-hidden rounded-2xl border border-line bg-white shadow-card">
            <div className="border-b border-line px-5 py-3.5 text-sm font-bold text-slate-900">
              Rincian Transaksi
              <span className="ml-2 text-[11px] font-medium text-slate-400">{s.tx.length} entri · {s.periode_label}</span>
            </div>
            {s.tx.length === 0 && s.saldo_awal === 0 && s.saldo_akhir === 0 ? (
              <div className="px-5 py-8 text-center">
                <p className="text-sm text-slate-400">
                  {s.unit.jenis === 'rt'
                    ? 'Belum ada laporan kas RT ini. Iuran yang dibayarkan petugas akan tercatat otomatis di sini.'
                    : s.unit.jenis === 'organisasi'
                      ? 'Belum ada laporan kas organisasi ini.'
                      : 'Belum ada laporan kas. Petugas wilayah belum mencatat setoran/saldo awal.'}
                </p>
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full text-[13px]">
                  <thead>
                    <tr className="border-b border-line bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-400">
                      <th className="px-5 py-2.5 font-semibold">Tgl</th>
                      <th className="px-5 py-2.5 font-semibold">Keterangan</th>
                      <th className="px-5 py-2.5 font-semibold">Kategori</th>
                      <th className="px-5 py-2.5 text-right font-semibold">Masuk</th>
                      <th className="px-5 py-2.5 text-right font-semibold">Keluar</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-line">
                    {s.tx.map((t) => (
                      <tr key={t.id} className="hover:bg-slate-50">
                        <td className="px-5 py-3 whitespace-nowrap tabular-nums text-slate-500">{t.tgl}</td>
                        <td className="px-5 py-3 text-slate-800">{t.ket ?? t.kat}</td>
                        <td className="px-5 py-3">
                          <span className={`rounded-full px-2 py-0.5 text-[11px] font-semibold ${KAT_COLOR[t.kat] ?? 'bg-slate-100 text-slate-600'}`}>{t.kat}</span>
                        </td>
                        <td className="px-5 py-3 text-right font-semibold tabular-nums text-emerald-700">{t.masuk ? rupiah(t.masuk) : '—'}</td>
                        <td className="px-5 py-3 text-right font-semibold tabular-nums text-rose-700">{t.keluar ? rupiah(t.keluar) : '—'}</td>
                      </tr>
                    ))}
                    <tr className="bg-brand-50 font-extrabold text-brand-800">
                      <td colSpan={4} className="px-5 py-3 text-right">Saldo per akhir periode</td>
                      <td className="px-5 py-3 text-right tabular-nums">{rupiah(s.saldo_akhir)}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            )}
          </div>
        </>
      )}

      <p className="mt-5 flex items-center justify-center gap-1.5 text-center text-xs text-slate-400">
        <ShieldCheck size={13} /> Unit kas wilayah &amp; organisasi terdaftar · data agregat tanpa informasi pribadi.
      </p>
    </div>
  )
}
