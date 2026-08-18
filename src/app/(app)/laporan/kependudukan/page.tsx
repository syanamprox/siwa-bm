'use client'

import { useMemo } from 'react'
import { FileBarChart, Users, Home } from 'lucide-react'
import { useQuery } from '@tanstack/react-query'
import { api } from '@/lib/api-client'
import { PageHeader } from '@/components/PageHeader'
import { KpiCard } from '@/components/KpiCard'
import { Card, Skeleton } from '@/components/ui/primitives'
import type { LaporanKependudukan, WilayahRef } from '@/types'

export default function LaporanKependudukanPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['laporan-kependudukan'],
    queryFn: () => api.get<{ data: LaporanKependudukan }>('/laporan/kependudukan'),
  })

  const rows = data?.data?.rows ?? []
  const totals = data?.data?.totals

  // Group by RW untuk visual per-RW
  const perRw = useMemo(() => {
    const m = new Map<string, { kk: number; warga: number }>()
    rows.forEach((r) => {
      const cur = m.get(r.rw) ?? { kk: 0, warga: 0 }
      m.set(r.rw, { kk: cur.kk + r.total_kk, warga: cur.warga + r.total_warga })
    })
    return [...m.entries()]
  }, [rows])

  function exportCsv() {
    const header = 'RT,RW,Total KK,Laki-laki,Perempuan,Total Warga\n'
    const body = rows.map((r) => `"${r.rt}","${r.rw}",${r.total_kk},${r.laki},${r.perempuan},${r.total_warga}`).join('\n')
    const blob = new Blob([header + body], { type: 'text/csv;charset=utf-8' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `laporan-kependudukan-${new Date().toISOString().slice(0, 10)}.csv`
    a.click()
    URL.revokeObjectURL(url)
  }

  return (
    <div className="animate-fade-up">
      <PageHeader title="Laporan Kependudukan" subtitle="Rekap per RT/RW dalam scope wilayah Anda"
        actions={
          <button onClick={exportCsv} className="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-line transition hover:bg-slate-50">
            Export CSV
          </button>
        } />

      <div className="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <KpiCard label="Total Warga" value={totals?.total_warga ?? '—'} icon={<Users size={18} />} accent="#2563eb" />
        <KpiCard label="Total KK" value={totals?.total_kk ?? '—'} icon={<Home size={18} />} accent="#059669" />
        <KpiCard label="Rasio L/P" value={totals ? `${totals.laki} : ${totals.perempuan}` : '—'} icon={<FileBarChart size={18} />} accent="#f59e0b" />
      </div>

      {/* Per RW summary */}
      {perRw.length > 0 && (
        <Card className="mb-5 p-6">
          <h3 className="mb-3 text-sm font-bold text-slate-900">Ringkasan per RW</h3>
          <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            {perRw.map(([rw, v]) => (
              <div key={rw} className="rounded-xl border border-line p-3">
                <p className="text-[13px] font-bold text-slate-800">{rw}</p>
                <p className="text-xs text-slate-500">{v.kk} KK · {v.warga} warga</p>
                <div className="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-100">
                  <div className="h-full rounded-full bg-gradient-to-r from-brand-400 to-brand-600"
                    style={{ width: `${Math.min(100, (v.warga / Math.max(1, totals?.total_warga ?? 1)) * 100)}%` }} />
                </div>
              </div>
            ))}
          </div>
        </Card>
      )}

      <Card className="overflow-hidden">
        {isLoading ? (
          <div className="space-y-2 p-4">{Array.from({ length: 8 }).map((_, i) => <Skeleton key={i} className="h-10" />)}</div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-[13px]">
              <thead>
                <tr className="border-b border-line bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-400">
                  <th className="px-4 py-3 font-semibold">RT</th>
                  <th className="px-4 py-3 font-semibold">RW</th>
                  <th className="px-4 py-3 text-right font-semibold">Total KK</th>
                  <th className="px-4 py-3 text-right font-semibold">Laki-laki</th>
                  <th className="px-4 py-3 text-right font-semibold">Perempuan</th>
                  <th className="px-4 py-3 text-right font-semibold">Total Warga</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-line">
                {rows.map((r) => (
                  <tr key={r.rt} className="hover:bg-slate-50">
                    <td className="px-4 py-2.5 font-semibold text-slate-800">{r.rt}</td>
                    <td className="px-4 py-2.5 text-slate-600">{r.rw}</td>
                    <td className="px-4 py-2.5 text-right tabular-nums text-slate-900">{r.total_kk}</td>
                    <td className="px-4 py-2.5 text-right tabular-nums text-blue-600">{r.laki}</td>
                    <td className="px-4 py-2.5 text-right tabular-nums text-pink-600">{r.perempuan}</td>
                    <td className="px-4 py-2.5 text-right font-bold tabular-nums text-slate-900">{r.total_warga}</td>
                  </tr>
                ))}
              </tbody>
              {totals && (
                <tfoot>
                  <tr className="border-t-2 border-slate-200 bg-slate-50 font-bold text-slate-900">
                    <td className="px-4 py-3" colSpan={2}>TOTAL</td>
                    <td className="px-4 py-3 text-right tabular-nums">{totals.total_kk}</td>
                    <td className="px-4 py-3 text-right tabular-nums">{totals.laki}</td>
                    <td className="px-4 py-3 text-right tabular-nums">{totals.perempuan}</td>
                    <td className="px-4 py-3 text-right tabular-nums">{totals.total_warga}</td>
                  </tr>
                </tfoot>
              )}
            </table>
          </div>
        )}
      </Card>
    </div>
  )
}
