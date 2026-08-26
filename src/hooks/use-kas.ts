'use client'

import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { toast } from 'sonner'
import { api } from '@/lib/api-client'

/* ═══════════ Kas — unit & laporan keuangan ═══════════ */

export interface KasUnitItem {
  id: number
  nama: string
  jenis: 'rt' | 'rw' | 'kelurahan' | 'kecamatan' | 'organisasi'
  wilayah_nama: string | null
  kelurahan_nama?: string | null
  rw_nama?: string | null
  parent_label: string | null
}

export interface KasSummary {
  unit: { id: number; nama: string; jenis: string; parent_label: string | null }
  periode_label: string
  saldo_awal: number
  pemasukan_iuran: number
  pemasukan_lain: number
  pengeluaran: number
  saldo_akhir: number
  tx_count: number
  tx_keluar_count: number
  tren: { bulan: string; masuk: number; keluar: number }[]
  tx: { id: number; tgl: string; ket: string | null; kat: string; masuk: number; keluar: number; sumber: string }[]
}

function qs(p: Record<string, string | number | undefined>) {
  const u = new URLSearchParams()
  Object.entries(p).forEach(([k, v]) => { if (v !== undefined && v !== '') u.set(k, String(v)) })
  return u.toString()
}

/** Unit kas dalam scope user (auth). */
export function useKasUnits() {
  return useQuery({
    queryKey: ['kas-units'],
    queryFn: () => api.get<{ data: KasUnitItem[] }>('/kas/units'),
  })
}

/** Summary kas (auth — scoped). Periode custom via mulai+sampai; q/tipe = filter daftar transaksi. */
export function useKasSummary(unitId: number | null, params?: { mulai?: string; sampai?: string; q?: string; tipe?: string }) {
  const { mulai, sampai, q, tipe } = params ?? {}
  return useQuery({
    queryKey: ['kas-summary', unitId, mulai, sampai, q, tipe],
    queryFn: () => api.get<{ data: KasSummary }>(`/kas/summary?${qs({ unit_id: unitId ?? undefined, mulai, sampai, q, tipe })}`),
    enabled: !!unitId && !!mulai && !!sampai && mulai <= sampai,
  })
}

/** Daftarkan unit organisasi. */
export function useCreateKasUnit() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: (body: { nama: string; wilayah_id: number }) => api.post('/kas/units', body),
    onSuccess: () => {
      toast.success('Organisasi terdaftar')
      qc.invalidateQueries({ queryKey: ['kas-units'] })
    },
    onError: (e) => toast.error(e instanceof Error ? e.message : 'Gagal mendaftarkan'),
  })
}

/** Hapus unit organisasi (admin only). */
export function useDeleteKasUnit() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: (id: number) => api.delete(`/kas/units/${id}`),
    onSuccess: () => {
      toast.success('Organisasi dihapus')
      qc.invalidateQueries({ queryKey: ['kas-units'] })
    },
    onError: (e) => toast.error(e instanceof Error ? e.message : 'Gagal menghapus'),
  })
}

export interface KasTrxInput {
  kas_unit_id: number
  tipe: 'masuk' | 'keluar'
  jumlah: number
  kategori: string
  keterangan?: string
  tanggal: string
}

export function useCreateKasTrx() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: (body: KasTrxInput) => api.post('/kas/transaksis', body),
    onSuccess: () => {
      toast.success('Transaksi dicatat')
      qc.invalidateQueries({ queryKey: ['kas-summary'] })
    },
    onError: (e) => toast.error(e instanceof Error ? e.message : 'Gagal menyimpan'),
  })
}

export function useDeleteKasTrx() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: (id: number) => api.delete(`/kas/transaksis/${id}`),
    onSuccess: () => {
      toast.success('Transaksi dihapus')
      qc.invalidateQueries({ queryKey: ['kas-summary'] })
    },
    onError: (e) => toast.error(e instanceof Error ? e.message : 'Gagal menghapus'),
  })
}

/** Portal publik — tanpa auth. */
export function usePortalKasUnits() {
  return useQuery({
    queryKey: ['portal-kas-units'],
    queryFn: () => api.get<{ data: KasUnitItem[] }>('/portal/kas/units'),
  })
}

export function usePortalKasSummary(unitId: number | null, bulan?: string) {
  return useQuery({
    queryKey: ['portal-kas-summary', unitId, bulan],
    queryFn: () => api.get<{ data: KasSummary }>(`/portal/kas/summary?${qs({ unit_id: unitId ?? undefined, bulan })}`),
    enabled: !!unitId,
  })
}
