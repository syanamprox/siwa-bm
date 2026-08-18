'use client'

import { useState } from 'react'
import { Coins, Search, Banknote, History, TrendingUp, AlertCircle, CheckCircle2, Clock } from 'lucide-react'
import { useIuranList, useIuranStats, useBayar, usePayments, useJenisIuranList, type IuranFilters } from '@/hooks/use-siwa'
import { PageHeader } from '@/components/PageHeader'
import { KpiCard } from '@/components/KpiCard'
import { Button, Card, Input, Label, Select, Skeleton, StatusBadge, EmptyState, Textarea } from '@/components/ui/primitives'
import { Modal } from '@/components/ui/Modal'
import { fmtMoney, fmtDate } from '@/lib/utils'
import type { Iuran } from '@/types'

const STATUS_LABEL: Record<string, string> = {
  belum_bayar: 'belum lunas',
  sebagian: 'pending',
  lunas: 'lunas',
  batal: 'cancelled',
}

export default function IuranPage() {
  const [filters, setFilters] = useState<IuranFilters>({ per_page: 15 })
  const [searchInput, setSearchInput] = useState('')
  const [bayarTarget, setBayarTarget] = useState<Iuran | null>(null)
  const [historyTarget, setHistoryTarget] = useState<Iuran | null>(null)

  const { data, isLoading, isFetching } = useIuranList(filters)
  const { data: stats } = useIuranStats()
  const { data: jenisList } = useJenisIuranList(true)

  function applySearch() {
    setFilters((f) => ({ ...f, search: searchInput || undefined, page: 1 }))
  }

  const bulanIni = new Date().toISOString().slice(0, 7)

  return (
    <div className="animate-fade-up">
      <PageHeader title="Tagihan Iuran" subtitle="Tagihan per kartu keluarga, pembayaran & tunggakan" />

      {/* KPI */}
      <div className="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <KpiCard label="Total Tagihan" value={stats?.data?.total ?? '—'} icon={<Coins size={18} />} accent="#2563eb" />
        <KpiCard label="Belum Bayar" value={stats?.data?.belum_bayar ?? '—'} icon={<AlertCircle size={18} />} accent="#f59e0b" />
        <KpiCard label="Lunas" value={stats?.data?.lunas ?? '—'} icon={<CheckCircle2 size={18} />} accent="#10b981" />
        <KpiCard label="Total Nominal" value={fmtMoney(stats?.data?.total_nominal ?? 0)} icon={<TrendingUp size={18} />} accent="#059669"
          sub={`${stats?.data?.persentase_lunas ?? 0}% lunas`} />
      </div>

      {/* Filter */}
      <Card className="mb-4 p-4">
        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
          <div className="lg:col-span-2">
            <Input placeholder="Cari KK / kepala keluarga / jenis…" value={searchInput}
              onChange={(e) => setSearchInput(e.target.value)} onKeyDown={(e) => e.key === 'Enter' && applySearch()} />
          </div>
          <Input type="month" value={filters.periode ?? ''} onChange={(e) => setFilters((f) => ({ ...f, periode: e.target.value || undefined, page: 1 }))} />
          <Select value={filters.status ?? ''} onChange={(v) => setFilters((f) => ({ ...f, status: v || undefined, page: 1 }))}
            placeholder="Semua Status"
            options={[
              { value: 'belum_bayar', label: 'Belum Bayar' },
              { value: 'sebagian', label: 'Sebagian' },
              { value: 'lunas', label: 'Lunas' },
            ]} />
          <Select value={filters.jenis_iuran_id ? String(filters.jenis_iuran_id) : ''} onChange={(v) => setFilters((f) => ({ ...f, jenis_iuran_id: v ? Number(v) : undefined, page: 1 }))}
            placeholder="Semua Jenis" options={(jenisList?.data ?? []).map((j) => ({ value: String(j.id), label: j.nama }))} />
        </div>
      </Card>

      {/* Table */}
      <Card className="overflow-hidden">
        {isLoading ? (
          <div className="space-y-2 p-4">{Array.from({ length: 8 }).map((_, i) => <Skeleton key={i} className="h-12" />)}</div>
        ) : (data?.data ?? []).length === 0 ? (
          <EmptyState icon={<Coins size={24} />} title="Belum ada tagihan"
            hint={`Gunakan Generate Tagihan untuk membuat tagihan periode ${bulanIni}.`} />
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-[13px]">
              <thead>
                <tr className="border-b border-line bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-400">
                  <th className="px-4 py-3 font-semibold">KK</th>
                  <th className="px-4 py-3 font-semibold">Kepala Keluarga</th>
                  <th className="px-4 py-3 font-semibold">Jenis</th>
                  <th className="px-4 py-3 font-semibold">Periode</th>
                  <th className="px-4 py-3 text-right font-semibold">Nominal</th>
                  <th className="px-4 py-3 text-right font-semibold">Dibayar</th>
                  <th className="px-4 py-3 font-semibold">Jatuh Tempo</th>
                  <th className="px-4 py-3 font-semibold">Status</th>
                  <th className="px-4 py-3 text-right font-semibold">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-line">
                {data!.data.map((i) => {
                  const dibayar = Number(i.total_dibayar ?? 0)
                  const sisa = Number(i.nominal) + Number(i.denda_terlambatan ?? 0) - dibayar
                  return (
                    <tr key={i.id} className={`hover:bg-slate-50 ${isFetching ? 'opacity-60' : ''}`}>
                      <td className="px-4 py-3 tabular-nums text-slate-500">…{i.keluarga?.no_kk?.slice(-4)}</td>
                      <td className="px-4 py-3 font-semibold text-slate-900">{i.keluarga?.nama_kepala_keluarga ?? '-'}</td>
                      <td className="px-4 py-3 text-slate-600">{i.jenisIuran?.nama}</td>
                      <td className="px-4 py-3 tabular-nums text-slate-600">{i.periode_bulan}</td>
                      <td className="px-4 py-3 text-right tabular-nums font-semibold text-slate-900">{fmtMoney(Number(i.nominal))}</td>
                      <td className="px-4 py-3 text-right tabular-nums text-slate-600">{fmtMoney(dibayar)}</td>
                      <td className="px-4 py-3 tabular-nums text-slate-600">{i.jatuh_tempo?.slice(0, 10) ?? '—'}</td>
                      <td className="px-4 py-3">
                        <StatusBadge status={STATUS_LABEL[i.status]} />
                        {i.status === 'sebagian' && <span className="ml-1 text-[11px] text-amber-600">sisa {fmtMoney(sisa)}</span>}
                      </td>
                      <td className="px-4 py-3">
                        <div className="flex justify-end gap-1">
                          {i.status !== 'lunas' && i.status !== 'batal' && (
                            <Button size="sm" onClick={() => setBayarTarget(i)}><Banknote size={13} /> Bayar</Button>
                          )}
                          <Button size="sm" variant="ghost" onClick={() => setHistoryTarget(i)} title="Riwayat"><History size={14} /></Button>
                        </div>
                      </td>
                    </tr>
                  )
                })}
              </tbody>
            </table>
          </div>
        )}

        {data && data.meta.last_page > 1 && (
          <div className="flex items-center justify-between border-t border-line px-4 py-3">
            <span className="text-xs text-slate-500">Hal. {data.meta.current_page} / {data.meta.last_page} · {data.meta.total} data</span>
            <div className="flex gap-2">
              <Button size="sm" variant="secondary" disabled={data.meta.current_page <= 1} onClick={() => setFilters((f) => ({ ...f, page: (f.page ?? 1) - 1 }))}>Sebelumnya</Button>
              <Button size="sm" variant="secondary" disabled={data.meta.current_page >= data.meta.last_page} onClick={() => setFilters((f) => ({ ...f, page: (f.page ?? 1) + 1 }))}>Berikutnya</Button>
            </div>
          </div>
        )}
      </Card>

      {bayarTarget && <BayarModal iuran={bayarTarget} onClose={() => setBayarTarget(null)} />}
      {historyTarget && <HistoryModal iuran={historyTarget} onClose={() => setHistoryTarget(null)} />}
    </div>
  )
}

/* ═══════════ Bayar modal ═══════════ */
function BayarModal({ iuran, onClose }: { iuran: Iuran; onClose: () => void }) {
  const bayar = useBayar()
  const dibayar = Number(iuran.total_dibayar ?? 0)
  const sisa = Number(iuran.nominal) + Number(iuran.denda_terlambatan ?? 0) - dibayar
  const [jumlah, setJumlah] = useState(String(sisa))
  const [metode, setMetode] = useState('cash')
  const [ket, setKet] = useState('')

  return (
    <Modal open onClose={onClose} title="Catat Pembayaran"
      subtitle={`${iuran.jenisIuran?.nama} · periode ${iuran.periode_bulan} · KK …${iuran.keluarga?.no_kk?.slice(-4)}`}>
      <div className="space-y-4">
        <Card className="bg-slate-50 p-3">
          <div className="flex justify-between text-[13px]">
            <span className="text-slate-500">Total tagihan</span>
            <span className="font-bold tabular-nums text-slate-900">{fmtMoney(Number(iuran.nominal) + Number(iuran.denda_terlambatan ?? 0))}</span>
          </div>
          <div className="flex justify-between text-[13px]">
            <span className="text-slate-500">Sudah dibayar</span>
            <span className="tabular-nums text-emerald-600">{fmtMoney(dibayar)}</span>
          </div>
          <div className="flex justify-between border-t border-line pt-1.5 text-[13px]">
            <span className="font-semibold text-slate-700">Sisa</span>
            <span className="font-bold tabular-nums text-amber-600">{fmtMoney(sisa)}</span>
          </div>
        </Card>

        <div className="grid grid-cols-2 gap-3">
          <div>
            <Label>Jumlah Bayar *</Label>
            <Input type="number" min={0} max={sisa} value={jumlah} onChange={(e) => setJumlah(e.target.value)} className="tabular-nums" required />
          </div>
          <div>
            <Label>Metode *</Label>
            <Select value={metode} onChange={setMetode}
              options={[
                { value: 'cash', label: 'Cash' },
                { value: 'transfer', label: 'Transfer' },
                { value: 'qris', label: 'QRIS' },
                { value: 'ewallet', label: 'E-Wallet' },
              ]} />
          </div>
        </div>
        <div>
          <Label>Keterangan</Label>
          <Textarea rows={2} value={ket} onChange={(e) => setKet(e.target.value)} placeholder="opsional…" />
        </div>

        <div className="flex justify-end gap-2 border-t border-line pt-4">
          <Button variant="secondary" onClick={onClose}>Batal</Button>
          <Button
            disabled={bayar.isPending || !jumlah || Number(jumlah) <= 0}
            onClick={() => bayar.mutate(
              { id: iuran.id, jumlah_bayar: Number(jumlah), metode_pembayaran: metode, keterangan: ket || undefined },
              { onSuccess: onClose },
            )}
          >
            {bayar.isPending ? 'Menyimpan…' : `Bayar ${fmtMoney(Number(jumlah))}`}
          </Button>
        </div>
      </div>
    </Modal>
  )
}

/* ═══════════ Riwayat modal ═══════════ */
function HistoryModal({ iuran, onClose }: { iuran: Iuran; onClose: () => void }) {
  const { data, isLoading } = usePayments(iuran.id)

  return (
    <Modal open onClose={onClose} title="Riwayat Pembayaran"
      subtitle={`${iuran.jenisIuran?.nama} · ${iuran.periode_bulan} · status: ${iuran.status}`}>
      {isLoading ? (
        <Skeleton className="h-32" />
      ) : (data?.data ?? []).length === 0 ? (
        <p className="py-6 text-center text-sm text-slate-400">Belum ada pembayaran</p>
      ) : (
        <div className="space-y-2">
          {data!.data.map((p) => (
            <div key={p.id} className="flex items-center gap-3 rounded-xl border border-line px-3 py-2.5">
              <span className={`flex h-8 w-8 items-center justify-center rounded-lg ${p.metode_pembayaran === 'cash' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600'}`}>
                <Banknote size={14} />
              </span>
              <div className="min-w-0 flex-1">
                <p className="text-[13px] font-bold tabular-nums text-slate-900">{fmtMoney(p.jumlah_bayar)} <span className="font-medium text-slate-400">· {p.metode_pembayaran}</span></p>
                <p className="text-[11px] text-slate-400">{p.petugas ?? '—'} · {fmtDate(p.created_at)} {p.nomor_referensi ? `· ${p.nomor_referensi}` : ''}</p>
              </div>
            </div>
          ))}
        </div>
      )}
    </Modal>
  )
}
