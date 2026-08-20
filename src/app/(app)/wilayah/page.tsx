'use client'

import { useState } from 'react'
import { Map, Plus, Pencil, Trash2, ChevronRight, Building2, Users, Home } from 'lucide-react'
import { toast } from 'sonner'
import { useQueryClient } from '@tanstack/react-query'
import { api, ApiError } from '@/lib/api-client'
import { useWilayahTree } from '@/hooks/use-siwa'
import { PageHeader } from '@/components/PageHeader'
import { Button, Card, Input, Label, Select, Skeleton, StatusBadge, EmptyState } from '@/components/ui/primitives'
import { Modal } from '@/components/ui/Modal'
import { QueryError } from '@/components/QueryError'
import type { WilayahRef } from '@/types'

const TINGKAT_ICON: Record<string, React.ReactNode> = {
  Kelurahan: <Building2 size={14} />,
  RW: <Users size={14} />,
  RT: <Home size={14} />,
}

export default function WilayahPage() {
  const { data, isLoading, isError, error, refetch } = useWilayahTree()
  const qc = useQueryClient()
  const [expanded, setExpanded] = useState<Set<number>>(new Set())
  const [modal, setModal] = useState<{ mode: 'create' | 'edit'; wilayah?: WilayahRef; parentId?: number; parentTingkat?: string } | null>(null)
  const [deleteTarget, setDeleteTarget] = useState<WilayahRef | null>(null)
  const [pending, setPending] = useState(false)

  async function submit(payload: Record<string, unknown>, mode: 'create' | 'edit', id?: number) {
    setPending(true)
    try {
      if (mode === 'create') await api.post('/wilayah', payload)
      else await api.put(`/wilayah/${id}`, payload)
      toast.success(mode === 'create' ? 'Wilayah ditambahkan' : 'Wilayah diperbarui')
      qc.invalidateQueries({ queryKey: ['wilayah-tree'] })
      qc.invalidateQueries({ queryKey: ['rt-options'] })
      setModal(null)
    } catch (e) {
      toast.error(e instanceof ApiError ? (Object.values(e.errors ?? {})[0]?.[0] ?? e.message) : 'Gagal')
    } finally {
      setPending(false)
    }
  }

  async function doDelete(w: WilayahRef) {
    setPending(true)
    try {
      await api.delete(`/wilayah/${w.id}`)
      toast.success('Wilayah dihapus')
      qc.invalidateQueries({ queryKey: ['wilayah-tree'] })
      setDeleteTarget(null)
    } catch (e) {
      toast.error(e instanceof ApiError ? e.message : 'Gagal')
    } finally {
      setPending(false)
    }
  }

  function toggleExpand(id: number) {
    setExpanded((s) => {
      const next = new Set(s)
      if (next.has(id)) next.delete(id)
      else next.add(id)
      return next
    })
  }

  function NodeRow({ w, depth }: { w: WilayahRef; depth: number }) {
    const hasChildren = (w.children?.length ?? 0) > 0
    const isOpen = expanded.has(w.id)

    return (
      <>
        <div className="flex items-center gap-2 border-b border-line px-4 py-2.5 hover:bg-slate-50" style={{ paddingLeft: 16 + depth * 24 }}>
          {hasChildren ? (
            <button onClick={() => toggleExpand(w.id)} className="rounded p-0.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
              <ChevronRight size={14} className={`transition-transform ${isOpen ? 'rotate-90' : ''}`} />
            </button>
          ) : (
            <span className="w-[22px]" />
          )}
          <span className={`flex h-7 w-7 items-center justify-center rounded-lg ${w.tingkat === 'Kelurahan' ? 'bg-brand-50 text-brand-600' : w.tingkat === 'RW' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500'}`}>
            {TINGKAT_ICON[w.tingkat]}
          </span>
          <span className="flex-1 text-[13px] font-semibold text-slate-800">{w.nama}</span>
          <span className="rounded-md bg-slate-100 px-1.5 py-0.5 font-mono text-[11px] text-slate-500">{w.kode}</span>
          <StatusBadge status={w.tingkat === 'Kelurahan' ? 'active' : w.tingkat === 'RW' ? 'approved' : 'draft'} label={w.tingkat} />
          <div className="flex gap-1">
            {w.tingkat !== 'RT' && (
              <Button size="sm" variant="ghost" title={`Tambah ${w.tingkat === 'Kelurahan' ? 'RW' : 'RT'} di bawah ini`}
                onClick={() => setModal({ mode: 'create', parentId: w.id, parentTingkat: w.tingkat })}>
                <Plus size={14} />
              </Button>
            )}
            <Button size="sm" variant="ghost" onClick={() => setModal({ mode: 'edit', wilayah: w })}><Pencil size={14} /></Button>
            <Button size="sm" variant="ghost" className="text-rose-500 hover:bg-rose-50" onClick={() => setDeleteTarget(w)}><Trash2 size={14} /></Button>
          </div>
        </div>
        {isOpen && w.children?.map((c) => <NodeRow key={c.id} w={c} depth={depth + 1} />)}
      </>
    )
  }

  return (
    <div className="animate-fade-up">
      <PageHeader title="Struktur Wilayah" subtitle="Hirarki Kelurahan → RW → RT (kode unik, tidak boleh melingkar)"
        actions={<Button onClick={() => setModal({ mode: 'create', parentTingkat: 'none' })}><Plus size={15} /> Tambah Kelurahan</Button>} />

      <Card className="overflow-hidden">
        {isLoading ? (
          <div className="space-y-2 p-4">{Array.from({ length: 6 }).map((_, i) => <Skeleton key={i} className="h-10" />)}</div>
        ) : isError ? (
          <QueryError message={error?.message} onRetry={() => refetch()} />
        ) : (data?.data ?? []).length === 0 ? (
          <EmptyState icon={<Map size={24} />} title="Belum ada wilayah" />
        ) : (
          data!.data.map((kel) => <NodeRow key={kel.id} w={kel} depth={0} />)
        )}
      </Card>

      {modal && (
        <WilayahFormModal modal={modal} onClose={() => setModal(null)}
          onSubmit={(payload) => submit(payload, modal.mode, modal.wilayah?.id)} pending={pending} />
      )}

      <Modal open={!!deleteTarget} onClose={() => setDeleteTarget(null)} title="Hapus Wilayah" size="sm">
        <p className="text-sm text-slate-600">Yakin hapus <strong>{deleteTarget?.nama}</strong>?</p>
        <p className="mt-1 text-xs text-slate-400">Diblokir jika masih ada turunan, user tertugas, atau keluarga berdomisili.</p>
        <div className="mt-5 flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
          <Button variant="danger" disabled={pending} onClick={() => doDelete(deleteTarget!)}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}

function WilayahFormModal({ modal, onClose, onSubmit, pending }: {
  modal: { mode: 'create' | 'edit'; wilayah?: WilayahRef; parentId?: number; parentTingkat?: string }
  onClose: () => void
  onSubmit: (payload: Record<string, unknown>) => void
  pending: boolean
}) {
  // infer tingkat: edit = tingkat existing; create = child dari parent
  const tingkatAwal = modal.mode === 'edit'
    ? modal.wilayah!.tingkat
    : modal.parentTingkat === 'Kelurahan' ? 'RW' : modal.parentTingkat === 'RW' ? 'RT' : 'Kelurahan'

  const [form, setForm] = useState({
    kode: modal.wilayah?.kode ?? '',
    nama: modal.wilayah?.nama?.replace(/^(Kelurahan|RW|RT)\s+/, '') ?? '',
    tingkat: tingkatAwal,
    parent_id: modal.wilayah?.parent?.id ?? modal.parentId ?? null,
  })
  const set = (k: string, v: string) => setForm((f) => ({ ...f, [k]: v }))

  const namaTingkat = form.tingkat
  const prefix = form.tingkat !== 'Kelurahan' ? `${form.tingkat} ` : ''

  return (
    <Modal open onClose={onClose} title={modal.mode === 'create' ? `Tambah ${namaTingkat}` : `Edit ${modal.wilayah!.tingkat}`}
      subtitle={modal.mode === 'create' && modal.parentTingkat && modal.parentTingkat !== 'none' ? `Di bawah ${modal.parentTingkat} terpilih` : undefined} size="sm">
      <form onSubmit={(e) => {
        e.preventDefault()
        onSubmit({
          kode: form.kode,
          nama: prefix + form.nama,
          tingkat: form.tingkat,
          parent_id: form.parent_id,
        })
      }} className="space-y-4">
        <div>
          <Label>Tingkat</Label>
          <Select value={form.tingkat} onChange={(v) => set('tingkat', v)}
            options={[
              { value: 'Kelurahan', label: 'Kelurahan' },
              { value: 'RW', label: 'RW' },
              { value: 'RT', label: 'RT' },
            ]} />
        </div>
        <div>
          <Label>Kode * (unik, mis. {form.tingkat === 'Kelurahan' ? 'BM' : form.tingkat === 'RW' ? '01' : '0101'})</Label>
          <Input value={form.kode} onChange={(e) => set('kode', e.target.value.toUpperCase())} required maxLength={10} className="font-mono" />
        </div>
        <div>
          <Label>Nama * ({prefix ? `tanpa awalan "${form.tingkat}"` : 'nama kelurahan'})</Label>
          <Input value={form.nama} onChange={(e) => set('nama', e.target.value)} required placeholder={form.tingkat === 'Kelurahan' ? 'mis. Bendul Merisi' : 'mis. 01 Bendul Merisi'} />
          <p className="mt-1 text-[11px] text-slate-400">Akan tersimpan sebagai: <strong>{prefix + form.nama || '—'}</strong></p>
        </div>
        <div className="flex justify-end gap-2 border-t border-line pt-4">
          <Button type="button" variant="secondary" onClick={onClose}>Batal</Button>
          <Button type="submit" disabled={pending}>{pending ? 'Menyimpan…' : 'Simpan'}</Button>
        </div>
      </form>
    </Modal>
  )
}
