'use client'

import { Map, Building2, Users, Home } from 'lucide-react'
import { useQuery } from '@tanstack/react-query'
import { api } from '@/lib/api-client'
import { PageHeader } from '@/components/PageHeader'
import { Card, Skeleton } from '@/components/ui/primitives'
import type { WilayahRef } from '@/types'

export default function LaporanWilayahPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['laporan-wilayah'],
    queryFn: () => api.get<{ data: WilayahRef[] }>('/laporan/wilayah'),
  })

  return (
    <div className="animate-fade-up">
      <PageHeader title="Laporan Wilayah" subtitle="Struktur wilayah & sebaran kartu keluarga" />

      {isLoading ? (
        <div className="space-y-4">{Array.from({ length: 3 }).map((_, i) => <Skeleton key={i} className="h-32" />)}</div>
      ) : (
        (data?.data ?? []).map((kel) => (
          <Card key={kel.id} className="mb-4 p-6">
            <div className="mb-4 flex items-center gap-3">
              <span className="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><Building2 size={18} /></span>
              <div>
                <h3 className="text-sm font-bold text-slate-900">{kel.nama}</h3>
                <p className="text-xs text-slate-400">Kode {kel.kode}</p>
              </div>
            </div>

            <div className="space-y-4">
              {(kel.children ?? []).map((rw) => {
                const totalKk = (rw.children ?? []).reduce((s, rt) => s + (rt.total_kk ?? 0), 0)
                return (
                  <div key={rw.id}>
                    <div className="mb-2 flex items-center gap-2">
                      <Users size={13} className="text-emerald-500" />
                      <span className="text-[13px] font-bold text-slate-800">{rw.nama}</span>
                      <span className="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold tabular-nums text-slate-600">{totalKk} KK</span>
                    </div>
                    <div className="ml-6 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                      {(rw.children ?? []).map((rt) => (
                        <div key={rt.id} className="flex items-center gap-2 rounded-xl border border-line px-3 py-2">
                          <Home size={12} className="shrink-0 text-slate-300" />
                          <span className="min-w-0 flex-1 truncate text-[12px] text-slate-700">
                            {rt.nama.replace(/^RT\s*\d+\s*RW\s*\d+\s*/, 'RT ')}
                          </span>
                          <span className="text-[12px] font-bold tabular-nums text-slate-900">{rt.total_kk ?? 0}</span>
                        </div>
                      ))}
                      {(rw.children ?? []).length === 0 && <p className="text-xs text-slate-300">Belum ada RT</p>}
                    </div>
                  </div>
                )
              })}
              {(kel.children ?? []).length === 0 && <p className="text-sm text-slate-400">Belum ada RW</p>}
            </div>
          </Card>
        ))
      )}
    </div>
  )
}
