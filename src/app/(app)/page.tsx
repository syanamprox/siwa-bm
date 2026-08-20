'use client'

import {
  Users, Home, Coins, TrendingUp, Map, Activity, AlertCircle, UserCheck, User,
} from 'lucide-react'
import {
  AreaChart, Area, XAxis, YAxis, Tooltip, ResponsiveContainer, CartesianGrid,
} from 'recharts'
import { useDashboard, useWilayahTree } from '@/hooks/use-siwa'
import { useAuth } from '@/stores/auth-store'
import { KpiCard } from '@/components/KpiCard'
import { PageHeader } from '@/components/PageHeader'
import { Card, Skeleton, StatusBadge } from '@/components/ui/primitives'
import { QueryError } from '@/components/QueryError'
import { fmtMoney, fmtDateTime } from '@/lib/utils'

const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']

function greeting(): string {
  const h = new Date().getHours()
  if (h < 11) return 'Selamat pagi'
  if (h < 15) return 'Selamat siang'
  if (h < 19) return 'Selamat sore'
  return 'Selamat malam'
}

export default function DashboardPage() {
  const user = useAuth((s) => s.user)
  const { data: raw, isLoading, isError, error, refetch } = useDashboard()
  const dash = raw?.data
  const { data: wilayahTree } = useWilayahTree()

  const firstName = (user?.name ?? 'User').split(' ')[0]
  const today = new Date().toLocaleDateString('id-ID', {
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
  })

  // resolve nama RT untuk warga_per_rt map
  const rtNames: Record<string, string> = {}
  wilayahTree?.data?.forEach((kel) =>
    kel.children?.forEach((rw) =>
      rw.children?.forEach((rt) => { rtNames[String(rt.id)] = rt.nama }),
    ),
  )

  const tren = (dash?.pembayaran_tren ?? []).map((t) => ({
    bulan: MONTHS[Number(t.bulan.slice(5, 7)) - 1] ?? t.bulan,
    total: t.total,
  }))

  return (
    <div className="animate-fade-up">
      <PageHeader
        title={`${greeting()}, ${firstName}`}
        subtitle={`${today}${user?.wilayah ? ` · ${user.wilayah.nama}` : ''}`}
      />

      {/* KPI row */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {isLoading ? (
          Array.from({ length: 4 }).map((_, i) => <Skeleton key={i} className="h-[130px]" />)
        ) : isError ? (
          <div className="sm:col-span-2 xl:col-span-4">
            <QueryError message={error?.message} onRetry={() => refetch()} />
          </div>
        ) : (
          <>
            <KpiCard label="Total Warga" value={dash?.total_warga ?? 0} icon={<Users size={18} />} accent="#2563eb"
              sub={`${dash?.warga_laki ?? 0} L · ${dash?.warga_perempuan ?? 0} P`} />
            <KpiCard label="Kartu Keluarga" value={dash?.total_keluarga ?? 0} icon={<Home size={18} />} accent="#059669" />
            <KpiCard label="Tunggakan Iuran" value={fmtMoney(dash?.total_tagihan_iuran ?? 0)} icon={<Coins size={18} />} accent="#f59e0b" />
            <KpiCard label="Pemasukan Bulan Ini" value={fmtMoney(dash?.pemasukan_bulan_ini ?? 0)} icon={<TrendingUp size={18} />} accent="#10b981" />
          </>
        )}
      </div>

      <div className="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        {/* Chart tren pembayaran */}
        <Card className="p-6 lg:col-span-2">
          <div className="mb-4 flex items-center justify-between">
            <div>
              <h3 className="text-sm font-bold text-slate-900">Tren Pemasukan Iuran</h3>
              <p className="text-xs text-slate-500">6 bulan terakhir</p>
            </div>
          </div>
          {isLoading ? (
            <Skeleton className="h-[240px]" />
          ) : isError ? (
            <QueryError message={error?.message} onRetry={() => refetch()} />
          ) : tren.length === 0 ? (
            <p className="py-16 text-center text-sm text-slate-400">Belum ada pembayaran</p>
          ) : (
            <div className="h-[240px]">
              <ResponsiveContainer width="100%" height="100%">
                <AreaChart data={tren} margin={{ top: 4, right: 4, bottom: 0, left: 4 }}>
                  <defs>
                    <linearGradient id="colorTotal" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" stopColor="#2563eb" stopOpacity={0.25} />
                      <stop offset="100%" stopColor="#2563eb" stopOpacity={0} />
                    </linearGradient>
                  </defs>
                  <CartesianGrid strokeDasharray="3 3" stroke="#eceff3" vertical={false} />
                  <XAxis dataKey="bulan" tick={{ fontSize: 11, fill: '#94a3b8' }} axisLine={false} tickLine={false} />
                  <YAxis tick={{ fontSize: 11, fill: '#94a3b8' }} axisLine={false} tickLine={false}
                    tickFormatter={(v: number) => (v >= 1_000_000 ? `${v / 1_000_000}jt` : v >= 1000 ? `${v / 1000}rb` : String(v))} />
                  <Tooltip
                    formatter={(value) => [fmtMoney(Number(value)), 'Pemasukan']}
                    contentStyle={{ borderRadius: 12, border: '1px solid #eceff3', fontSize: 12 }}
                  />
                  <Area type="monotone" dataKey="total" stroke="#2563eb" strokeWidth={2} fill="url(#colorTotal)" />
                </AreaChart>
              </ResponsiveContainer>
            </div>
          )}
        </Card>

        {/* Aktivitas terbaru */}
        <Card className="p-6">
          <div className="mb-4 flex items-center gap-2">
            <Activity size={15} className="text-slate-400" />
            <h3 className="text-sm font-bold text-slate-900">Aktivitas Terbaru</h3>
          </div>
          {isLoading ? (
            <div className="space-y-3">{Array.from({ length: 5 }).map((_, i) => <Skeleton key={i} className="h-12" />)}</div>
          ) : isError ? (
            <QueryError message={error?.message} onRetry={() => refetch()} />
          ) : (dash?.recent_activities ?? []).length === 0 ? (
            <p className="py-8 text-center text-sm text-slate-400">Belum ada aktivitas</p>
          ) : (
            <div className="-mx-2 max-h-[280px] space-y-0.5 overflow-y-auto">
              {dash!.recent_activities.map((a) => (
                <div key={a.id} className="flex gap-3 rounded-xl px-2 py-2 hover:bg-slate-50">
                  <span className="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                    {a.module === 'iuran' ? <Coins size={13} /> : a.module === 'keluarga' ? <Home size={13} /> : a.module === 'user' ? <UserCheck size={13} /> : <User size={13} />}
                  </span>
                  <div className="min-w-0 flex-1">
                    <p className="truncate text-[13px] font-medium text-slate-800">{a.description}</p>
                    <p className="text-[11px] text-slate-400">{a.user} · {fmtDateTime(a.created_at)}</p>
                  </div>
                </div>
              ))}
            </div>
          )}
        </Card>
      </div>

      {/* Wilayah distribution + pending iuran */}
      <div className="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        {/* Warga per wilayah */}
        <Card className="p-6">
          <div className="mb-4 flex items-center gap-2">
            <Map size={15} className="text-slate-400" />
            <h3 className="text-sm font-bold text-slate-900">
              {dash?.warga_per_rw ? 'Warga per RW' : 'Warga per RT'}
            </h3>
          </div>
          {isLoading ? (
            <Skeleton className="h-[160px]" />
          ) : isError ? (
            <QueryError message={error?.message} onRetry={() => refetch()} />
          ) : (
            <div className="space-y-3">
              {Object.entries(dash?.warga_per_rw ?? dash?.warga_per_rt ?? {}).map(([nama, total]) => (
                <div key={nama}>
                  <div className="mb-1 flex items-center justify-between text-xs">
                    <span className="font-medium text-slate-700">{rtNames[nama] ?? nama}</span>
                    <span className="tabular-nums font-bold text-slate-900">{total}</span>
                  </div>
                  <div className="h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <div
                      className="h-full rounded-full bg-gradient-to-r from-brand-400 to-brand-600"
                      style={{ width: `${Math.min(100, (Number(total) / Math.max(1, dash?.total_warga ?? 1)) * 100 * 2)}%` }}
                    />
                  </div>
                </div>
              ))}
              {Object.keys(dash?.warga_per_rw ?? dash?.warga_per_rt ?? {}).length === 0 && (
                <p className="py-8 text-center text-sm text-slate-400">Belum ada data</p>
              )}
            </div>
          )}
        </Card>

        {/* Pending iuran (RT/RW) */}
        {dash?.pending_iuran && (
          <Card className="p-6 lg:col-span-2">
            <div className="mb-4 flex items-center gap-2">
              <AlertCircle size={15} className="text-amber-500" />
              <h3 className="text-sm font-bold text-slate-900">Tagihan Belum Lunas — Jatuh Tempo Terdekat</h3>
            </div>
            <div className="-mx-2 max-h-[240px] overflow-y-auto">
              <table className="w-full text-[13px]">
                <thead>
                  <tr className="border-b border-line text-left text-[11px] uppercase tracking-wider text-slate-400">
                    <th className="px-2 py-2 font-semibold">Keluarga</th>
                    <th className="px-2 py-2 font-semibold">Jenis</th>
                    <th className="px-2 py-2 font-semibold">Periode</th>
                    <th className="px-2 py-2 text-right font-semibold">Nominal</th>
                    <th className="px-2 py-2 font-semibold">Jatuh Tempo</th>
                    <th className="px-2 py-2 font-semibold">Status</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-line">
                  {dash.pending_iuran.map((p) => (
                    <tr key={p.id} className="hover:bg-slate-50">
                      <td className="px-2 py-2.5 font-medium text-slate-800">{p.keluarga ?? '-'}</td>
                      <td className="px-2 py-2.5 text-slate-600">{p.jenis ?? '-'}</td>
                      <td className="px-2 py-2.5 tabular-nums text-slate-600">{p.periode}</td>
                      <td className="px-2 py-2.5 text-right tabular-nums font-semibold text-slate-900">{fmtMoney(p.nominal)}</td>
                      <td className="px-2 py-2.5 tabular-nums text-slate-600">{p.jatuh_tempo ?? '-'}</td>
                      <td className="px-2 py-2.5"><StatusBadge status="belum lunas" label="Belum Lunas" /></td>
                    </tr>
                  ))}
                </tbody>
              </table>
              {dash.pending_iuran.length === 0 && (
                <p className="py-8 text-center text-sm text-slate-400">Tidak ada tunggakan 🎉</p>
              )}
            </div>
          </Card>
        )}
      </div>
    </div>
  )
}
