'use client'

import { useState } from 'react'
import { Landmark, Plus, Pencil, Trash2, Power } from 'lucide-react'
import { useJenisIuranList, useJenisIuranMutations } from '@/hooks/use-siwa'
import { PageHeader } from '@/components/PageHeader'
import { Button, Card, Input, Label, Select, Skeleton, StatusBadge, EmptyState, Textarea } from '@/components/ui/primitives'
import { Modal } from '@/components/ui/Modal'
import { QueryError } from '@/components/QueryError'
import { fmtMoney } from '@/lib/utils'
import type { JenisIuran } from '@/types'

const PERIODE_LABEL: Record<string, string> = { bulanan: 'Setiap Bulan', tahunan: 'Tahunan', sekali: 'Sekali' }

export default function JenisIuranPage() {
  const [modal, setModal] = useState<{ mode: 'create' | 'edit'; jenis?: JenisIuran } | null>(null)
  const [deleteTarget, setDeleteTarget] = useState<JenisIuran | null>(null)

  const { data, isLoading, isError, error, refetch } = useJenisIuranList()
  const { create, update, remove, toggle } = useJenisIuranMutations()

  return (
    <div className="animate-fade-up">
      <PageHeader title="Jenis Iuran" subtitle="Master data jenis iuran & nominal default"
        actions={<Button onClick={() => setModal({ mode: 'create' })}><Plus size={15} /> Tambah Jenis</Button>} />

      <Card className="overflow-hidden">
        {isLoading ? (
          <div className="space-y-2 p-4">{Array.from({ length: 5 }).map((_, i) => <Skeleton key={i} className="h-12" />)}</div>
        ) : isError ? (
          <QueryError message={error?.message} onRetry={() => refetch()} />
        ) : (data?.data ?? []).length === 0 ? (
          <EmptyState icon={<Landmark size={24} />} title="Belum ada jenis iuran" />
        ) : (
          <table className="w-full text-[13px]">
            <thead>
              <tr className="border-b border-line bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-400">
                <th className="px-4 py-3 font-semibold">Nama</th>
                <th className="px-4 py-3 font-semibold">Kode</th>
                <th className="px-4 py-3 text-right font-semibold">Nominal Default</th>
                <th className="px-4 py-3 font-semibold">Frekuensi</th>
                <th className="px-4 py-3 text-right font-semibold">KK Terhubung</th>
                <th className="px-4 py-3 font-semibold">Status</th>
                <th className="px-4 py-3 text-right font-semibold">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-line">
              {data!.data.map((j) => (
                <tr key={j.id} className="hover:bg-slate-50">
                  <td className="px-4 py-3">
                    <p className="font-semibold text-slate-900">{j.nama}</p>
                    {j.keterangan && <p className="text-[11px] text-slate-400">{j.keterangan.slice(0, 60)}</p>}
                  </td>
                  <td className="px-4 py-3"><span className="rounded-md bg-slate-100 px-1.5 py-0.5 font-mono text-[11px] text-slate-600">{j.kode}</span></td>
                  <td className="px-4 py-3 text-right font-bold tabular-nums text-slate-900">{fmtMoney(Number(j.jumlah))}</td>
                  <td className="px-4 py-3 text-slate-600">{PERIODE_LABEL[j.periode] ?? j.periode}</td>
                  <td className="px-4 py-3 text-right tabular-nums text-slate-600">{j.koneksi_aktif ?? 0}</td>
                   <td className="px-4 py-3"><StatusBadge status={j.is_aktif ? 'active' : 'paused'} label={j.is_aktif ? 'Aktif' : 'Nonaktif'} /></td>
                  <td className="px-4 py-3">
                    <div className="flex justify-end gap-1">
                      <Button size="sm" variant="ghost" onClick={() => toggle.mutate(j.id)} title={j.is_aktif ? 'Nonaktifkan' : 'Aktifkan'}>
                        <Power size={14} className={j.is_aktif ? 'text-emerald-500' : 'text-slate-300'} />
                      </Button>
                      <Button size="sm" variant="ghost" onClick={() => setModal({ mode: 'edit', jenis: j })}><Pencil size={14} /></Button>
                      <Button size="sm" variant="ghost" className="text-rose-500 hover:bg-rose-50" onClick={() => setDeleteTarget(j)}><Trash2 size={14} /></Button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </Card>

      {modal && (
        <JenisFormModal mode={modal.mode} jenis={modal.jenis} onClose={() => setModal(null)}
          onSubmit={(payload) => {
            if (modal.mode === 'create') create.mutate(payload)
            else if (modal.jenis) update.mutate({ id: modal.jenis.id, payload })
            setModal(null)
          }}
          pending={create.isPending || update.isPending} />
      )}

      <Modal open={!!deleteTarget} onClose={() => setDeleteTarget(null)} title="Hapus Jenis Iuran" size="sm">
        <p className="text-sm text-slate-600">Yakin hapus <strong>{deleteTarget?.nama}</strong>?</p>
        <div className="mt-5 flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
          <Button variant="danger" onClick={() => { remove.mutate(deleteTarget!.id); setDeleteTarget(null) }}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}

function JenisFormModal({ mode, jenis, onClose, onSubmit, pending }: {
  mode: 'create' | 'edit'; jenis?: JenisIuran; onClose: () => void
  onSubmit: (payload: Record<string, unknown>) => void; pending: boolean
}) {
  const [form, setForm] = useState({
    nama: jenis?.nama ?? '',
    kode: jenis?.kode ?? '',
    jumlah: jenis?.jumlah ? String(Number(jenis.jumlah)) : '',
    periode: jenis?.periode ?? 'bulanan',
    keterangan: jenis?.keterangan ?? '',
  })
  const set = (k: string, v: string) => setForm((f) => ({ ...f, [k]: v }))

  return (
    <Modal open onClose={onClose} title={mode === 'create' ? 'Tambah Jenis Iuran' : 'Edit Jenis Iuran'} size="md">
      <form onSubmit={(e) => { e.preventDefault(); onSubmit(form) }} className="space-y-4">
        <div className="grid grid-cols-2 gap-4">
          <div className="col-span-2">
            <Label>Nama *</Label>
            <Input value={form.nama} onChange={(e) => set('nama', e.target.value)} required placeholder="mis. Iuran Kebersihan" />
          </div>
          <div>
            <Label>Kode *</Label>
            <Input value={form.kode} onChange={(e) => set('kode', e.target.value.toUpperCase())} required maxLength={10} className="font-mono" />
          </div>
          <div>
            <Label>Nominal Default *</Label>
            <Input type="number" min={0} value={form.jumlah} onChange={(e) => set('jumlah', e.target.value)} required className="tabular-nums" />
          </div>
          <div>
            <Label>Frekuensi *</Label>
            <Select value={form.periode} onChange={(v) => set('periode', v)}
              options={[
                { value: 'bulanan', label: 'Setiap Bulan' },
                { value: 'tahunan', label: 'Tahunan' },
                { value: 'sekali', label: 'Sekali' },
              ]} />
          </div>
        </div>
        <div>
          <Label>Keterangan</Label>
          <Textarea rows={2} value={form.keterangan} onChange={(e) => set('keterangan', e.target.value)} />
        </div>
        <div className="flex justify-end gap-2 border-t border-line pt-4">
          <Button type="button" variant="secondary" onClick={onClose}>Batal</Button>
          <Button type="submit" disabled={pending}>{pending ? 'Menyimpan…' : 'Simpan'}</Button>
        </div>
      </form>
    </Modal>
  )
}
