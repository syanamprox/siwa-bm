'use client'

import { useState } from 'react'
import { Settings, Save } from 'lucide-react'
import { toast } from 'sonner'
import { useQuery, useQueryClient } from '@tanstack/react-query'
import { api, ApiError } from '@/lib/api-client'
import { PageHeader } from '@/components/PageHeader'
import { Button, Card, Input, Label, Skeleton } from '@/components/ui/primitives'
import { QueryError } from '@/components/QueryError'

const GROUP_LABEL: Record<string, string> = {
  app: 'Aplikasi',
  kelurahan: 'Identitas Kelurahan',
  keamanan: 'Keamanan',
}

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
                return (
                  <div key={s.key}>
                    <Label>{s.keterangan} <span className="font-mono text-slate-300">({s.key})</span></Label>
                    <Input value={val} onChange={(e) => setValues((v) => ({ ...v, [s.key]: e.target.value }))}
                      className={values[s.key] !== undefined ? 'border-brand-400' : ''} />
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
