'use client'

import { useState, useEffect } from 'react'
import { Coins, Search, Banknote, History, TrendingUp, AlertCircle, CheckCircle2, Layers } from 'lucide-react'
import { useIuranList, useIuranStats, useBayar, useBayarBatch, usePayments, useJenisIuranList, type IuranFilters } from '@/hooks/use-siwa'
import { PageHeader } from '@/components/PageHeader'
import { KpiCard } from '@/components/KpiCard'
import { Button, Card, Input, Label, Select, Skeleton, StatusBadge, EmptyState, Textarea } from '@/components/ui/primitives'
import { Modal } from '@/components/ui/Modal'
import { QueryError } from '@/components/QueryError'
import { fmtMoney, fmtDate } from '@/lib/utils'
import type { Iuran } from '@/types'

/* key internal → warna badge (tanpa bayar sebagian: hanya lunas/belum) */
const STATUS_COLOR: Record<string, string> = {
  belum_bayar: 'belum lunas',
  lunas: 'lunas',
}
const STATUS_LABEL: Record<string, string> = {
  belum_bayar: 'Belum Bayar',
  lunas: 'Lunas',
}

export default function IuranPage() {
  // Default filter: bulan berjalan — statistik KPI mengikuti filter aktif
  const bulanIni = new Date().toISOString().slice(0, 7)
  const [filters, setFilters] = useState<IuranFilters>({ per_page: 15, periode: bulanIni })
  const [searchInput, setSearchInput] = useState('')
  const [bayarTarget, setBayarTarget] = useState<Iuran | null>(null)
  const [historyTarget, setHistoryTarget] = useState<Iuran | null>(null)
  const [selected, setSelected] = useState<Set<number>>(new Set())
  const [batchOpen, setBatchOpen] = useState(false)

  const { data, isLoading, isFetching, isError, error, refetch } = useIuranList(filters)
  const { data: stats } = useIuranStats(filters)
  const { data: jenisList } = useJenisIuranList(true)

  const hasActiveFilter = Boolean(searchInput || filters.status || filters.jenis_iuran_id)

  // Auto-search debounce 400ms — pola sama dengan page warga/keluarga
  useEffect(() => {
    const t = setTimeout(() => {
      setFilters((f) => (f.search === (searchInput || undefined) ? f : { ...f, search: searchInput || undefined, page: 1 }))
    }, 400)
    return () => clearTimeout(t)
  }, [searchInput])

  function resetFilters() {
    setSearchInput('')
    setFilters({ per_page: 15, periode: bulanIni })
  }

  const selectedRows = (data?.data ?? []).filter((i) => selected.has(i.id))
  const selectedTotal = selectedRows.reduce((s, i) => s + Number(i.nominal) + Number(i.denda_terlambatan ?? 0), 0)

  function toggleSelect(id: number) {
    setSelected((prev) => {
      const next = new Set(prev)
      if (next.has(id)) next.delete(id)
      else next.add(id)
      return next
    })
  }

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
              onChange={(e) => setSearchInput(e.target.value)} />
          </div>
          <Input type="month" value={filters.periode ?? ''} onChange={(e) => setFilters((f) => ({ ...f, periode: e.target.value || undefined, page: 1 }))} />
          <Select value={filters.status ?? ''} onChange={(v) => setFilters((f) => ({ ...f, status: v || undefined, page: 1 }))}
            placeholder="Semua Status"
            options={[
              { value: 'belum_bayar', label: 'Belum Bayar' },
              { value: 'lunas', label: 'Lunas' },
            ]} />
          <div className="flex gap-2">
            <Select value={filters.jenis_iuran_id ? String(filters.jenis_iuran_id) : ''} onChange={(v) => setFilters((f) => ({ ...f, jenis_iuran_id: v ? Number(v) : undefined, page: 1 }))}
              placeholder="Semua Jenis" options={(jenisList?.data ?? []).map((j) => ({ value: String(j.id), label: j.nama }))} />
          </div>
        </div>
        {hasActiveFilter && (
          <div className="mt-3 flex justify-end">
            <Button variant="secondary" onClick={resetFilters}><Search size={14} /> Reset Filter</Button>
          </div>
        )}
      </Card>

      {/* Table */}
      <Card className="overflow-hidden">
        {/* Batch action bar — di atas tabel, muncul saat ada pilihan */}
        {selected.size > 0 && (
          <div className="sticky top-0 z-10 flex flex-wrap items-center justify-between gap-3 border-b border-brand-100 bg-brand-50/95 px-4 py-3 backdrop-blur">
            <p className="text-[13px] font-medium text-brand-800">
              <Layers size={14} className="mr-1.5 inline" />
              {selected.size} tagihan terpilih · total <strong className="tabular-nums">{fmtMoney(selectedTotal)}</strong>
              <span className="ml-2 font-normal text-brand-600">(semua dibayar penuh)</span>
            </p>
            <div className="flex gap-2">
              <Button size="sm" variant="secondary" onClick={() => setSelected(new Set())}>Batal</Button>
              <Button size="sm" onClick={() => setBatchOpen(true)}><Banknote size={13} /> Bayar Terpilih</Button>
            </div>
          </div>
        )}
        {isLoading ? (
          <div className="space-y-2 p-4">{Array.from({ length: 8 }).map((_, i) => <Skeleton key={i} className="h-12" />)}</div>
        ) : isError ? (
          <QueryError message={error?.message} onRetry={() => refetch()} />
        ) : (data?.data ?? []).length === 0 ? (
          <EmptyState icon={<Coins size={24} />} title="Belum ada tagihan"
            hint={`Gunakan Generate Tagihan untuk membuat tagihan periode ${bulanIni}.`} />
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-[13px]">
              <thead>
                <tr className="border-b border-line bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-400">
                  <th className="w-10 px-4 py-3">
                    <input
                      type="checkbox"
                      className="h-4 w-4 rounded border-line accent-brand-600"
                      checked={selected.size > 0 && (data?.data ?? []).every((i) => i.status === 'lunas' || selected.has(i.id))}
                      onChange={(e) =>
                        setSelected(e.target.checked
                          ? new Set((data?.data ?? []).filter((i) => i.status === 'belum_bayar').map((i) => i.id))
                          : new Set())
                      }
                      title="Pilih semua belum bayar"
                    />
                  </th>
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
                   return (
                    <tr key={i.id} className={`hover:bg-slate-50 ${isFetching ? 'opacity-60' : ''}`}>
                      <td className="px-4 py-3">
                        {i.status === 'belum_bayar' ? (
                          <input
                            type="checkbox"
                            className="h-4 w-4 rounded border-line accent-brand-600"
                            checked={selected.has(i.id)}
                            onChange={() => toggleSelect(i.id)}
                          />
                        ) : (
                          <CheckCircle2 size={14} className="text-emerald-400" />
                        )}
                      </td>
                      <td className="px-4 py-3 tabular-nums text-slate-500">…{i.keluarga?.no_kk?.slice(-4)}</td>
                      <td className="px-4 py-3 font-semibold text-slate-900">{i.keluarga?.kepala_keluarga?.nama_lengkap ?? i.keluarga?.nama_kepala_keluarga ?? '-'}</td>
                      <td className="px-4 py-3 text-slate-600">{i.jenis_iuran?.nama}</td>
                      <td className="px-4 py-3 tabular-nums text-slate-600">{i.periode_bulan}</td>
                      <td className="px-4 py-3 text-right tabular-nums font-semibold text-slate-900">{fmtMoney(Number(i.nominal))}</td>
                      <td className="px-4 py-3 text-right tabular-nums text-slate-600">{fmtMoney(dibayar)}</td>
                      <td className="px-4 py-3 tabular-nums text-slate-600">{i.jatuh_tempo?.slice(0, 10) ?? '—'}</td>
                      <td className="px-4 py-3">
                        <StatusBadge status={STATUS_COLOR[i.status]} label={STATUS_LABEL[i.status]} />
                      </td>
                      <td className="px-4 py-3">
                        <div className="flex justify-end gap-1">
                          {i.status === 'belum_bayar' && (
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
      {batchOpen && (
        <BatchModal
          items={selectedRows}
          onClose={() => setBatchOpen(false)}
          onDone={() => { setBatchOpen(false); setSelected(new Set()) }}
        />
      )}
    </div>
  )
}

/* ═══════════ Bayar modal (single — selalu penuh, tanpa input jumlah) ═══════════ */
function BayarModal({ iuran, onClose }: { iuran: Iuran; onClose: () => void }) {
  const bayar = useBayar()
  const total = Number(iuran.nominal) + Number(iuran.denda_terlambatan ?? 0)
  const [metode, setMetode] = useState('cash')
  const [ket, setKet] = useState('')

  return (
    <Modal open onClose={onClose} title="Catat Pembayaran"
      subtitle={`${iuran.jenis_iuran?.nama} · periode ${iuran.periode_bulan} · KK …${iuran.keluarga?.no_kk?.slice(-4)}`}>
      <div className="space-y-4">
        <Card className="bg-slate-50 p-3">
          <div className="flex justify-between text-[13px]">
            <span className="text-slate-500">Nominal tagihan</span>
            <span className="font-bold tabular-nums text-slate-900">{fmtMoney(Number(iuran.nominal))}</span>
          </div>
          {Number(iuran.denda_terlambatan ?? 0) > 0 && (
            <div className="flex justify-between text-[13px]">
              <span className="text-slate-500">Denda keterlambatan</span>
              <span className="tabular-nums text-rose-600">{fmtMoney(Number(iuran.denda_terlambatan))}</span>
            </div>
          )}
          <div className="flex justify-between border-t border-line pt-1.5 text-[13px]">
            <span className="font-semibold text-slate-700">Dibayar penuh</span>
            <span className="font-bold tabular-nums text-emerald-600">{fmtMoney(total)}</span>
          </div>
        </Card>

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
        <div>
          <Label>Keterangan</Label>
          <Textarea rows={2} value={ket} onChange={(e) => setKet(e.target.value)} placeholder="opsional…" />
        </div>

        <div className="flex justify-end gap-2 border-t border-line pt-4">
          <Button variant="secondary" onClick={onClose}>Batal</Button>
          <Button
            disabled={bayar.isPending}
            onClick={() => bayar.mutate(
              { id: iuran.id, metode_pembayaran: metode, keterangan: ket || undefined },
              { onSuccess: onClose },
            )}
          >
            {bayar.isPending ? 'Menyimpan…' : `Bayar ${fmtMoney(total)}`}
          </Button>
        </div>
      </div>
    </Modal>
  )
}

/* ═══════════ Batch modal (rapelan — banyak tagihan lunas sekaligus) ═══════════ */
function BatchModal({ items, onClose, onDone }: { items: Iuran[]; onClose: () => void; onDone: () => void }) {
  const batch = useBayarBatch()
  const total = items.reduce((s, i) => s + Number(i.nominal) + Number(i.denda_terlambatan ?? 0), 0)
  const [metode, setMetode] = useState('cash')
  const [ket, setKet] = useState('')

  return (
    <Modal open onClose={onClose} title="Bayar Rapelan"
      subtitle={`${items.length} tagihan terpilih · semua dibayar penuh · total ${fmtMoney(total)}`}>
      <div className="space-y-4">
        <div className="max-h-44 space-y-1 overflow-y-auto rounded-xl border border-line p-2">
          {items.map((i) => (
            <div key={i.id} className="flex items-center justify-between px-2 py-1.5 text-[12.5px]">
              <span className="min-w-0 truncate text-slate-600">
                …{i.keluarga?.no_kk?.slice(-4)} · {i.jenis_iuran?.nama} · <span className="tabular-nums">{i.periode_bulan}</span>
              </span>
              <span className="ml-2 shrink-0 font-semibold tabular-nums text-slate-800">
                {fmtMoney(Number(i.nominal) + Number(i.denda_terlambatan ?? 0))}
              </span>
            </div>
          ))}
        </div>

        <div className="grid grid-cols-2 gap-3">
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
          <div>
            <Label>Keterangan</Label>
            <Input value={ket} onChange={(e) => setKet(e.target.value)} placeholder="mis. rapelan 3 bulan" />
          </div>
        </div>

        <div className="flex items-center justify-between border-t border-line pt-4">
          <p className="text-[12px] text-slate-400">Satu metode & keterangan untuk semua tagihan.</p>
          <div className="flex gap-2">
            <Button variant="secondary" onClick={onClose}>Batal</Button>
            <Button
              disabled={batch.isPending}
              onClick={() => batch.mutate(
                { payments: items.map((i) => ({ iuran_id: i.id })), metode_pembayaran: metode, keterangan: ket || undefined },
                { onSuccess: onDone },
              )}
            >
              {batch.isPending ? 'Memproses…' : `Lunasi ${items.length} Tagihan`}
            </Button>
          </div>
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
      subtitle={`${iuran.jenis_iuran?.nama} · ${iuran.periode_bulan} · status: ${iuran.status}`}>
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
