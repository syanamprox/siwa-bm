'use client'

import { useState } from 'react'
import { History, Search } from 'lucide-react'
import { useQuery } from '@tanstack/react-query'
import { api } from '@/lib/api-client'
import { PageHeader } from '@/components/PageHeader'
import { Button, Card, Input, Select, Skeleton, EmptyState, Avatar } from '@/components/ui/primitives'
import { QueryError } from '@/components/QueryError'
import { fmtDateTime } from '@/lib/utils'
import type { Aktivitas } from '@/types'

export default function AktivitasPage() {
  const [filters, setFilters] = useState<{ search?: string; module?: string; page?: number }>({})
  const [searchInput, setSearchInput] = useState('')

  const { data, isLoading, isFetching, isError, error, refetch } = useQuery({
    queryKey: ['aktivitas', filters],
    queryFn: () => api.get<{ data: Aktivitas[]; meta: { current_page: number; last_page: number; total: number } }>(
      `/aktivitas?${new URLSearchParams(Object.entries(filters).filter(([, v]) => v).map(([k, v]) => [k, String(v)]) as [string, string][])}`,
    ),
    placeholderData: (prev) => prev,
  })

  return (
    <div className="animate-fade-up">
      <PageHeader title="Log Aktivitas" subtitle="Audit trail semua aksi petugas & akses portal" />

      <Card className="mb-4 p-4">
        <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
          <div className="sm:col-span-2">
            <Input placeholder="Cari deskripsi / user…" value={searchInput} onChange={(e) => setSearchInput(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && setFilters((f) => ({ ...f, search: searchInput || undefined, page: 1 }))} />
          </div>
          <Select value={filters.module ?? ''} onChange={(v) => setFilters((f) => ({ ...f, module: v || undefined, page: 1 }))}
            placeholder="Semua Modul"
            options={['warga', 'keluarga', 'iuran', 'jenis_iuran', 'keluarga_iuran', 'wilayah', 'user', 'pengaturan', 'backup', 'portal_warga', 'portal_keluarga', 'portal_iuran'].map((m) => ({ value: m, label: m.replace('_', ' ') }))} />
        </div>
      </Card>

      <Card className="overflow-hidden">
        {isLoading ? (
          <div className="space-y-2 p-4">{Array.from({ length: 8 }).map((_, i) => <Skeleton key={i} className="h-12" />)}</div>
        ) : isError ? (
          <QueryError message={error?.message} onRetry={() => refetch()} />
        ) : (data?.data ?? []).length === 0 ? (
          <EmptyState icon={<History size={24} />} title="Belum ada aktivitas" />
        ) : (
          <div className={`divide-y divide-line ${isFetching ? 'opacity-60' : ''}`}>
            {data!.data.map((a) => (
              <div key={a.id} className="flex items-center gap-3 px-4 py-3">
                <Avatar name={a.user?.name ?? 'Tamu'} size="sm" />
                <div className="min-w-0 flex-1">
                  <p className="truncate text-[13px] text-slate-800">{a.description}</p>
                  <p className="text-[11px] text-slate-400">
                    {a.user?.name ?? 'Portal Publik'} · <span className="rounded bg-slate-100 px-1 py-0.5 font-mono text-[10px]">{a.action}</span> · {a.module} · {fmtDateTime(a.created_at)}
                  </p>
                </div>
                {a.user?.role && (
                  <span className="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-500">{a.user.role}</span>
                )}
              </div>
            ))}
          </div>
        )}
        {data && data.meta.last_page > 1 && (
          <div className="flex items-center justify-between border-t border-line px-4 py-3">
            <span className="text-xs text-slate-500">Hal. {data.meta.current_page} / {data.meta.last_page} · {data.meta.total} entri</span>
            <div className="flex gap-2">
              <Button size="sm" variant="secondary" disabled={data.meta.current_page <= 1} onClick={() => setFilters((f) => ({ ...f, page: (f.page ?? 1) - 1 }))}>Sebelumnya</Button>
              <Button size="sm" variant="secondary" disabled={data.meta.current_page >= data.meta.last_page} onClick={() => setFilters((f) => ({ ...f, page: (f.page ?? 1) + 1 }))}>Berikutnya</Button>
            </div>
          </div>
        )}
      </Card>
    </div>
  )
}
