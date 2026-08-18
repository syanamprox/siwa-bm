'use client'

import { useState } from 'react'
import { Wand2, Eye, CheckCircle2, Loader2, Coins, Users, AlertTriangle } from 'lucide-react'
import { useGenerationPreview, useGenerate, useGenerationRtOptions, useJenisIuranList } from '@/hooks/use-siwa'
import { PageHeader } from '@/components/PageHeader'
import { Button, Card, Label, Select, StatusBadge, Skeleton } from '@/components/ui/primitives'
import { fmtMoney } from '@/lib/utils'

const MONTHS = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']

export default function GeneratePage() {
  const bulanDefault = new Date().toISOString().slice(0, 7)
  const [periode, setPeriode] = useState(bulanDefault)
  const [rtId, setRtId] = useState('')
  const [jenisIds, setJenisIds] = useState<number[]>([])
  const [step, setStep] = useState<'form' | 'preview' | 'done'>('form')

  const { data: rtOptions } = useGenerationRtOptions()
  const { data: jenisList } = useJenisIuranList(true)
  const { data: preview, isLoading: loadingPreview, isFetching } = useGenerationPreview(
    { periode_bulan: periode, rt_id: rtId ? Number(rtId) : undefined, jenis_iuran_ids: jenisIds.length ? jenisIds : undefined },
    step !== 'form',
  )
  const generate = useGenerate()
  const [result, setResult] = useState<{ generated: number; duplicates: number } | null>(null)

  const periodeLabel = () => {
    const [y, m] = periode.split('-')
    return `${MONTHS[Number(m) - 1]} ${y}`
  }

  return (
    <div className="animate-fade-up">
      <PageHeader title="Generate Tagihan" subtitle="Buat tagihan massal per periode — dengan preview sebelum commit" />

      {/* Stepper */}
      <div className="mb-6 flex items-center gap-2 text-xs font-semibold">
        {['1. Pilih', '2. Preview', '3. Selesai'].map((label, i) => {
          const active = (i === 0 && step === 'form') || (i === 1 && step === 'preview') || (i === 2 && step === 'done')
          const done = (i === 0 && step !== 'form') || (i === 1 && step === 'done')
          return (
            <div key={label} className="flex items-center gap-2">
              <span className={`flex h-6 w-6 items-center justify-center rounded-full text-[11px] ${active ? 'bg-brand-600 text-white' : done ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400'}`}>
                {done ? <CheckCircle2 size={13} /> : i + 1}
              </span>
              <span className={active ? 'text-slate-900' : 'text-slate-400'}>{label.split('. ')[1]}</span>
              {i < 2 && <span className="h-px w-8 bg-line" />}
            </div>
          )
        })}
      </div>

      {step === 'form' && (
        <Card className="max-w-2xl p-6">
          <div className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <Label>Periode *</Label>
                <input
                  type="month"
                  value={periode}
                  onChange={(e) => setPeriode(e.target.value)}
                  className="w-full rounded-xl border border-line bg-white h-10 px-3.5 text-sm focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400 focus:outline-none"
                />
              </div>
              <div>
                <Label>RT (opsional — kosongkan = semua dalam scope)</Label>
                <Select value={rtId} onChange={setRtId} placeholder="Semua RT…" searchable
                  options={(rtOptions?.data ?? []).map((rt) => ({ value: String(rt.id), label: rt.nama }))} />
              </div>
            </div>
            <div>
              <Label>Jenis Iuran (opsional — kosongkan = semua yang terhubung)</Label>
              <div className="mt-1 flex flex-wrap gap-2">
                {(jenisList?.data ?? []).map((j) => {
                  const on = jenisIds.includes(j.id)
                  return (
                    <button
                      key={j.id}
                      type="button"
                      onClick={() => setJenisIds((ids) => (on ? ids.filter((i) => i !== j.id) : [...ids, j.id]))}
                      className={`rounded-full border px-3 py-1.5 text-xs font-semibold transition ${on ? 'border-brand-600 bg-brand-600 text-white' : 'border-line bg-white text-slate-600 hover:border-brand-300'}`}
                    >
                      {j.nama} · {fmtMoney(j.jumlah)}
                    </button>
                  )
                })}
              </div>
            </div>
            <div className="flex items-center justify-end gap-2 border-t border-line pt-4">
              <Button onClick={() => setStep('preview')} disabled={!periode}>
                <Eye size={15} /> Preview Tagihan
              </Button>
            </div>
          </div>
        </Card>
      )}

      {step === 'preview' && (
        <div className="space-y-4">
          {/* Summary */}
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <Card className="p-5">
              <div className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400"><Users size={13} /> Keluarga</div>
              <p className="mt-2 text-[28px] font-extrabold tabular-nums text-slate-900">{preview?.data?.summary.total_families ?? '—'}</p>
            </Card>
            <Card className="p-5">
              <div className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400"><Coins size={13} /> Total Tagihan</div>
              <p className="mt-2 text-[28px] font-extrabold tabular-nums text-slate-900">{fmtMoney(preview?.data?.summary.total_nominal ?? 0)}</p>
            </Card>
            <Card className="p-5">
              <div className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400"><AlertTriangle size={13} /> Item sudah ada</div>
              <p className="mt-2 text-[28px] font-extrabold tabular-nums text-amber-600">
                {preview?.data?.preview.reduce((s, f) => s + f.sudah_ada, 0) ?? 0}
              </p>
            </Card>
          </div>

          {/* Detail table */}
          <Card className="overflow-hidden">
            <div className="border-b border-line px-4 py-3 text-sm font-bold text-slate-900">
              Preview — periode {periodeLabel()}
              {rtId && ` · RT terpilih`}
            </div>
            {loadingPreview ? (
              <div className="space-y-2 p-4">{Array.from({ length: 5 }).map((_, i) => <Skeleton key={i} className="h-10" />)}</div>
            ) : (
              <div className="max-h-[420px] overflow-y-auto">
                <table className="w-full text-[13px]">
                  <thead className="sticky top-0 bg-slate-50">
                    <tr className="border-b border-line text-left text-[11px] uppercase tracking-wider text-slate-400">
                      <th className="px-4 py-2.5 font-semibold">No. KK</th>
                      <th className="px-4 py-2.5 font-semibold">Kepala Keluarga</th>
                      <th className="px-4 py-2.5 font-semibold">RT</th>
                      <th className="px-4 py-2.5 font-semibold">Iuran</th>
                      <th className="px-4 py-2.5 text-right font-semibold">Total</th>
                      <th className="px-4 py-2.5 font-semibold">Sudah Ada</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-line">
                    {(preview?.data?.preview ?? []).map((f) => (
                      <tr key={f.kk_id} className={`hover:bg-slate-50 ${isFetching ? 'opacity-60' : ''}`}>
                        <td className="px-4 py-2.5 tabular-nums text-slate-500">{f.no_kk}</td>
                        <td className="px-4 py-2.5 font-semibold text-slate-900">{f.kepala_keluarga}</td>
                        <td className="px-4 py-2.5 text-slate-600">{f.rt ?? '-'}</td>
                        <td className="px-4 py-2.5">
                          <div className="flex flex-wrap gap-1">
                            {f.iurans.map((it) => (
                              <span key={it.jenis_iuran_id} className="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600">
                                {it.jenis_iuran} {fmtMoney(it.nominal)}
                              </span>
                            ))}
                          </div>
                        </td>
                        <td className="px-4 py-2.5 text-right font-bold tabular-nums text-slate-900">{fmtMoney(f.total)}</td>
                        <td className="px-4 py-2.5">
                          {f.sudah_ada > 0 ? <StatusBadge status="pending" /> : <span className="text-slate-300">—</span>}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
                {(preview?.data?.preview ?? []).length === 0 && (
                  <p className="py-10 text-center text-sm text-slate-400">Tidak ada keluarga aktif dengan koneksi iuran untuk parameter ini.</p>
                )}
              </div>
            )}
            <div className="flex items-center justify-between border-t border-line px-4 py-3">
              <Button variant="secondary" onClick={() => setStep('form')}>← Ubah Parameter</Button>
              <Button
                disabled={generate.isPending || (preview?.data?.preview ?? []).length === 0}
                onClick={() =>
                  generate.mutate(
                    {
                      periode_bulan: periode,
                      rt_id: rtId ? Number(rtId) : undefined,
                      jenis_iuran_ids: jenisIds.length ? jenisIds : undefined,
                    },
                    { onSuccess: (res) => { setResult(res.data); setStep('done') } },
                  )
                }
              >
                {generate.isPending ? <><Loader2 size={15} className="animate-spin" /> Membuat…</> : <><Wand2 size={15} /> Generate {preview?.data?.summary.total_iuran ?? 0} Tagihan</>}
              </Button>
            </div>
          </Card>
        </div>
      )}

      {step === 'done' && result && (
        <Card className="max-w-lg p-8 text-center">
          <span className="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
            <CheckCircle2 size={26} />
          </span>
          <h2 className="mt-4 text-lg font-extrabold text-slate-900">Generate Selesai</h2>
          <p className="mt-1 text-sm text-slate-500">Periode {periodeLabel()}</p>
          <div className="mt-6 grid grid-cols-2 gap-3">
            <div className="rounded-2xl bg-emerald-50 p-4">
              <p className="text-[28px] font-extrabold tabular-nums text-emerald-700">{result.generated}</p>
              <p className="text-xs font-semibold text-emerald-600">tagihan dibuat</p>
            </div>
            <div className="rounded-2xl bg-amber-50 p-4">
              <p className="text-[28px] font-extrabold tabular-nums text-amber-700">{result.duplicates}</p>
              <p className="text-xs font-semibold text-amber-600">duplikat di-skip</p>
            </div>
          </div>
          <div className="mt-6 flex justify-center gap-2">
            <Button variant="secondary" onClick={() => { setStep('form'); setResult(null) }}>Generate Lagi</Button>
            <a href="/iuran"><Button>Lihat Tagihan</Button></a>
          </div>
        </Card>
      )}
    </div>
  )
}
