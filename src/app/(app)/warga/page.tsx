'use client'

import { useState } from 'react'
import { Users, Plus, Search, Pencil, Trash2, User, FileImage } from 'lucide-react'
import { toast } from 'sonner'
import { useWargaList, useWargaMutations, useWargaStats, type WargaFilters } from '@/hooks/use-siwa'
import { PageHeader } from '@/components/PageHeader'
import { Button, Card, Input, Label, Select, Skeleton, StatusBadge, EmptyState } from '@/components/ui/primitives'
import { Modal } from '@/components/ui/Modal'
import type { Warga } from '@/types'

const AGAMA = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']
const PENDIDIKAN = ['Tidak Sekolah', 'SD/sederajat', 'SMP/sederajat', 'SMA/sederajat', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3']
const PERKAWINAN = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']
const HUBUNGAN = ['Kepala Keluarga', 'Suami', 'Istri', 'Anak', 'Menantu', 'Cucu', 'Orang Tua', 'Mertua', 'Famili Lain', 'Lainnya']

export default function WargaPage() {
  const [filters, setFilters] = useState<WargaFilters>({ per_page: 15 })
  const [searchInput, setSearchInput] = useState('')
  const [modal, setModal] = useState<{ mode: 'create' | 'edit'; warga?: Warga } | null>(null)
  const [deleteTarget, setDeleteTarget] = useState<Warga | null>(null)

  const { data, isLoading, isFetching } = useWargaList(filters)
  const { data: stats } = useWargaStats()
  const { create, update, remove } = useWargaMutations()

  function applySearch() {
    setFilters((f) => ({ ...f, search: searchInput || undefined, page: 1 }))
  }

  return (
    <div className="animate-fade-up">
      <PageHeader
        title="Data Warga"
        subtitle={`${stats?.data?.total_warga ?? '—'} warga terdaftar · ${stats?.data?.warga_dengan_kk ?? '—'} dalam KK`}
        actions={
          <Button onClick={() => setModal({ mode: 'create' })}>
            <Plus size={15} /> Tambah Warga
          </Button>
        }
      />

      {/* Filter bar */}
      <Card className="mb-4 p-4">
        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
          <div className="lg:col-span-2">
            <Input
              placeholder="Cari NIK / nama / no KK…"
              value={searchInput}
              onChange={(e) => setSearchInput(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && applySearch()}
            />
          </div>
          <Select
            value={filters.jenis_kelamin ?? ''}
            onChange={(v) => setFilters((f) => ({ ...f, jenis_kelamin: v || undefined, page: 1 }))}
            placeholder="Jenis Kelamin"
            options={[
              { value: 'L', label: 'Laki-laki' },
              { value: 'P', label: 'Perempuan' },
            ]}
          />
          <Select
            value={filters.agama ?? ''}
            onChange={(v) => setFilters((f) => ({ ...f, agama: v || undefined, page: 1 }))}
            placeholder="Agama"
            options={AGAMA.map((a) => ({ value: a, label: a }))}
          />
          <Button variant="secondary" onClick={applySearch}>
            <Search size={14} /> Cari
          </Button>
        </div>
      </Card>

      {/* Table */}
      <Card className="overflow-hidden">
        {isLoading ? (
          <div className="space-y-2 p-4">{Array.from({ length: 8 }).map((_, i) => <Skeleton key={i} className="h-12" />)}</div>
        ) : (data?.data ?? []).length === 0 ? (
          <EmptyState icon={<Users size={24} />} title="Belum ada data warga" hint="Tambahkan warga pertama atau ubah filter pencarian." />
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-[13px]">
              <thead>
                <tr className="border-b border-line bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-400">
                  <th className="px-4 py-3 font-semibold">NIK</th>
                  <th className="px-4 py-3 font-semibold">Nama</th>
                  <th className="px-4 py-3 font-semibold">L/P</th>
                  <th className="px-4 py-3 font-semibold">Umur</th>
                  <th className="px-4 py-3 font-semibold">Pekerjaan</th>
                  <th className="px-4 py-3 font-semibold">Keluarga</th>
                  <th className="px-4 py-3 font-semibold">Hubungan</th>
                  <th className="px-4 py-3 text-right font-semibold">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-line">
                {data!.data.map((w) => (
                  <tr key={w.id} className={`hover:bg-slate-50 ${isFetching ? 'opacity-60' : ''}`}>
                    <td className="px-4 py-3 tabular-nums text-slate-600">{w.nik}</td>
                    <td className="px-4 py-3 font-semibold text-slate-900">{w.nama_lengkap}</td>
                    <td className="px-4 py-3">
                      <span className={`rounded-full px-2 py-0.5 text-[11px] font-bold ${w.jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700'}`}>
                        {w.jenis_kelamin}
                      </span>
                    </td>
                    <td className="px-4 py-3 tabular-nums text-slate-600">{w.umur ?? '—'}</td>
                    <td className="px-4 py-3 text-slate-600">{w.pekerjaan}</td>
                    <td className="px-4 py-3 text-slate-600">
                      {w.keluarga ? `${w.keluarga.no_kk.slice(-4)} · ${w.keluarga.wilayah?.nama ?? ''}` : <span className="text-slate-300">tanpa KK</span>}
                    </td>
                    <td className="px-4 py-3">
                      <StatusBadge status={w.hubungan_keluarga === 'Kepala Keluarga' ? 'active' : undefined} />
                      <span className="ml-1.5 text-slate-600">{w.hubungan_keluarga}</span>
                    </td>
                    <td className="px-4 py-3">
                      <div className="flex justify-end gap-1">
                        <Button size="sm" variant="ghost" onClick={() => setModal({ mode: 'edit', warga: w })} title="Edit">
                          <Pencil size={14} />
                        </Button>
                        <Button size="sm" variant="ghost" className="text-rose-500 hover:bg-rose-50" onClick={() => setDeleteTarget(w)} title="Hapus">
                          <Trash2 size={14} />
                        </Button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        {/* Pagination */}
        {data && data.meta.last_page > 1 && (
          <div className="flex items-center justify-between border-t border-line px-4 py-3">
            <span className="text-xs text-slate-500">
              Hal. {data.meta.current_page} / {data.meta.last_page} · {data.meta.total} data
            </span>
            <div className="flex gap-2">
              <Button size="sm" variant="secondary" disabled={data.meta.current_page <= 1}
                onClick={() => setFilters((f) => ({ ...f, page: (f.page ?? 1) - 1 }))}>
                Sebelumnya
              </Button>
              <Button size="sm" variant="secondary" disabled={data.meta.current_page >= data.meta.last_page}
                onClick={() => setFilters((f) => ({ ...f, page: (f.page ?? 1) + 1 }))}>
                Berikutnya
              </Button>
            </div>
          </div>
        )}
      </Card>

      {/* Form modal */}
      {modal && (
        <WargaFormModal
          mode={modal.mode}
          warga={modal.warga}
          onClose={() => setModal(null)}
          onSubmit={(payload) => {
            if (modal.mode === 'create') create.mutate(payload)
            else if (modal.warga) update.mutate({ id: modal.warga.id, payload })
            setModal(null)
          }}
          pending={create.isPending || update.isPending}
        />
      )}

      {/* Delete confirm */}
      <Modal open={!!deleteTarget} onClose={() => setDeleteTarget(null)} title="Hapus Warga" size="sm">
        <p className="text-sm text-slate-600">
          Yakin hapus <strong>{deleteTarget?.nama_lengkap}</strong> (NIK {deleteTarget?.nik})?
          Data dipindah ke tong sampah (soft delete).
        </p>
        <div className="mt-5 flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
          <Button variant="danger" onClick={() => { remove.mutate(deleteTarget!.id); setDeleteTarget(null) }}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}

/* ═══════════ Form modal ═══════════ */
function WargaFormModal({ mode, warga, onClose, onSubmit, pending }: {
  mode: 'create' | 'edit'
  warga?: Warga
  onClose: () => void
  onSubmit: (payload: Record<string, string>) => void
  pending: boolean
}) {
  const [form, setForm] = useState({
    nik: warga?.nik ?? '',
    nama_lengkap: warga?.nama_lengkap ?? '',
    tempat_lahir: warga?.tempat_lahir ?? '',
    tanggal_lahir: warga?.tanggal_lahir?.slice(0, 10) ?? '',
    jenis_kelamin: warga?.jenis_kelamin ?? 'L',
    golongan_darah: warga?.golongan_darah ?? '',
    agama: warga?.agama ?? 'Islam',
    status_perkawinan: warga?.status_perkawinan ?? 'Belum Kawin',
    pekerjaan: warga?.pekerjaan ?? '',
    pendidikan_terakhir: warga?.pendidikan_terakhir ?? 'SMA/sederajat',
    kewarganegaraan: warga?.kewarganegaraan ?? 'WNI',
    hubungan_keluarga: warga?.hubungan_keluarga ?? 'Anak',
    no_telepon: warga?.no_telepon ?? '',
    nama_ayah: warga?.nama_ayah ?? '',
    nama_ibu: warga?.nama_ibu ?? '',
  })
  const set = (k: string, v: string) => setForm((f) => ({ ...f, [k]: v }))

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault()
    const payload: Record<string, string> = {}
    Object.entries(form).forEach(([k, v]) => { if (v !== '') payload[k] = v })
    if (mode === 'edit' && foto) {
      // foto dikirim terpisah via multipart — defer ke onSubmit caller
    }
    onSubmit(payload)
  }
  const [foto, setFoto] = useState<File | null>(null)

  return (
    <Modal
      open
      onClose={onClose}
      title={mode === 'create' ? 'Tambah Warga' : 'Edit Warga'}
      subtitle={mode === 'edit' ? `${warga?.nik} — ${warga?.nama_lengkap}` : 'Data kependudukan sesuai KTP'}
      size="xl"
    >
      <form onSubmit={handleSubmit} className="space-y-5">
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <div>
            <Label>NIK *</Label>
            <Input value={form.nik} onChange={(e) => set('nik', e.target.value)} required maxLength={16} placeholder="16 digit" className="tabular-nums" />
          </div>
          <div className="lg:col-span-2">
            <Label>Nama Lengkap *</Label>
            <Input value={form.nama_lengkap} onChange={(e) => set('nama_lengkap', e.target.value)} required />
          </div>
          <div>
            <Label>Tempat Lahir *</Label>
            <Input value={form.tempat_lahir} onChange={(e) => set('tempat_lahir', e.target.value)} required />
          </div>
          <div>
            <Label>Tanggal Lahir *</Label>
            <Input type="date" value={form.tanggal_lahir} onChange={(e) => set('tanggal_lahir', e.target.value)} required />
          </div>
          <div>
            <Label>Jenis Kelamin *</Label>
            <Select value={form.jenis_kelamin} onChange={(v) => set('jenis_kelamin', v)}
              options={[{ value: 'L', label: 'Laki-laki' }, { value: 'P', label: 'Perempuan' }]} />
          </div>
          <div>
            <Label>Golongan Darah</Label>
            <Select value={form.golongan_darah} onChange={(v) => set('golongan_darah', v)} placeholder="—"
              options={['A', 'B', 'AB', 'O', 'A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-', 'Tidak Tahu'].map((g) => ({ value: g, label: g }))} />
          </div>
          <div>
            <Label>Agama *</Label>
            <Select value={form.agama} onChange={(v) => set('agama', v)} options={AGAMA.map((a) => ({ value: a, label: a }))} />
          </div>
          <div>
            <Label>Status Perkawinan *</Label>
            <Select value={form.status_perkawinan} onChange={(v) => set('status_perkawinan', v)} options={PERKAWINAN.map((p) => ({ value: p, label: p }))} />
          </div>
          <div>
            <Label>Pekerjaan *</Label>
            <Input value={form.pekerjaan} onChange={(e) => set('pekerjaan', e.target.value)} required />
          </div>
          <div>
            <Label>Pendidikan Terakhir *</Label>
            <Select value={form.pendidikan_terakhir} onChange={(v) => set('pendidikan_terakhir', v)} options={PENDIDIKAN.map((p) => ({ value: p, label: p }))} />
          </div>
          <div>
            <Label>Hubungan Keluarga *</Label>
            <Select value={form.hubungan_keluarga} onChange={(v) => set('hubungan_keluarga', v)} options={HUBUNGAN.map((h) => ({ value: h, label: h }))} />
          </div>
          <div>
            <Label>No. Telepon</Label>
            <Input value={form.no_telepon} onChange={(e) => set('no_telepon', e.target.value)} />
          </div>
          <div>
            <Label>Nama Ayah</Label>
            <Input value={form.nama_ayah} onChange={(e) => set('nama_ayah', e.target.value)} />
          </div>
          <div>
            <Label>Nama Ibu</Label>
            <Input value={form.nama_ibu} onChange={(e) => set('nama_ibu', e.target.value)} />
          </div>
          {mode === 'edit' && (
            <div>
              <Label>Foto KTP</Label>
              <Input type="file" accept="image/jpeg,image/png" onChange={(e) => setFoto(e.target.files?.[0] ?? null)} />
              {warga?.foto_ktp && !foto && <p className="mt-1 text-[11px] text-slate-400">Sudah ada foto — biarkan kosong untuk pertahankan</p>}
            </div>
          )}
        </div>

        <div className="flex justify-end gap-2 border-t border-line pt-4">
          <Button type="button" variant="secondary" onClick={onClose}>Batal</Button>
          <Button type="submit" disabled={pending}>{pending ? 'Menyimpan…' : 'Simpan'}</Button>
        </div>
      </form>
    </Modal>
  )
}
