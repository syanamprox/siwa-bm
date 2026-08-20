'use client'

import { useState } from 'react'
import { Link2, Search, Power, Trash2, Pencil } from 'lucide-react'
import { useKeluargaIuranList, useUpdateConnIuran, useDisconnectIuran, useJenisIuranList } from '@/hooks/use-siwa'
import { PageHeader } from '@/components/PageHeader'
import { KpiCard } from '@/components/KpiCard'
import { Button, Card, Input, Label, Select, Skeleton, StatusBadge, EmptyState } from '@/components/ui/primitives'
import { Modal } from '@/components/ui/Modal'
import { QueryError } from '@/components/QueryError'
import { fmtMoney } from '@/lib/utils'
import type { KeluargaIuranConn } from '@/types'

export default function KeluargaIuranPage() {
  const [searchInput, setSearchInput] = useState('')
  const [filters, setFilters] = useState<{ search?: string; jenis_iuran_id?: number; status_aktif?: string }>({})
  const [editTarget, setEditTarget] = useState<KeluargaIuranConn | null>(null)
  const [disconnectTarget, setDisconnectTarget] = useState<KeluargaIuranConn | null>(null)

  const { data, isLoading, isError, error, refetch } = useKeluargaIuranList(filters)
  const { data: jenisList } = useJenisIuranList()
  const updateConn = useUpdateConnIuran()
  const disconnect = useDisconnectIuran()

  return (
    <div className="animate-fade-up">
      <PageHeader title="Konfigurasi Iuran Keluarga" subtitle="Hubungan kartu keluarga ↔ jenis iuran (nominal custom, aktif/nonaktif)" />

      <div className="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <KpiCard label="Total Koneksi" value={data?.meta?.total ?? '—'} />
        <KpiCard label="Koneksi Aktif" value={data?.meta?.aktif ?? '—'} accent="#059669" />
        <KpiCard label="Nominal Custom" value={data?.meta?.custom_nominal ?? '—'} accent="#f59e0b" />
      </div>

      <Card className="mb-4 p-4">
        <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
          <Input placeholder="Cari KK / kepala keluarga…" value={searchInput}
            onChange={(e) => setSearchInput(e.target.value)}
            onKeyDown={(e) => e.key === 'Enter' && setFilters((f) => ({ ...f, search: searchInput || undefined }))} />
          <Select value={filters.jenis_iuran_id ? String(filters.jenis_iuran_id) : ''} placeholder="Semua Jenis"
            onChange={(v) => setFilters((f) => ({ ...f, jenis_iuran_id: v ? Number(v) : undefined }))}
            options={(jenisList?.data ?? []).map((j) => ({ value: String(j.id), label: j.nama }))} />
          <Select value={filters.status_aktif ?? ''} placeholder="Semua Status"
            onChange={(v) => setFilters((f) => ({ ...f, status_aktif: v || undefined }))}
            options={[{ value: '1', label: 'Aktif' }, { value: '0', label: 'Nonaktif' }]} />
        </div>
      </Card>

      <Card className="overflow-hidden">
        {isLoading ? (
          <div className="space-y-2 p-4">{Array.from({ length: 6 }).map((_, i) => <Skeleton key={i} className="h-12" />)}</div>
        ) : isError ? (
          <QueryError message={error?.message} onRetry={() => refetch()} />
        ) : (data?.data ?? []).length === 0 ? (
          <EmptyState icon={<Link2 size={24} />} title="Belum ada koneksi iuran"
            hint="Hubungkan jenis iuran ke keluarga dari halaman detail keluarga." />
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-[13px]">
              <thead>
                <tr className="border-b border-line bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-400">
                  <th className="px-4 py-3 font-semibold">Keluarga</th>
                  <th className="px-4 py-3 font-semibold">Jenis Iuran</th>
                  <th className="px-4 py-3 text-right font-semibold">Nominal Efektif</th>
                  <th className="px-4 py-3 font-semibold">Alasan Custom</th>
                  <th className="px-4 py-3 font-semibold">Status</th>
                  <th className="px-4 py-3 text-right font-semibold">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-line">
                {data!.data.map((c) => (
                  <tr key={c.id} className="hover:bg-slate-50">
                    <td className="px-4 py-3">
                      <p className="font-semibold text-slate-900">{c.keluarga?.nama_kepala_keluarga ?? '-'}</p>
                      <p className="text-[11px] tabular-nums text-slate-400">{c.keluarga?.no_kk}</p>
                    </td>
                    <td className="px-4 py-3 text-slate-600">{c.jenisIuran?.nama}</td>
                    <td className="px-4 py-3 text-right font-bold tabular-nums text-slate-900">
                      {fmtMoney(Number(c.nominal_custom ?? c.jenisIuran?.jumlah ?? 0))}
                      {c.nominal_custom && <span className="ml-1 rounded-full bg-amber-50 px-1.5 py-0.5 text-[10px] font-bold text-amber-600">custom</span>}
                    </td>
                    <td className="px-4 py-3 max-w-[200px] truncate text-slate-500">{c.alasan_custom ?? '—'}</td>
                    <td className="px-4 py-3"><StatusBadge status={c.status_aktif ? 'active' : 'paused'} label={c.status_aktif ? 'Aktif' : 'Nonaktif'} /></td>
                    <td className="px-4 py-3">
                      <div className="flex justify-end gap-1">
                        <Button size="sm" variant="ghost" onClick={() => updateConn.mutate({ id: c.id, status_aktif: !c.status_aktif })} title="Toggle">
                          <Power size={14} className={c.status_aktif ? 'text-emerald-500' : 'text-slate-300'} />
                        </Button>
                        <Button size="sm" variant="ghost" onClick={() => setEditTarget(c)}><Pencil size={14} /></Button>
                        <Button size="sm" variant="ghost" className="text-rose-500 hover:bg-rose-50" onClick={() => setDisconnectTarget(c)} title="Putuskan"><Trash2 size={14} /></Button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </Card>

      {/* Edit modal */}
      <Modal open={!!editTarget} onClose={() => setEditTarget(null)} title="Edit Koneksi Iuran"
        subtitle={`${editTarget?.keluarga?.no_kk} → ${editTarget?.jenisIuran?.nama}`} size="sm">
        {editTarget && (
          <ConnEditForm conn={editTarget} onClose={() => setEditTarget(null)}
            onSubmit={(payload) => { updateConn.mutate({ id: editTarget.id, ...payload }); setEditTarget(null) }} />
        )}
      </Modal>

      {/* Disconnect confirm */}
      <Modal open={!!disconnectTarget} onClose={() => setDisconnectTarget(null)} title="Putuskan Iuran?" size="sm"
        subtitle={disconnectTarget ? `${disconnectTarget.keluarga?.no_kk} → ${disconnectTarget.jenisIuran?.nama}` : undefined}>
        <p className="text-sm text-slate-600">
          Keluarga <strong>{disconnectTarget?.keluarga?.nama_kepala_keluarga ?? disconnectTarget?.keluarga?.no_kk}</strong> tidak
          lagi ditagih <strong>{disconnectTarget?.jenisIuran?.nama}</strong> pada generate tagihan berikutnya. Riwayat tagihan
          yang sudah ada tetap tersimpan.
        </p>
        <div className="mt-5 flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDisconnectTarget(null)}>Batal</Button>
          <Button variant="danger" disabled={disconnect.isPending}
            onClick={() => { disconnect.mutate(disconnectTarget!.id); setDisconnectTarget(null) }}>
            {disconnect.isPending ? 'Memutuskan…' : 'Putuskan'}
          </Button>
        </div>
      </Modal>
    </div>
  )
}

function ConnEditForm({ conn, onClose, onSubmit }: {
  conn: KeluargaIuranConn
  onClose: () => void
  onSubmit: (payload: { nominal_custom?: number | null; alasan_custom?: string }) => void
}) {
  const [nominal, setNominal] = useState(conn.nominal_custom ? String(conn.nominal_custom) : '')
  const [alasan, setAlasan] = useState(conn.alasan_custom ?? '')

  return (
    <div className="space-y-4">
      <div>
        <Label>Nominal Custom (kosongkan = default {fmtMoney(Number(conn.jenisIuran?.jumlah ?? 0))})</Label>
        <Input type="number" min={0} value={nominal} onChange={(e) => setNominal(e.target.value)} className="tabular-nums" placeholder="default" />
      </div>
      <div>
        <Label>Alasan Custom</Label>
        <Input value={alasan} onChange={(e) => setAlasan(e.target.value)} placeholder="mis. difasilitasi RT" />
      </div>
      <div className="flex justify-end gap-2">
        <Button variant="secondary" onClick={onClose}>Batal</Button>
        <Button onClick={() => onSubmit({ nominal_custom: nominal ? Number(nominal) : null, alasan_custom: alasan || undefined })}>Simpan</Button>
      </div>
    </div>
  )
}
