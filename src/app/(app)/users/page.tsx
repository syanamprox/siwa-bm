'use client'

import { useState } from 'react'
import { ShieldCheck, Plus, Pencil, Trash2, Power, KeyRound, Search } from 'lucide-react'
import { toast } from 'sonner'
import { useQueryClient } from '@tanstack/react-query'
import { api, ApiError } from '@/lib/api-client'
import { useUserList, useWilayahTree } from '@/hooks/use-siwa'
import { useAuth } from '@/stores/auth-store'
import { PageHeader } from '@/components/PageHeader'
import { Button, Card, Input, Label, Select, Skeleton, StatusBadge, EmptyState, Avatar } from '@/components/ui/primitives'
import { Modal } from '@/components/ui/Modal'
import { QueryError } from '@/components/QueryError'
import type { SiwaUser, WilayahRef } from '@/types'

const ROLE_LABEL: Record<string, string> = { admin: 'Admin', camat: 'Camat', lurah: 'Lurah', rw: 'Ketua RW', rt: 'Ketua RT' }

export default function UsersPage() {
  const me = useAuth((s) => s.user)
  const [filters, setFilters] = useState<{ search?: string; role?: string; status?: string; page?: number }>({})
  const [searchInput, setSearchInput] = useState('')
  const [modal, setModal] = useState<{ mode: 'create' | 'edit'; user?: SiwaUser } | null>(null)
  const [deleteTarget, setDeleteTarget] = useState<SiwaUser | null>(null)
  const [resetTarget, setResetTarget] = useState<SiwaUser | null>(null)
  const [newPassword, setNewPassword] = useState<string | null>(null)
  const [pending, setPending] = useState(false)
  const qc = useQueryClient()

  const { data, isLoading, isError, error, refetch } = useUserList(filters)

  async function act(fn: () => Promise<unknown>, success: string) {
    setPending(true)
    try {
      await fn()
      toast.success(success)
      qc.invalidateQueries({ queryKey: ['users'] })
      return true
    } catch (e) {
      toast.error(e instanceof ApiError ? (Object.values(e.errors ?? {})[0]?.[0] ?? e.message) : 'Gagal')
      return false
    } finally {
      setPending(false)
    }
  }

  return (
    <div className="animate-fade-up">
      <PageHeader title="Pengguna" subtitle="Akun petugas — admin, lurah, RW, RT beserta penugasan wilayah"
        actions={<Button onClick={() => setModal({ mode: 'create' })}><Plus size={15} /> Tambah User</Button>} />

      <Card className="mb-4 p-4">
        <div className="grid grid-cols-1 gap-3 sm:grid-cols-4">
          <div className="sm:col-span-2">
            <Input placeholder="Cari nama / username…" value={searchInput} onChange={(e) => setSearchInput(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && setFilters((f) => ({ ...f, search: searchInput || undefined, page: 1 }))} />
          </div>
          <Select value={filters.role ?? ''} onChange={(v) => setFilters((f) => ({ ...f, role: v || undefined, page: 1 }))} placeholder="Semua Role"
            options={Object.entries(ROLE_LABEL).map(([v, l]) => ({ value: v, label: l }))} />
          <Select value={filters.status ?? ''} onChange={(v) => setFilters((f) => ({ ...f, status: v || undefined, page: 1 }))} placeholder="Semua Status"
            options={[{ value: '1', label: 'Aktif' }, { value: '0', label: 'Nonaktif' }]} />
        </div>
      </Card>

      <Card className="overflow-hidden">
        {isLoading ? (
          <div className="space-y-2 p-4">{Array.from({ length: 6 }).map((_, i) => <Skeleton key={i} className="h-12" />)}</div>
        ) : isError ? (
          <QueryError message={error?.message} onRetry={() => refetch()} />
        ) : (data?.data ?? []).length === 0 ? (
          <EmptyState icon={<ShieldCheck size={24} />} title="Tidak ada user" />
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-[13px]">
              <thead>
                <tr className="border-b border-line bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-400">
                  <th className="px-4 py-3 font-semibold">User</th>
                  <th className="px-4 py-3 font-semibold">Role</th>
                  <th className="px-4 py-3 font-semibold">Wilayah Tugas</th>
                  <th className="px-4 py-3 font-semibold">Status</th>
                  <th className="px-4 py-3 text-right font-semibold">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-line">
                {data!.data.map((u) => {
                  const isMe = u.id === me?.id
                  return (
                    <tr key={u.id} className="hover:bg-slate-50">
                      <td className="px-4 py-3">
                        <div className="flex items-center gap-3">
                          <Avatar name={u.name} src={u.avatar} size="sm" />
                          <div>
                            <p className="font-semibold text-slate-900">{u.name}{isMe && <span className="ml-1.5 text-[10px] font-bold uppercase text-brand-600">Anda</span>}</p>
                            <p className="text-[11px] text-slate-400">@{u.username}</p>
                          </div>
                        </div>
                      </td>
                      <td className="px-4 py-3"><StatusBadge status={u.role === 'admin' ? 'active' : 'draft'} label={ROLE_LABEL[u.role]} /></td>
                      <td className="px-4 py-3">
                        <div className="flex flex-wrap gap-1">
                          {(u.user_wilayah ?? []).length === 0 ? <span className="text-slate-300">{['admin', 'camat'].includes(u.role) ? 'Semua wilayah' : '—'}</span> :
                            u.user_wilayah!.map((uw) => (
                              <span key={uw.id} className="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-600">{uw.wilayah?.nama ?? '#'}</span>
                            ))}
                        </div>
                      </td>
                      <td className="px-4 py-3"><StatusBadge status={Number(u.status_aktif) ? 'active' : 'paused'} label={Number(u.status_aktif) ? 'Aktif' : 'Nonaktif'} /></td>
                      <td className="px-4 py-3">
                        <div className="flex justify-end gap-1">
                          <Button size="sm" variant="ghost" disabled={isMe} title="Reset password" onClick={() => setResetTarget(u)}><KeyRound size={14} /></Button>
                          <Button size="sm" variant="ghost" disabled={isMe} title={Number(u.status_aktif) ? 'Nonaktifkan' : 'Aktifkan'}
                            onClick={() => act(() => api.post(`/users/${u.id}/toggle-status`), 'Status diubah')}>
                            <Power size={14} className={Number(u.status_aktif) ? 'text-emerald-500' : 'text-slate-300'} />
                          </Button>
                          <Button size="sm" variant="ghost" onClick={() => setModal({ mode: 'edit', user: u })}><Pencil size={14} /></Button>
                          <Button size="sm" variant="ghost" className="text-rose-500 hover:bg-rose-50" disabled={isMe} onClick={() => setDeleteTarget(u)}><Trash2 size={14} /></Button>
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
            <span className="text-xs text-slate-500">Hal. {data.meta.current_page} / {data.meta.last_page}</span>
            <div className="flex gap-2">
              <Button size="sm" variant="secondary" disabled={data.meta.current_page <= 1} onClick={() => setFilters((f) => ({ ...f, page: (f.page ?? 1) - 1 }))}>Sebelumnya</Button>
              <Button size="sm" variant="secondary" disabled={data.meta.current_page >= data.meta.last_page} onClick={() => setFilters((f) => ({ ...f, page: (f.page ?? 1) + 1 }))}>Berikutnya</Button>
            </div>
          </div>
        )}
      </Card>

      {modal && (
        <UserFormModal mode={modal.mode} user={modal.user} onClose={() => setModal(null)} pending={pending}
          onSubmit={async (payload) => {
            const ok = modal.mode === 'create'
              ? await act(() => api.post('/users', payload), 'User ditambahkan')
              : await act(() => api.put(`/users/${modal.user!.id}`, payload), 'User diperbarui')
            if (ok) setModal(null)
          }} />
      )}

      <Modal open={!!deleteTarget} onClose={() => setDeleteTarget(null)} title="Hapus User" size="sm">
        <p className="text-sm text-slate-600">Yakin hapus <strong>{deleteTarget?.name}</strong> (@{deleteTarget?.username})?</p>
        <div className="mt-5 flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
          <Button variant="danger" disabled={pending}
            onClick={async () => { if (await act(() => api.delete(`/users/${deleteTarget!.id}`), 'User dihapus')) setDeleteTarget(null) }}>Hapus</Button>
        </div>
      </Modal>

      {/* Reset password result */}
      <Modal open={!!newPassword} onClose={() => setNewPassword(null)} title="Password Baru" size="sm">
        <p className="text-sm text-slate-600">Password baru untuk <strong>{resetTarget?.name}</strong>:</p>
        <div className="mt-3 rounded-xl bg-slate-900 px-4 py-3 text-center font-mono text-lg font-bold tracking-widest text-emerald-400">
          {newPassword}
        </div>
        <p className="mt-2 text-xs text-slate-400">Catat sekarang — hanya ditampilkan sekali.</p>
        <div className="mt-4 flex justify-end">
          <Button onClick={() => { navigator.clipboard.writeText(newPassword!); toast.success('Disalin ke clipboard') }}>Salin</Button>
        </div>
      </Modal>

      <Modal open={!!resetTarget && !newPassword} onClose={() => { setResetTarget(null); setNewPassword(null) }} title="Reset Password" size="sm">
        <p className="text-sm text-slate-600">Generate password acak untuk <strong>{resetTarget?.name}</strong>?</p>
        <div className="mt-5 flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setResetTarget(null)}>Batal</Button>
          <Button disabled={pending} onClick={async () => {
            setPending(true)
            try {
              const res = await api.post<{ data: { password: string } }>(`/users/${resetTarget!.id}/reset-password`)
              setNewPassword(res.data.password)
            } catch (e) {
              toast.error('Gagal reset password')
            } finally { setPending(false) }
          }}>Reset</Button>
        </div>
      </Modal>
    </div>
  )
}

function UserFormModal({ mode, user, onClose, onSubmit, pending }: {
  mode: 'create' | 'edit'; user?: SiwaUser; onClose: () => void
  onSubmit: (payload: Record<string, unknown>) => void; pending: boolean
}) {
  const { data: tree } = useWilayahTree()
  const [form, setForm] = useState({
    name: user?.name ?? '',
    username: user?.username ?? '',
    email: user?.email ?? '',
    password: '',
    role: user?.role ?? 'rt',
    status_aktif: user ? Boolean(Number(user.status_aktif)) : true,
  })
  const [wilayahIds, setWilayahIds] = useState<number[]>(
    (user?.user_wilayah ?? []).map((uw) => uw.wilayah?.id ?? uw.id),
  )
  const set = (k: string, v: string | boolean) => setForm((f) => ({ ...f, [k]: v }))

  // flatten tree → pilihan sesuai role
  const flat: { id: number; label: string; tingkat: string }[] = []
  ;(tree?.data ?? []).forEach((kel: WilayahRef) => {
    flat.push({ id: kel.id, label: kel.nama, tingkat: 'Kelurahan' })
    kel.children?.forEach((rw: WilayahRef) => {
      if (form.role === 'rw' || form.role === 'admin' || form.role === 'lurah') flat.push({ id: rw.id, label: `  ${rw.nama}`, tingkat: 'RW' })
      if (form.role === 'rt' || form.role === 'rw') {
        rw.children?.forEach((rt: WilayahRef) => flat.push({ id: rt.id, label: `    ${rt.nama}`, tingkat: 'RT' }))
      }
    })
  })

  return (
    <Modal open onClose={onClose} title={mode === 'create' ? 'Tambah User' : 'Edit User'} size="md"
      subtitle={mode === 'edit' ? `@${user?.username}` : undefined}>
      <form onSubmit={(e) => {
        e.preventDefault()
        const payload: Record<string, unknown> = {
          name: form.name, username: form.username, role: form.role,
          status_aktif: form.status_aktif, wilayah_ids: wilayahIds,
        }
        if (form.email) payload.email = form.email
        if (form.password) payload.password = form.password
        onSubmit(payload)
      }} className="space-y-4">
        <div className="grid grid-cols-2 gap-4">
          <div>
            <Label>Nama *</Label>
            <Input value={form.name} onChange={(e) => set('name', e.target.value)} required />
          </div>
          <div>
            <Label>Username *</Label>
            <Input value={form.username} onChange={(e) => set('username', e.target.value.toLowerCase())} required className="font-mono" />
          </div>
          <div>
            <Label>{mode === 'create' ? 'Password *' : 'Password (kosongkan = tetap)'}</Label>
            <Input type="password" value={form.password} onChange={(e) => set('password', e.target.value)} required={mode === 'create'} minLength={6} />
          </div>
          <div>
            <Label>Email (opsional)</Label>
            <Input type="email" value={form.email ?? ''} onChange={(e) => set('email', e.target.value)} />
          </div>
          <div>
            <Label>Role *</Label>
            <Select value={form.role} onChange={(v) => { set('role', v); setWilayahIds([]) }}
              options={Object.entries(ROLE_LABEL).map(([v, l]) => ({ value: v, label: l }))} />
          </div>
          <div>
            <Label>Status</Label>
            <Select value={form.status_aktif ? '1' : '0'} onChange={(v) => set('status_aktif', v === '1')}
              options={[{ value: '1', label: 'Aktif' }, { value: '0', label: 'Nonaktif' }]} />
          </div>
        </div>
        <div>
          <Label>Penugasan Wilayah {form.role === 'admin' || form.role === 'lurah' ? '(opsional — akses penuh)' : '(wajib untuk scoping data)'}</Label>
          <div className="max-h-44 space-y-1 overflow-y-auto rounded-xl border border-line p-2">
            {flat.map((w) => {
              const on = wilayahIds.includes(w.id)
              return (
                <label key={w.id} className="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-slate-50">
                  <input type="checkbox" checked={on} onChange={() => setWilayahIds((ids) => (on ? ids.filter((i) => i !== w.id) : [...ids, w.id]))}
                    className="h-4 w-4 rounded border-slate-300 accent-brand-600" />
                  <span className={`text-[13px] ${w.tingkat === 'RT' ? 'text-slate-600' : 'font-semibold text-slate-800'}`}>{w.label}</span>
                </label>
              )
            })}
          </div>
          {form.role === 'rw' && <p className="mt-1 text-[11px] text-slate-400">Pilih wilayah RW — semua RT di bawahnya otomatis masuk scope.</p>}
        </div>
        <div className="flex justify-end gap-2 border-t border-line pt-4">
          <Button type="button" variant="secondary" onClick={onClose}>Batal</Button>
          <Button type="submit" disabled={pending}>{pending ? 'Menyimpan…' : 'Simpan'}</Button>
        </div>
      </form>
    </Modal>
  )
}
