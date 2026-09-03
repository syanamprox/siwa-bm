'use client'

import { useState, useEffect, useRef } from 'react'
import {
  Home, Plus, RotateCcw, Pencil, Trash2, Users, MapPin, ChevronRight,
  UserPlus, UserMinus, Crown, Settings2, Coins, FileImage, BadgeCheck, BadgeX, Upload,
} from 'lucide-react'
import { useKeluargaList, useKeluarga, useKeluargaMutations, useRtOptions, useWargaList, useAvailableJenisIuran, useConnectIuran, useDisconnectIuran, type KeluargaFilters } from '@/hooks/use-siwa'
import { useAuth } from '@/stores/auth-store'
import { PageHeader } from '@/components/PageHeader'
import { Button, Card, Input, Label, Select, Skeleton, StatusBadge, EmptyState, Textarea } from '@/components/ui/primitives'
import { Modal } from '@/components/ui/Modal'
import { Drawer } from '@/components/ui/Drawer'
import { QueryError } from '@/components/QueryError'
import { api } from '@/lib/api-client'
import { toast } from 'sonner'
import type { Keluarga, WilayahRef } from '@/types'

const STATUS_KELUARGA = ['Tetap', 'Domisili', 'Non Domisili', 'Pendatang']

/** Warna badge per status (Tetap hijau · Domisili biru · Pendatang amber · Non Domisili abu) */
function statusBadge(status?: string | null): string {
  switch (status) {
    case 'Tetap': return 'active'
    case 'Domisili': return 'approved'
    case 'Pendatang': return 'pending'
    default: return 'draft'
  }
}
const HUBUNGAN = ['Kepala Keluarga', 'Suami', 'Istri', 'Anak', 'Menantu', 'Cucu', 'Orang Tua', 'Mertua', 'Famili Lain', 'Lainnya']

export default function KeluargaPage() {
  const [filters, setFilters] = useState<KeluargaFilters>({ per_page: 15 })
  const [searchInput, setSearchInput] = useState('')
  const [modal, setModal] = useState<{ mode: 'create' | 'edit'; keluarga?: Keluarga } | null>(null)
  const [detailId, setDetailId] = useState<number | null>(null)
  const [deleteTarget, setDeleteTarget] = useState<Keluarga | null>(null)

  const { data, isLoading, isFetching, isError, error, refetch } = useKeluargaList(filters)
  const { create, update, remove, verify } = useKeluargaMutations()
  const isAdmin = useAuth((s) => s.user?.role === 'admin')

  // Auto-search: debounce 400ms — pola sama dengan page warga
  useEffect(() => {
    const t = setTimeout(() => {
      setFilters((f) => (f.search === (searchInput || undefined) ? f : { ...f, search: searchInput || undefined, page: 1 }))
    }, 400)
    return () => clearTimeout(t)
  }, [searchInput])

  const hasActiveFilter = Boolean(searchInput || filters.status)

  function resetFilters() {
    setSearchInput('')
    setFilters({ per_page: 15 })
  }

  return (
    <div className="animate-fade-up">
      <PageHeader
        title="Kartu Keluarga"
        subtitle="Data keluarga & domisili (alamat KTP manual + domisili via wilayah)"
        actions={<Button onClick={() => setModal({ mode: 'create' })}><Plus size={15} /> Tambah Keluarga</Button>}
      />

      {/* Filter */}
      <Card className="mb-4 p-4">
        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
          <div className="lg:col-span-2">
            <Input placeholder="Cari no KK / nama kepala keluarga…" value={searchInput}
              onChange={(e) => setSearchInput(e.target.value)} />
          </div>
          <Select value={filters.status ?? ''} onChange={(v) => setFilters((f) => ({ ...f, status: v || undefined, page: 1 }))}
            placeholder="Semua Status" options={STATUS_KELUARGA.map((s) => ({ value: s, label: s }))} />
          {hasActiveFilter && (
            <Button variant="secondary" onClick={resetFilters}><RotateCcw size={14} /> Reset</Button>
          )}
        </div>
      </Card>

      {/* Table */}
      <Card className="overflow-hidden">
        {isLoading ? (
          <div className="space-y-2 p-4">{Array.from({ length: 6 }).map((_, i) => <Skeleton key={i} className="h-12" />)}</div>
        ) : isError ? (
          <QueryError message={error?.message} onRetry={() => refetch()} />
        ) : (data?.data ?? []).length === 0 ? (
          <EmptyState icon={<Home size={24} />} title="Belum ada keluarga" hint="Tambahkan kartu keluarga pertama." />
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-[13px]">
              <thead>
                <tr className="border-b border-line bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-400">
                  <th className="px-4 py-3 font-semibold">No. KK</th>
                  <th className="px-4 py-3 font-semibold">Kepala Keluarga</th>
                  <th className="px-4 py-3 font-semibold">Domisili (RT)</th>
                  <th className="px-4 py-3 font-semibold">Anggota</th>
                  <th className="px-4 py-3 font-semibold">Status</th>
                  <th className="px-4 py-3 text-right font-semibold">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-line">
                {data!.data.map((k) => (
                  <tr key={k.id} className={`cursor-pointer hover:bg-slate-50 ${isFetching ? 'opacity-60' : ''}`}
                    onClick={() => setDetailId(k.id)}>
                    <td className="px-4 py-3 tabular-nums text-slate-600">{k.no_kk}</td>
                    <td className="px-4 py-3 font-semibold text-slate-900">
                      <span className="inline-flex items-center gap-1.5">
                        {k.kepala_keluarga?.nama_lengkap ?? k.nama_kepala_keluarga ?? <span className="text-amber-600">belum ada</span>}
                        {k.is_verified ? (
                          <span className="inline-flex items-center gap-1 text-emerald-600" title={`KK terverifikasi${k.verified_at ? ` ${new Date(k.verified_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}` : ''} — seluruh anggota ikut terverifikasi`}>
                            <BadgeCheck size={14} />
                            <span className="text-[10px] font-medium text-slate-400 tabular-nums">
                              {k.verified_at ? new Date(k.verified_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '—'}
                            </span>
                          </span>
                        ) : (
                          <span className="inline-flex items-center rounded-full border border-dashed border-amber-400 px-1.5 py-0.5 text-[10px] font-semibold text-amber-600" title="Data KK belum diverifikasi petugas">Belum verifikasi</span>
                        )}
                      </span>
                    </td>
                    <td className="px-4 py-3 text-slate-600">
                      <span className="inline-flex items-center gap-1"><MapPin size={12} className="text-slate-300" />{k.wilayah?.nama ?? '-'}</span>
                    </td>
                    <td className="px-4 py-3">
                      <span className="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-700 tabular-nums">
                        <Users size={11} /> {k.anggota_keluarga_count ?? 0}
                      </span>
                    </td>
                    <td className="px-4 py-3">
                      <StatusBadge status={statusBadge(k.status_keluarga)} label={k.status_keluarga} />
                      {k.status_miskin && k.status_miskin !== 'Non' && (
                        <span className={`ml-1.5 inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold uppercase ${k.status_miskin === 'Miskin' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700'}`}>
                          {k.status_miskin === 'Miskin' ? 'Miskin' : 'Pra-Miskin'}
                        </span>
                      )}
                    </td>
                    <td className="px-4 py-3" onClick={(e) => e.stopPropagation()}>
                      <div className="flex justify-end gap-1">
                        {isAdmin && (k.is_verified ? (
                          <Button size="sm" variant="ghost" className="text-amber-500 hover:bg-amber-50" disabled={verify.isPending}
                            onClick={() => verify.mutate({ id: k.id, verified: false })} title="Batalkan verifikasi KK (beserta seluruh anggota)">
                            <BadgeX size={14} />
                          </Button>
                        ) : (
                          <Button size="sm" variant="ghost" className="text-emerald-600 hover:bg-emerald-50" disabled={verify.isPending}
                            onClick={() => verify.mutate({ id: k.id, verified: true })} title="Verifikasi KK beserta seluruh anggota">
                            <BadgeCheck size={14} />
                          </Button>
                        ))}
                        <Button size="sm" variant="ghost" onClick={() => setDetailId(k.id)} title="Detail"><ChevronRight size={14} /></Button>
                        <Button size="sm" variant="ghost" onClick={() => setModal({ mode: 'edit', keluarga: k })} title="Edit"><Pencil size={14} /></Button>
                        <Button size="sm" variant="ghost" className="text-rose-500 hover:bg-rose-50" onClick={() => setDeleteTarget(k)} title="Hapus"><Trash2 size={14} /></Button>
                      </div>
                    </td>
                  </tr>
                ))}
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

      {/* Detail drawer */}
      {detailId && <KeluargaDetail id={detailId} onClose={() => setDetailId(null)} onEdit={(k) => { setDetailId(null); setModal({ mode: 'edit', keluarga: k }) }} />}

      {/* Form modal */}
      {modal && (
        <KeluargaFormModal
          mode={modal.mode}
          keluarga={modal.keluarga}
          onClose={() => setModal(null)}
          onSubmit={(payload) => {
            if (modal.mode === 'create') create.mutate(payload)
            else if (modal.keluarga) update.mutate({ id: modal.keluarga.id, payload })
            setModal(null)
          }}
          pending={create.isPending || update.isPending}
        />
      )}

      {/* Delete */}
      <Modal open={!!deleteTarget} onClose={() => setDeleteTarget(null)} title="Hapus Keluarga" size="sm">
        <p className="text-sm text-slate-600">
          Yakin hapus KK <strong>{deleteTarget?.no_kk}</strong>? Semua anggota ikut dihapus (soft delete). Riwayat iuran dipertahankan untuk audit.
        </p>
        <div className="mt-5 flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
          <Button variant="danger" onClick={() => { remove.mutate(deleteTarget!.id); setDeleteTarget(null) }}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}

/* ═══════════ Detail drawer ═══════════ */
/* ═══════════ Tombol dokumen KK — link bertoken ═══════════ */

/**
 * File dokumen terkunci (dokumen KK / foto rumah) — URL polos ditolak 404 middleware.
 * Klik = minta token ke backend (scoped + TTL 5 menit), lalu buka tab baru.
 */
function DocumentButton({ path, label, failLabel }: { path: string; label: string; failLabel?: string }) {
  const [loading, setLoading] = useState(false)

  async function open() {
    setLoading(true)
    try {
      const res = await api.get<{ data: { url: string } }>(`/keluarga/doc-token?path=${encodeURIComponent(path)}`)
      window.open(res.data.url, '_blank', 'noreferrer')
    } catch {
      toast.error(failLabel ?? 'Tidak dapat membuka dokumen. Coba lagi.')
    } finally {
      setLoading(false)
    }
  }

  return (
    <button type="button" onClick={open} disabled={loading}
      className="inline-flex items-center gap-1.5 rounded-lg bg-brand-50 px-3 py-1.5 text-[12px] font-semibold text-brand-700 transition hover:bg-brand-100 disabled:opacity-60">
      <FileImage size={13} /> {loading ? 'Menyiapkan…' : label}
    </button>
  )
}

/* ═══════════ Drawer detail ═══════════ */
function KeluargaDetail({ id, onClose, onEdit }: { id: number; onClose: () => void; onEdit: (k: Keluarga) => void }) {
  const { data, isLoading, isError, error, refetch } = useKeluarga(id)
  const { addMember, removeMember, updateStatus, uploadFotoRumah, deleteFotoRumah } = useKeluargaMutations()
  const { data: availableJenis } = useAvailableJenisIuran(id)
  const connectIuran = useConnectIuran()
  const disconnectIuran = useDisconnectIuran()
  const [memberModal, setMemberModal] = useState(false)
  const [statusModal, setStatusModal] = useState(false)
  const [iuranModal, setIuranModal] = useState(false)
  const [wargaSearch, setWargaSearch] = useState('')
  const fotoRumahInput = useRef<HTMLInputElement>(null)

  const kel = data?.data
  const { data: wargaResults } = useWargaList({ search: wargaSearch.length >= 3 ? wargaSearch : undefined, per_page: 5, status_kk: 'tanpa_kk' })

  return (
    <Drawer open onClose={onClose}>
      {isLoading ? (
        <div className="space-y-3 p-6">{Array.from({ length: 6 }).map((_, i) => <Skeleton key={i} className="h-16" />)}</div>
      ) : isError || !kel ? (
        <QueryError message={error?.message} onRetry={() => refetch()} />
      ) : (
          <>
            {/* Header */}
            <div className="sticky top-0 z-10 flex items-start justify-between border-b border-line bg-white/95 px-6 py-4 backdrop-blur">
              <div>
                <div className="flex items-center gap-2">
                  <h2 className="text-lg font-extrabold tracking-tight text-slate-900 tabular-nums">{kel.no_kk}</h2>
                  <StatusBadge status={statusBadge(kel.status_keluarga)} label={kel.status_keluarga} />
                </div>
                <p className="mt-0.5 text-xs text-slate-500">
                  Kepala: {kel.kepala_keluarga?.nama_lengkap ?? kel.nama_kepala_keluarga ?? '—'} · {kel.anggota_keluarga?.length ?? 0} anggota
                </p>
              </div>
              <div className="flex gap-1.5">
                <Button size="sm" variant="secondary" onClick={() => setStatusModal(true)}><Settings2 size={13} /> Status</Button>
                <Button size="sm" variant="secondary" onClick={() => onEdit(kel)}><Pencil size={13} /> Edit</Button>
              </div>
            </div>

            <div className="space-y-6 p-6">
              {/* Alamat */}
              <section>
                <h3 className="mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Alamat</h3>
                <Card className="p-4">
                  <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                      <p className="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Alamat KTP</p>
                      <p className="mt-1 text-[13px] text-slate-800">{kel.alamat_kk}</p>
                      <p className="text-xs text-slate-500">RT {kel.rt_kk ?? '—'} / RW {kel.rw_kk ?? '—'}, {kel.kelurahan_kk ?? '—'}, {kel.kecamatan_kk ?? '—'}</p>
                    </div>
                    <div>
                      <p className="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Domisili</p>
                      <p className="mt-1 text-[13px] text-slate-800">{kel.alamat_domisili ?? '—'}</p>
                      <p className="text-xs text-slate-500">
                        {kel.wilayah?.nama} · {kel.wilayah?.parent?.nama} · {kel.wilayah?.parent?.parent?.nama}
                      </p>
                    </div>
                  </div>
                  {kel.foto_kk && (
                    <DocumentButton path={kel.foto_kk} label="Lihat Dokumen KK" />
                  )}

                  {/* Foto rumah + penghuni — upload petugas, untuk verifikasi visual saat turun lapangan */}
                  <div className="mt-4 border-t border-line pt-3">
                    <p className="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Foto Rumah &amp; Penghuni</p>
                    <p className="mt-0.5 text-xs text-slate-500">Foto rumah bersama penghuninya — agar petugas mengenali keluarga aslinya.</p>
                    <div className="mt-2 flex flex-wrap items-center gap-2">
                      {kel.foto_rumah ? (
                        <>
                          <DocumentButton path={kel.foto_rumah} label="Lihat Foto Rumah" failLabel="Tidak dapat membuka foto rumah. Coba lagi." />
                          <Button size="sm" variant="secondary" disabled={uploadFotoRumah.isPending} onClick={() => fotoRumahInput.current?.click()}>
                            <Upload size={13} /> Ganti
                          </Button>
                          <Button size="sm" variant="secondary" disabled={deleteFotoRumah.isPending} onClick={() => deleteFotoRumah.mutate(kel.id)}>
                            <Trash2 size={13} /> Hapus
                          </Button>
                        </>
                      ) : (
                        <Button size="sm" variant="secondary" disabled={uploadFotoRumah.isPending} onClick={() => fotoRumahInput.current?.click()}>
                          <Upload size={13} /> {uploadFotoRumah.isPending ? 'Mengunggah…' : 'Upload Foto'}
                        </Button>
                      )}
                    </div>
                    <input
                      ref={fotoRumahInput} type="file" accept="image/jpeg,image/png,image/webp" className="hidden"
                      onChange={(e) => {
                        const file = e.target.files?.[0]
                        if (file) uploadFotoRumah.mutate({ id: kel.id, file })
                        e.target.value = '' // file sama bisa dipilih ulang setelah gagal
                      }}
                    />
                  </div>
                </Card>
              </section>

              {/* Anggota */}
              <section>
                <div className="mb-2 flex items-center justify-between">
                  <h3 className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Anggota Keluarga</h3>
                  <Button size="sm" variant="secondary" onClick={() => setMemberModal(true)}><UserPlus size={13} /> Tambah</Button>
                </div>
                <Card className="divide-y divide-line">
                  {(kel.anggota_keluarga ?? []).map((w) => (
                    <div key={w.id} className="flex items-center gap-3 px-4 py-3">
                      {w.id === kel.kepala_keluarga_id ? (
                        <Crown size={15} className="text-amber-500" />
                      ) : (
                        <span className="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-[11px] font-bold text-slate-500">
                          {w.jenis_kelamin === 'L' ? 'L' : 'P'}
                        </span>
                      )}
                      <div className="min-w-0 flex-1">
                        <p className="truncate text-[13px] font-semibold text-slate-800">
                          {w.nama_lengkap}
                          {w.id === kel.kepala_keluarga_id && <span className="ml-1.5 text-[10px] font-bold uppercase text-amber-600">Kepala</span>}
                        </p>
                        <p className="text-[11px] text-slate-400 tabular-nums">{w.nik} · {w.hubungan_keluarga}</p>
                      </div>
                      {w.id !== kel.kepala_keluarga_id && (
                        <Button size="sm" variant="ghost" className="text-rose-400 hover:bg-rose-50"
                          onClick={() => removeMember.mutate({ id: kel.id, warga_id: w.id })} title="Keluarkan">
                          <UserMinus size={14} />
                        </Button>
                      )}
                    </div>
                  ))}
                  {(kel.anggota_keluarga ?? []).length === 0 && (
                    <p className="px-4 py-6 text-center text-sm text-slate-400">Belum ada anggota</p>
                  )}
                </Card>
              </section>

              {/* Iuran terhubung */}
              <section>
                <div className="mb-2 flex items-center justify-between">
                  <h3 className="text-[11px] font-bold uppercase tracking-wider text-slate-400">Iuran Terhubung</h3>
                  <Button size="sm" variant="secondary" onClick={() => setIuranModal(true)}><Coins size={13} /> Hubungkan</Button>
                </div>
                <Card className="divide-y divide-line">
                  {(kel.keluarga_iuran ?? []).map((c) => (
                    <div key={c.id} className="flex items-center gap-3 px-4 py-3">
                      <Coins size={15} className={c.status_aktif ? 'text-emerald-500' : 'text-slate-300'} />
                      <div className="min-w-0 flex-1">
                        <p className="text-[13px] font-semibold text-slate-800">{c.jenis_iuran?.nama}</p>
                        <p className="text-[11px] text-slate-400">
                          {c.nominal_custom ? `Custom ${c.nominal_custom.toLocaleString('id-ID')}` : `Default ${(c.jenis_iuran?.jumlah ?? 0).toLocaleString('id-ID')}`}
                          {' · '}{c.jenis_iuran?.periode}
                        </p>
                      </div>
                      <StatusBadge status={c.status_aktif ? 'active' : 'paused'} label={c.status_aktif ? 'Aktif' : 'Nonaktif'} />
                      <Button size="sm" variant="ghost" className="text-rose-400 hover:bg-rose-50" onClick={() => disconnectIuran.mutate(c.id)}>
                        <Trash2 size={13} />
                      </Button>
                    </div>
                  ))}
                  {(kel.keluarga_iuran ?? []).length === 0 && (
                    <p className="px-4 py-6 text-center text-sm text-slate-400">Belum terhubung ke jenis iuran apapun</p>
                  )}
                </Card>
              </section>
            </div>
          </>
        )}

      {/* Add member modal */}
      <Modal open={memberModal} onClose={() => setMemberModal(false)} title="Tambah Anggota" subtitle="Warga tanpa KK saja yang muncul">
        <div className="space-y-4">
          <Input placeholder="Cari nama/NIK warga tanpa KK… (min 3 huruf)" value={wargaSearch} onChange={(e) => setWargaSearch(e.target.value)} />
          <div className="max-h-56 space-y-1 overflow-y-auto">
            {(wargaResults?.data ?? []).map((w) => (
              <div key={w.id} className="flex items-center justify-between rounded-xl border border-line px-3 py-2">
                <div>
                  <p className="text-[13px] font-semibold text-slate-800">{w.nama_lengkap}</p>
                  <p className="text-[11px] text-slate-400 tabular-nums">{w.nik}</p>
                </div>
                <Select
                  size="sm"
                  placeholder="Hubungan…"
                  options={HUBUNGAN.map((h) => ({ value: h, label: h }))}
                  onChange={(v) => {
                    addMember.mutate({ id, warga_id: w.id, hubungan_keluarga: v })
                    setMemberModal(false)
                  }}
                />
              </div>
            ))}
            {wargaSearch.length >= 3 && (wargaResults?.data ?? []).length === 0 && (
              <p className="py-4 text-center text-sm text-slate-400">Tidak ada warga tanpa KK yang cocok</p>
            )}
          </div>
        </div>
      </Modal>

      {/* Status modal */}
      <StatusModal open={statusModal} onClose={() => setStatusModal(false)} keluarga={kel}
        onSubmit={(status, ket) => { updateStatus.mutate({ id, status_keluarga: status, keterangan_status: ket }); setStatusModal(false) }} />

      {/* Connect iuran modal */}
      <Modal open={iuranModal} onClose={() => setIuranModal(false)} title="Hubungkan Iuran" subtitle="Jenis iuran aktif yang belum terhubung">
        <div className="space-y-2">
          {(availableJenis?.data ?? []).map((j) => (
            <div key={j.id} className="flex items-center justify-between rounded-xl border border-line px-3 py-2.5">
              <div>
                <p className="text-[13px] font-semibold text-slate-800">{j.nama}</p>
                <p className="text-[11px] text-slate-400">Default {j.jumlah.toLocaleString('id-ID')} · {j.periode}</p>
              </div>
              <Button size="sm" onClick={() => { connectIuran.mutate({ keluargaId: id, jenis_iuran_id: j.id }); setIuranModal(false) }}>
                Hubungkan
              </Button>
            </div>
          ))}
          {(availableJenis?.data ?? []).length === 0 && (
            <p className="py-4 text-center text-sm text-slate-400">Semua jenis iuran sudah terhubung</p>
          )}
        </div>
      </Modal>
    </Drawer>
  )
}

function StatusModal({ open, onClose, keluarga, onSubmit }: {
  open: boolean; onClose: () => void; keluarga?: Keluarga | null
  onSubmit: (status: string, ket?: string) => void
}) {
  const [status, setStatus] = useState(keluarga?.status_keluarga ?? 'Tetap')
  const [ket, setKet] = useState(keluarga?.keterangan_status ?? '')

  return (
    <Modal open={open} onClose={onClose} title="Ubah Status Keluarga" size="sm">
      <div className="space-y-4">
        <div>
          <Label>Status</Label>
          <Select value={status} onChange={setStatus} options={STATUS_KELUARGA.map((s) => ({ value: s, label: s }))} />
        </div>
        <div>
          <Label>Keterangan</Label>
          <Textarea value={ket} onChange={(e) => setKet(e.target.value)} rows={2} placeholder="opsional…" />
        </div>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={onClose}>Batal</Button>
          <Button onClick={() => onSubmit(status, ket || undefined)}>Simpan</Button>
        </div>
      </div>
    </Modal>
  )
}

/* ═══════════ Form modal ═══════════ */
function KeluargaFormModal({ mode, keluarga, onClose, onSubmit, pending }: {
  mode: 'create' | 'edit'
  keluarga?: Keluarga
  onClose: () => void
  onSubmit: (payload: Record<string, unknown>) => void
  pending: boolean
}) {
  const { data: rtTree } = useRtOptions()
  const [form, setForm] = useState({
    no_kk: keluarga?.no_kk ?? '',
    alamat_kk: keluarga?.alamat_kk ?? '',
    rt_kk: keluarga?.rt_kk ?? '',
    rw_kk: keluarga?.rw_kk ?? '',
    kelurahan_kk: keluarga?.kelurahan_kk ?? 'Bendul Merisi',
    kecamatan_kk: keluarga?.kecamatan_kk ?? 'Wonocolo',
    kabupaten_kk: keluarga?.kabupaten_kk ?? 'Kota Surabaya',
    provinsi_kk: keluarga?.provinsi_kk ?? 'Jawa Timur',
    alamat_domisili: keluarga?.alamat_domisili ?? '',
    rt_id: keluarga?.rt_id ? String(keluarga.rt_id) : '',
    status_keluarga: keluarga?.status_keluarga ?? 'Tetap',
    kepala_keluarga_id: keluarga?.kepala_keluarga_id ? String(keluarga.kepala_keluarga_id) : '',
  })
  const set = (k: string, v: string) => setForm((f) => ({ ...f, [k]: v }))

  const rtOptions = (rtTree?.data ?? []).map((rt: WilayahRef) => ({ value: String(rt.id), label: `${rt.nama} (${rt.kode})` }))

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault()
    const payload: Record<string, unknown> = {
      ...Object.fromEntries(Object.entries(form).filter(([, v]) => v !== '')),
      rt_id: form.rt_id ? Number(form.rt_id) : undefined,
      kepala_keluarga_id: form.kepala_keluarga_id ? Number(form.kepala_keluarga_id) : null,
    }
    if (mode === 'edit') delete (payload as Record<string, unknown>).kepala_keluarga_id // ganti kepala via anggota UI
    onSubmit(payload)
  }

  return (
    <Modal open onClose={onClose} title={mode === 'create' ? 'Tambah Keluarga' : 'Edit Keluarga'}
      subtitle={mode === 'create' ? 'Anggota dapat ditambahkan setelah KK dibuat' : keluarga?.no_kk} size="lg">
      <form onSubmit={handleSubmit} className="space-y-5">
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <Label>No. KK *</Label>
            <Input value={form.no_kk} onChange={(e) => set('no_kk', e.target.value)} required maxLength={16} placeholder="16 digit" className="tabular-nums" />
          </div>
          <div>
            <Label>RT Domisili (wilayah sistem) *</Label>
            <Select value={form.rt_id} onChange={(v) => set('rt_id', v)} placeholder="Pilih RT…" searchable options={rtOptions} />
          </div>
          <div className="sm:col-span-2">
            <Label>Alamat KTP (sesuai KK) *</Label>
            <Input value={form.alamat_kk} onChange={(e) => set('alamat_kk', e.target.value)} required />
          </div>
          <div>
            <Label>RT (KTP)</Label>
            <Input value={form.rt_kk} onChange={(e) => set('rt_kk', e.target.value)} />
          </div>
          <div>
            <Label>RW (KTP)</Label>
            <Input value={form.rw_kk} onChange={(e) => set('rw_kk', e.target.value)} />
          </div>
          <div>
            <Label>Kelurahan (KTP)</Label>
            <Input value={form.kelurahan_kk} onChange={(e) => set('kelurahan_kk', e.target.value)} />
          </div>
          <div>
            <Label>Kecamatan (KTP)</Label>
            <Input value={form.kecamatan_kk} onChange={(e) => set('kecamatan_kk', e.target.value)} />
          </div>
          <div className="sm:col-span-2">
            <Label>Alamat Domisili (jalan saja)</Label>
            <Input value={form.alamat_domisili} onChange={(e) => set('alamat_domisili', e.target.value)} />
          </div>
          <div>
            <Label>Status Keluarga</Label>
            <Select value={form.status_keluarga} onChange={(v) => set('status_keluarga', v)}
              options={STATUS_KELUARGA.map((s) => ({ value: s, label: s }))} />
          </div>
        </div>
        <div className="flex justify-end gap-2 border-t border-line pt-4">
          <Button type="button" variant="secondary" onClick={onClose}>Batal</Button>
          <Button type="submit" disabled={pending}>{pending ? 'Menyimpan…' : 'Simpan'}</Button>
        </div>
      </form>
    </Modal>
  )
}
