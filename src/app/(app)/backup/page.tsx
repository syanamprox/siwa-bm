'use client'

import { useState } from 'react'
import { Database, Plus, Download, Trash2, Upload, HardDrive, Clock } from 'lucide-react'
import { toast } from 'sonner'
import { useQuery, useQueryClient } from '@tanstack/react-query'
import { api, ApiError, getCsrfToken } from '@/lib/api-client'
import { PageHeader } from '@/components/PageHeader'
import { KpiCard } from '@/components/KpiCard'
import { Button, Card, Skeleton } from '@/components/ui/primitives'
import { QueryError } from '@/components/QueryError'
import { Modal } from '@/components/ui/Modal'
import { fmtDateTime } from '@/lib/utils'

interface BackupItem { filename: string; size: number; size_human: string; created_at: string }
interface BackupData { backups: BackupItem[]; status: { total_backups: number; total_size: number; last_backup: string | null } }

const API_BASE = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api'

function humanSize(bytes: number): string {
  const units = ['B', 'KB', 'MB', 'GB']
  let i = 0
  let b = bytes
  while (b >= 1024 && i < units.length - 1) { b /= 1024; i++ }
  return `${b.toFixed(1)} ${units[i]}`
}

export default function BackupPage() {
  const qc = useQueryClient()
  const { data, isLoading, isError, error, refetch } = useQuery({
    queryKey: ['backup'],
    queryFn: () => api.get<{ data: BackupData }>('/backup'),
  })
  const [pending, setPending] = useState(false)
  const [restoreModal, setRestoreModal] = useState(false)
  const [restoreFile, setRestoreFile] = useState<File | null>(null)
  const [armed, setArmed] = useState(false)
  const [deleteTarget, setDeleteTarget] = useState<BackupItem | null>(null)

  function closeRestore() {
    setRestoreModal(false)
    setRestoreFile(null)
    setArmed(false)
  }

  function handleRestoreClick() {
    if (!restoreFile || pending) return
    if (!armed) {
      setArmed(true)
      window.setTimeout(() => setArmed(false), 3000) // auto-batal konfirmasi kedua
      return
    }
    restore(restoreFile)
  }

  async function create() {
    setPending(true)
    try {
      await api.post('/backup')
      toast.success('Backup dibuat')
      qc.invalidateQueries({ queryKey: ['backup'] })
    } catch (e) {
      toast.error(e instanceof ApiError ? e.message : 'Backup gagal')
    } finally { setPending(false) }
  }

  async function download(filename: string) {
    await getCsrfToken().catch(() => {})
    const xsrf = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
    const res = await fetch(`${API_BASE}/backup/${filename}/download`, {
      credentials: 'include',
      headers: xsrf ? { 'X-XSRF-TOKEN': decodeURIComponent(xsrf[1]) } : {},
    })
    if (!res.ok) { toast.error('Download gagal'); return }
    const blob = await res.blob()
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = filename
    a.click()
    URL.revokeObjectURL(url)
  }

  async function remove(filename: string) {
    try {
      await api.delete(`/backup/${filename}`)
      toast.success('Backup dihapus')
      qc.invalidateQueries({ queryKey: ['backup'] })
    } catch (e) {
      toast.error(e instanceof ApiError ? e.message : 'Gagal')
    }
  }

  async function restore(file: File) {
    setPending(true)
    try {
      await getCsrfToken()
      const xsrf = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
      const fd = new FormData()
      fd.append('backup_file', file)
      const res = await fetch(`${API_BASE}/backup/restore`, {
        method: 'POST',
        body: fd,
        credentials: 'include',
        headers: xsrf ? { 'X-XSRF-TOKEN': decodeURIComponent(xsrf[1]) } : {},
      })
      if (!res.ok) {
        const d = await res.json().catch(() => null)
        throw new Error(d?.message ?? 'Restore gagal')
      }
      toast.success('Restore berhasil — reload halaman')
      setTimeout(() => window.location.reload(), 1500)
    } catch (e) {
      toast.error(e instanceof Error ? e.message : 'Restore gagal')
    } finally {
      setPending(false)
      closeRestore()
    }
  }

  return (
    <div className="animate-fade-up">
      <PageHeader title="Backup & Restore" subtitle="Snapshot database MySQL + berkas unggahan"
        actions={
          <div className="flex gap-2">
            <Button variant="secondary" onClick={() => setRestoreModal(true)}><Upload size={15} /> Restore</Button>
            <Button onClick={create} disabled={pending}><Plus size={15} /> {pending ? 'Membuat…' : 'Backup Sekarang'}</Button>
          </div>
        } />

      <div className="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <KpiCard label="Total Backup" value={data?.data?.status.total_backups ?? '—'} icon={<Database size={18} />} accent="#2563eb" />
        <KpiCard label="Ukuran Total" value={humanSize(data?.data?.status.total_size ?? 0)} icon={<HardDrive size={18} />} accent="#059669" />
        <KpiCard label="Backup Terakhir" value={data?.data?.status.last_backup ? fmtDateTime(data.data.status.last_backup) : '—'} icon={<Clock size={18} />} accent="#f59e0b" />
      </div>

      <Card className="overflow-hidden">
        {isLoading ? (
          <div className="space-y-2 p-4">{Array.from({ length: 3 }).map((_, i) => <Skeleton key={i} className="h-12" />)}</div>
        ) : isError ? (
          <QueryError message={error?.message} onRetry={() => refetch()} />
        ) : (data?.data?.backups ?? []).length === 0 ? (
          <p className="py-12 text-center text-sm text-slate-400">Belum ada backup</p>
        ) : (
          <table className="w-full text-[13px]">
            <thead>
              <tr className="border-b border-line bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-400">
                <th className="px-4 py-3 font-semibold">File</th>
                <th className="px-4 py-3 text-right font-semibold">Ukuran</th>
                <th className="px-4 py-3 font-semibold">Dibuat</th>
                <th className="px-4 py-3 text-right font-semibold">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-line">
              {data!.data.backups.map((b) => (
                <tr key={b.filename} className="hover:bg-slate-50">
                  <td className="px-4 py-3 font-mono text-[12px] text-slate-700">{b.filename}</td>
                  <td className="px-4 py-3 text-right tabular-nums text-slate-600">{b.size_human}</td>
                  <td className="px-4 py-3 text-slate-600">{fmtDateTime(b.created_at)}</td>
                  <td className="px-4 py-3">
                    <div className="flex justify-end gap-1">
                      <Button size="sm" variant="ghost" onClick={() => download(b.filename)} title="Download"><Download size={14} /></Button>
                      <Button size="sm" variant="ghost" className="text-rose-500 hover:bg-rose-50" onClick={() => setDeleteTarget(b)} title="Hapus"><Trash2 size={14} /></Button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </Card>

      {/* Delete confirm */}
      <Modal open={!!deleteTarget} onClose={() => setDeleteTarget(null)} title="Hapus Backup" size="sm">
        <p className="text-sm text-slate-600">
          Yakin hapus <strong className="font-mono text-[12px]">{deleteTarget?.filename}</strong>
          {' '}({deleteTarget?.size_human})? File tidak bisa dikembalikan.
        </p>
        <div className="mt-5 flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
          <Button variant="danger" onClick={() => { remove(deleteTarget!.filename); setDeleteTarget(null) }}>Hapus</Button>
        </div>
      </Modal>

      {/* Restore — 2 langkah: pilih file → konfirmasi ganda */}
      <Modal open={restoreModal} onClose={closeRestore} title="Restore Backup" size="sm"
        subtitle="Bahaya: menimpa database & file yang ada">
        <div className="space-y-4">
          <input type="file" accept=".zip"
            onChange={(e) => { setRestoreFile(e.target.files?.[0] ?? null); setArmed(false) }}
            className="w-full rounded-xl border border-line px-3 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-brand-700" />
          {restoreFile && (
            <p className="rounded-xl bg-slate-50 px-3 py-2.5 text-[13px] text-slate-700">
              File terpilih: <strong className="font-mono text-[12px]">{restoreFile.name}</strong> ({humanSize(restoreFile.size)})
            </p>
          )}
          <p className="text-xs text-rose-500">Restore akan menimpa seluruh data. Pastikan backup terbaru sudah dibuat.</p>
          <div className="flex justify-end gap-2 border-t border-line pt-4">
            <Button variant="secondary" onClick={closeRestore}>Batal</Button>
            <Button variant="danger" disabled={!restoreFile || pending} onClick={handleRestoreClick}>
              {pending ? 'Memulihkan…' : armed ? 'Klik lagi untuk konfirmasi restore' : 'Restore Sekarang'}
            </Button>
          </div>
        </div>
      </Modal>
    </div>
  )
}
