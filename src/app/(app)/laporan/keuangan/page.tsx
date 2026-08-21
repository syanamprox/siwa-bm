'use client'

import { useState } from 'react'
import { Wallet, ArrowDownToLine, ArrowUpFromLine, Landmark, Plus, Building2, Trash2 } from 'lucide-react'
import { useKasUnits, useKasSummary, useCreateKasUnit, useDeleteKasUnit, useCreateKasTrx, useDeleteKasTrx, type KasUnitItem } from '@/hooks/use-kas'
import { useWilayahTree } from '@/hooks/use-siwa'
import { PageHeader } from '@/components/PageHeader'
import { Button, Card, Input, Label, Select, Skeleton, StatusBadge } from '@/components/ui/primitives'
import { Modal } from '@/components/ui/Modal'
import { QueryError } from '@/components/QueryError'
import { fmtMoney } from '@/lib/utils'
import { useAuth } from '@/stores/auth-store'

const KATEGORI = ['Iuran', 'Parkir', 'Infaq', 'Donasi', 'Saldo Awal', 'Lain-lain', 'Operasional', 'Rapat', 'Perlengkapan', 'Kesehatan', 'Kegiatan']

const KAT_COLOR: Record<string, string> = {
  Iuran: 'bg-brand-50 text-brand-700',
  Parkir: 'bg-indigo-50 text-indigo-700',
  Infaq: 'bg-emerald-50 text-emerald-700',
  Donasi: 'bg-teal-50 text-teal-700',
  'Saldo Awal': 'bg-slate-100 text-slate-600',
  'Lain-lain': 'bg-slate-100 text-slate-600',
  Operasional: 'bg-rose-50 text-rose-700',
  Rapat: 'bg-amber-50 text-amber-700',
  Perlengkapan: 'bg-purple-50 text-purple-700',
  Kesehatan: 'bg-emerald-50 text-emerald-700',
  Kegiatan: 'bg-sky-50 text-sky-700',
}

const ym = (d: Date) => d.toISOString().slice(0, 7)

export default function LaporanKeuanganPage() {
  const isAdmin = useAuth((s) => s.user?.role === 'admin')
  const [unitId, setUnitId] = useState<number | null>(null)
  const [bulan, setBulan] = useState(ym(new Date()))
  const [trxOpen, setTrxOpen] = useState(false)
  const [orgOpen, setOrgOpen] = useState(false)

  const { data: unitsData, isLoading: loadingUnits, isError: errUnits, error: errUnitsObj, refetch: refetchUnits } = useKasUnits()
  const units = unitsData?.data ?? []
  const unit = units.find((u) => u.id === unitId) ?? units[0]

  const { data: sumData, isLoading: loadingSum, isError: errSum, error: errSumObj, refetch: refetchSum } = useKasSummary(unit?.id ?? null, bulan)
  const s = sumData?.data

  return (
    <div className="animate-fade-up">
      <PageHeader
        title="Laporan Keuangan"
        subtitle="Kas per unit — wilayah (RT/RW/Kelurahan) maupun organisasi · iuran masuk otomatis dari pembayaran"
        actions={
          <div className="flex gap-2">
            <Button variant="secondary" onClick={() => setOrgOpen(true)}><Building2 size={15} /> Daftar Organisasi</Button>
            <Button onClick={() => setTrxOpen(true)} disabled={!unit}><Plus size={15} /> Transaksi</Button>
          </div>
        }
      />

      {/* Selector */}
      <Card className="mb-4 p-4">
        {loadingUnits ? (
          <Skeleton className="h-10" />
        ) : errUnits ? (
          <QueryError message={errUnitsObj?.message} onRetry={() => refetchUnits()} />
        ) : (
          <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <Select
              value={String(unit?.id ?? '')}
              onChange={(v) => setUnitId(Number(v))}
              placeholder="Pilih unit kas…"
              options={units.map((u) => ({
                value: String(u.id),
                label: u.jenis === 'organisasi' ? `${u.nama} ${u.parent_label ?? ''}` : u.nama,
              }))}
            />
            <Input type="month" value={bulan} onChange={(e) => setBulan(e.target.value || ym(new Date()))} />
            <div className="flex items-center gap-2 text-[12px] text-slate-400">
              <Landmark size={13} className="shrink-0" /> {unit ? `${unit.jenis.toUpperCase()} · ${unit.parent_label ?? unit.wilayah_nama ?? ''}` : ''}
            </div>
          </div>
        )}
      </Card>

      {!unit ? (
        <Card><p className="py-10 text-center text-sm text-slate-400">Belum ada unit kas dalam scope Anda.</p></Card>
      ) : loadingSum ? (
        <div className="space-y-4">{Array.from({ length: 2 }).map((_, i) => <Skeleton key={i} className="h-40" />)}</div>
      ) : errSum ? (
        <Card><QueryError message={errSumObj?.message} onRetry={() => refetchSum()} /></Card>
      ) : s && (
        <>
          {/* KPI */}
          <div className="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <Card className="p-5">
              <div className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400"><Wallet size={13} /> Saldo Awal</div>
              <p className="mt-2 text-[26px] font-extrabold tabular-nums text-slate-900">{fmtMoney(s.saldo_awal)}</p>
              <p className="mt-1 text-[11px] text-slate-400">s.d. bulan sebelumnya</p>
            </Card>
            <Card className="p-5">
              <div className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-emerald-600"><ArrowDownToLine size={13} /> Pemasukan</div>
              <p className="mt-2 text-[26px] font-extrabold tabular-nums text-emerald-700">{fmtMoney(s.pemasukan_iuran + s.pemasukan_lain)}</p>
              <p className="mt-1 text-[11px] text-slate-400">iuran {fmtMoney(s.pemasukan_iuran)} · lainnya {fmtMoney(s.pemasukan_lain)}</p>
            </Card>
            <Card className="p-5">
              <div className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-rose-600"><ArrowUpFromLine size={13} /> Pengeluaran</div>
              <p className="mt-2 text-[26px] font-extrabold tabular-nums text-rose-700">{fmtMoney(s.pengeluaran)}</p>
              <p className="mt-1 text-[11px] text-slate-400">{s.tx.filter((t) => t.keluar > 0).length} transaksi</p>
            </Card>
            <Card className="bg-brand-600 p-5">
              <div className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-brand-200"><Landmark size={13} /> Saldo Akhir</div>
              <p className="mt-2 text-[26px] font-extrabold tabular-nums text-white">{fmtMoney(s.saldo_akhir)}</p>
              <p className="mt-1 text-[11px] text-brand-200/70">{s.unit.nama} · {s.periode_label}</p>
            </Card>
          </div>

          {/* Buku kas */}
          <Card className="overflow-hidden">
            <div className="border-b border-line px-5 py-3.5 text-sm font-bold text-slate-900">
              Buku Kas — {s.unit.nama}
              <span className="ml-2 text-[11px] font-medium text-slate-400">{s.tx.length} entri · {s.periode_label}</span>
            </div>
            {s.tx.length === 0 ? (
              <p className="px-5 py-8 text-center text-sm text-slate-400">
                {s.unit.jenis === 'rt'
                  ? 'Belum ada transaksi. Iuran warga yang dibayarkan otomatis tercatat di sini — atau catat "Saldo Awal" (masuk) untuk opening balance.'
                  : s.unit.jenis === 'organisasi'
                    ? 'Belum ada transaksi. Mulai dengan catat "Saldo Awal" (masuk) sebagai modal awal organisasi.'
                    : 'Belum ada transaksi. Kas RW/Kelurahan tidak menerima iuran otomatis — mulai dengan catat "Saldo Awal" (masuk), lalu setoran dari RT sebagai pemasukan.'}
              </p>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full text-[13px]">
                  <thead>
                    <tr className="border-b border-line bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-400">
                      <th className="px-5 py-3 font-semibold">Tgl</th>
                      <th className="px-5 py-3 font-semibold">Keterangan</th>
                      <th className="px-5 py-3 font-semibold">Kategori</th>
                      <th className="px-5 py-3 text-right font-semibold">Masuk</th>
                      <th className="px-5 py-3 text-right font-semibold">Keluar</th>
                      <th className="px-5 py-3" />
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-line">
                    {s.tx.map((t) => (
                      <TrxRow key={t.id} t={t} />
                    ))}
                    <tr className="bg-slate-50 font-bold">
                      <td colSpan={4} className="px-5 py-3 text-right text-slate-600">Saldo akhir periode</td>
                      <td className="px-5 py-3 text-right tabular-nums text-brand-800">{fmtMoney(s.saldo_akhir)}</td>
                      <td />
                    </tr>
                  </tbody>
                </table>
              </div>
            )}
          </Card>
        </>
      )}

      {trxOpen && unit && <TrxModal unitId={unit.id} kategori={KATEGORI} isEmpty={s ? s.tx.length === 0 : false} onClose={() => setTrxOpen(false)} />}
      {orgOpen && <OrgModal onClose={() => setOrgOpen(false)} isAdmin={isAdmin} />}
    </div>
  )
}

/* ═══ Row + hapus manual ═══ */
function TrxRow({ t }: { t: { id: number; tgl: string; ket: string | null; kat: string; masuk: number; keluar: number; sumber: string } }) {
  const del = useDeleteKasTrx()
  return (
    <tr className="hover:bg-slate-50">
      <td className="px-5 py-3 whitespace-nowrap tabular-nums text-slate-500">{t.tgl}</td>
      <td className="px-5 py-3 text-slate-800">
        {t.ket ?? t.kat}
        {t.sumber === 'iuran' && <span className="ml-2 text-[10px] font-bold uppercase text-slate-300">auto</span>}
      </td>
      <td className="px-5 py-3">
        <span className={`rounded-full px-2 py-0.5 text-[11px] font-semibold ${KAT_COLOR[t.kat] ?? 'bg-slate-100 text-slate-600'}`}>{t.kat}</span>
      </td>
      <td className="px-5 py-3 text-right font-semibold tabular-nums text-emerald-700">{t.masuk ? fmtMoney(t.masuk) : '—'}</td>
      <td className="px-5 py-3 text-right font-semibold tabular-nums text-rose-700">{t.keluar ? fmtMoney(t.keluar) : '—'}</td>
      <td className="px-5 py-3 text-right">
        {t.sumber === 'manual' && (
          <Button size="sm" variant="ghost" className="text-rose-400 hover:bg-rose-50" onClick={() => del.mutate(t.id)} title="Hapus">
            <Trash2 size={13} />
          </Button>
        )}
      </td>
    </tr>
  )
}

/* ═══ Modal transaksi ═══ */
function TrxModal({ unitId, kategori, isEmpty, onClose }: { unitId: number; kategori: string[]; isEmpty: boolean; onClose: () => void }) {
  const create = useCreateKasTrx()
  const [tipe, setTipe] = useState<'masuk' | 'keluar'>('masuk')
  const [jumlah, setJumlah] = useState('')
  const [kat, setKat] = useState(isEmpty ? 'Saldo Awal' : 'Donasi')
  const [ket, setKet] = useState(isEmpty ? 'Saldo awal kas' : '')
  const [tanggal, setTanggal] = useState(ym(new Date()) + '-' + String(new Date().getDate()).padStart(2, '0'))

  return (
    <Modal open onClose={onClose} title="Catat Transaksi Kas" subtitle="Pemasukan manual / pengeluaran unit ini">
      <div className="space-y-4">
        <div className="grid grid-cols-2 gap-3">
          <div>
            <Label>Tipe *</Label>
            <Select value={tipe} onChange={(v) => setTipe(v as 'masuk' | 'keluar')}
              options={[{ value: 'masuk', label: 'Masuk (pemasukan)' }, { value: 'keluar', label: 'Keluar (pengeluaran)' }]} />
          </div>
          <div>
            <Label>Tanggal *</Label>
            <Input type="date" value={tanggal} onChange={(e) => setTanggal(e.target.value)} />
          </div>
        </div>
        <div className="grid grid-cols-2 gap-3">
          <div>
            <Label>Jumlah (Rp) *</Label>
            <Input type="number" min={100} value={jumlah} onChange={(e) => setJumlah(e.target.value)} className="tabular-nums" placeholder="mis. 50000" />
          </div>
          <div>
            <Label>Kategori *</Label>
            <Select value={kat} onChange={setKat} options={kategori.map((k) => ({ value: k, label: k }))} />
          </div>
        </div>
        <div>
          <Label>Keterangan</Label>
          <Input value={ket} onChange={(e) => setKet(e.target.value)} placeholder="mis. beli konsumsi rapat" />
        </div>
        <div className="flex justify-end gap-2 border-t border-line pt-4">
          <Button variant="secondary" onClick={onClose}>Batal</Button>
          <Button
            disabled={create.isPending || !jumlah || Number(jumlah) < 100 || !tanggal}
            onClick={() => create.mutate(
              { kas_unit_id: unitId, tipe, jumlah: Number(jumlah), kategori: kat, keterangan: ket || undefined, tanggal },
              { onSuccess: onClose },
            )}
          >
            {create.isPending ? 'Menyimpan…' : 'Simpan'}
          </Button>
        </div>
      </div>
    </Modal>
  )
}

/* ═══ Modal daftar organisasi — daftar baru + kelola existing ═══ */
function OrgModal({ onClose, isAdmin }: { onClose: () => void; isAdmin: boolean }) {
  const create = useCreateKasUnit()
  const delUnit = useDeleteKasUnit()
  const { data: tree } = useWilayahTree()
  const { data: unitsData } = useKasUnits()
  const [nama, setNama] = useState('')
  const [wilayahId, setWilayahId] = useState('')
  const [confirmTarget, setConfirmTarget] = useState<KasUnitItem | null>(null)

  const orgs = (unitsData?.data ?? []).filter((u) => u.jenis === 'organisasi')

  // flatten wilayah RT/RW/Kelurahan dari tree
  const opts: { value: string; label: string }[] = []
  ;(tree?.data ?? []).forEach((kel: { id: number; nama: string; children?: { id: number; nama: string; children?: { id: number; nama: string }[] }[] }) => {
    opts.push({ value: String(kel.id), label: kel.nama })
    ;(kel.children ?? []).forEach((rw) => {
      opts.push({ value: String(rw.id), label: `  ${rw.nama}` })
      ;(rw.children ?? []).forEach((rt) => opts.push({ value: String(rt.id), label: `    ${rt.nama}` }))
    })
  })

  return (
    <Modal open onClose={onClose} title="Organisasi Kas" subtitle="Daftar unit baru & kelola organisasi terdaftar">
      <div className="space-y-5">
        {/* Existing */}
        <div>
          <p className="mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
            Terdaftar · {orgs.length}
          </p>
          {orgs.length === 0 ? (
            <p className="rounded-xl border border-dashed border-line px-4 py-3 text-center text-[13px] text-slate-400">
              Belum ada organisasi terdaftar.
            </p>
          ) : (
            <div className="space-y-1.5">
              {orgs.map((o) => (
                <div key={o.id} className="flex items-center justify-between gap-3 rounded-xl border border-line px-3.5 py-2.5">
                  <div className="min-w-0">
                    <p className="truncate text-[13px] font-semibold text-slate-800">{o.nama}</p>
                    <p className="text-[11px] text-slate-400">{o.parent_label ?? o.wilayah_nama ?? '—'}</p>
                  </div>
                  {isAdmin && (
                    confirmTarget?.id === o.id ? (
                      <Button size="sm" variant="danger" disabled={delUnit.isPending}
                        onClick={() => delUnit.mutate(o.id, { onSuccess: () => setConfirmTarget(null) })}>
                        {delUnit.isPending ? 'Menghapus…' : 'Yakin hapus?'}
                      </Button>
                    ) : (
                      <Button size="sm" variant="ghost" className="text-rose-400 hover:bg-rose-50" title="Hapus"
                        onClick={() => setConfirmTarget(o)}>
                        <Trash2 size={13} />
                      </Button>
                    )
                  )}
                </div>
              ))}
            </div>
          )}
        </div>

        {/* Form baru */}
        <div className="border-t border-line pt-4">
          <p className="mb-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Daftarkan Baru</p>
          <div className="space-y-3">
            <div>
              <Label>Nama Organisasi *</Label>
              <Input value={nama} onChange={(e) => setNama(e.target.value)} placeholder="mis. Musholla Al-Ikhlas" />
            </div>
            <div>
              <Label>Menempel di wilayah *</Label>
              <Select value={wilayahId} onChange={setWilayahId} placeholder="Pilih RT / RW / Kelurahan…" options={opts} searchable />
            </div>
            <p className="text-[12px] leading-relaxed text-slate-400">
              Unit RT/RW/Kelurahan tidak perlu didaftarkan — otomatis tersedia dan iurannya tercatat dari pembayaran.
              Hapus organisasi hanya oleh admin; riwayat transaksinya tetap tersimpan (arsip).
            </p>
            <div className="flex justify-end">
              <Button
                disabled={create.isPending || !nama || !wilayahId}
                onClick={() => create.mutate({ nama, wilayah_id: Number(wilayahId) }, { onSuccess: () => { setNama(''); setWilayahId('') } })}
              >
                <Plus size={14} /> {create.isPending ? 'Menyimpan…' : 'Daftarkan'}
              </Button>
            </div>
          </div>
        </div>
      </div>
    </Modal>
  )
}
