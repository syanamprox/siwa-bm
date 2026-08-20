'use client'

import { useState } from 'react'
import { Settings, Save } from 'lucide-react'
import { toast } from 'sonner'
import { useQuery, useQueryClient } from '@tanstack/react-query'
import { api, ApiError } from '@/lib/api-client'
import { PageHeader } from '@/components/PageHeader'
import { Button, Card, Input, Label, Select, Skeleton, Textarea } from '@/components/ui/primitives'
import { QueryError } from '@/components/QueryError'

const GROUP_LABEL: Record<string, string> = {
  app: 'Aplikasi',
  kelurahan: 'Identitas Kelurahan',
  keamanan: 'Keamanan',
}

/* ── Opsi siap pilih (ramah user awam) — key di sini dirender Select ── */
const today = new Date()
const tgl = (o: Intl.DateTimeFormatOptions) => today.toLocaleDateString('id-ID', o)

const FIELD_OPTIONS: Record<string, { value: string; label: string }[]> = {
  zona_waktu: [
    { value: 'Asia/Jakarta', label: 'WIB — Waktu Indonesia Barat' },
    { value: 'Asia/Makassar', label: 'WITA — Waktu Indonesia Tengah' },
    { value: 'Asia/Jayapura', label: 'WIT — Waktu Indonesia Timur' },
  ],
  format_tanggal: [
    { value: 'd/m/Y', label: tgl({ day: '2-digit', month: '2-digit', year: 'numeric' }) },
    { value: 'd-m-Y', label: tgl({ day: '2-digit', month: '2-digit', year: 'numeric' }).replaceAll('/', '-') },
    { value: 'd F Y', label: tgl({ day: 'numeric', month: 'long', year: 'numeric' }) },
    { value: 'l, d F Y', label: tgl({ weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) },
    { value: 'Y-m-d', label: `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}` },
  ],
  format_nomor: [
    { value: 'id_ID', label: '1.234.567 (Indonesia)' },
    { value: 'en_US', label: '1,234,567 (Internasional)' },
  ],
  mata_uang: [
    { value: 'IDR', label: 'Rupiah (Rp)' },
    { value: 'USD', label: 'Dolar AS ($)' },
  ],
  maks_login: [
    { value: '3', label: '3 kali' },
    { value: '5', label: '5 kali (disarankan)' },
    { value: '10', label: '10 kali' },
  ],
  timeout_sesi: [
    { value: '30', label: '30 menit' },
    { value: '60', label: '1 jam' },
    { value: '120', label: '2 jam (disarankan)' },
    { value: '240', label: '4 jam' },
  ],
  log_semua_aktivitas: [
    { value: '1', label: 'Aktif — semua aksi dicatat' },
    { value: '0', label: 'Nonaktif' },
  ],
}

/** Field panjang/multi-baris */
const TEXTAREA_KEYS = new Set(['alamat_kantor'])

export default function PengaturanPage() {
  const qc = useQueryClient()
  const { data, isLoading, isError, error, refetch } = useQuery({
    queryKey: ['pengaturan'],
    queryFn: () => api.get<{ data: Record<string, { key: string; value: string; keterangan: string }[]> }>('/pengaturan'),
  })
  const [values, setValues] = useState<Record<string, string>>({})
  const [pending, setPending] = useState(false)

  const dirty = Object.entries(values).length > 0

  async function save() {
    setPending(true)
    try {
      await api.put('/pengaturan', {
        settings: Object.entries(values).map(([key, value]) => ({ key, value })),
      })
      toast.success('Pengaturan tersimpan')
      setValues({})
      qc.invalidateQueries({ queryKey: ['pengaturan'] })
    } catch (e) {
      toast.error(e instanceof ApiError ? e.message : 'Gagal')
    } finally {
      setPending(false)
    }
  }

  return (
    <div className="animate-fade-up max-w-3xl">
      <PageHeader title="Pengaturan Sistem" subtitle="Konfigurasi aplikasi & identitas kelurahan"
        actions={
          dirty ? <Button onClick={save} disabled={pending}><Save size={15} /> {pending ? 'Menyimpan…' : 'Simpan Perubahan'}</Button> : undefined
        } />

      {isLoading ? (
        <div className="space-y-4">{Array.from({ length: 3 }).map((_, i) => <Skeleton key={i} className="h-32" />)}</div>
      ) : isError ? (
        <Card><QueryError message={error?.message} onRetry={() => refetch()} /></Card>
      ) : (
        Object.entries(data?.data ?? {}).map(([group, settings]) => (
          <Card key={group} className="mb-4 p-6">
            <h3 className="mb-4 flex items-center gap-2 text-sm font-bold text-slate-900">
              <Settings size={14} className="text-slate-400" /> {GROUP_LABEL[group] ?? group}
            </h3>
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
              {settings.map((s) => {
                const val = values[s.key] ?? s.value
                const opts = FIELD_OPTIONS[s.key]
                const changed = values[s.key] !== undefined
                return (
                  <div key={s.key} className={TEXTAREA_KEYS.has(s.key) ? 'sm:col-span-2' : ''}>
                    <Label>{s.keterangan}</Label>
                    {opts ? (
                      <Select value={val} onChange={(v) => setValues((p) => ({ ...p, [s.key]: v }))}
                        options={opts} className={changed ? '[&>button]:border-brand-400' : ''} />
                    ) : TEXTAREA_KEYS.has(s.key) ? (
                      <Textarea rows={2} value={val} onChange={(e) => setValues((v) => ({ ...v, [s.key]: e.target.value }))}
                        className={changed ? 'border-brand-400' : ''} />
                    ) : (
                      <Input value={val} onChange={(e) => setValues((v) => ({ ...v, [s.key]: e.target.value }))}
                        className={changed ? 'border-brand-400' : ''} />
                    )}
                  </div>
                )
              })}
            </div>
          </Card>
        ))
      )}
    </div>
  )
}
