'use client'

import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { toast } from 'sonner'
import { api } from '@/lib/api-client'
import type { DashboardData, Iuran, JenisIuran, Keluarga, KeluargaIuranConn, Paginated, Pembayaran, SiwaUser, Warga, WilayahRef } from '@/types'

/* ═══════════ Dashboard ═══════════ */
export function useDashboard() {
  return useQuery({
    queryKey: ['dashboard'],
    queryFn: () => api.get<{ data: DashboardData }>('/dashboard'),
    staleTime: 30_000,
  })
}

/* ═══════════ Warga ═══════════ */
export interface WargaFilters {
  search?: string
  jenis_kelamin?: string
  agama?: string
  pendidikan?: string
  meninggal?: string
  is_verified?: string
  kk_id?: number
  status_kk?: string
  page?: number
  per_page?: number
}

export function useWargaList(filters: WargaFilters) {
  return useQuery({
    queryKey: ['warga', filters],
    queryFn: () => api.get<Paginated<Warga>>(`/warga?${new URLSearchParams(clean(filters))}`),
    placeholderData: (prev) => prev,
  })
}

export function useWarga(id: number | null) {
  return useQuery({
    queryKey: ['warga', id],
    queryFn: () => api.get<{ data: Warga }>(`/warga/${id}`),
    enabled: !!id,
  })
}

function clean(obj: Record<string, unknown> | object): Record<string, string> {
  return Object.fromEntries(
    Object.entries(obj as Record<string, unknown>).filter(([, v]) => v !== undefined && v !== '' && v !== null).map(([k, v]) => [k, String(v)]),
  )
}

export function useWargaMutations() {
  const qc = useQueryClient()
  const invalidate = () => {
    qc.invalidateQueries({ queryKey: ['warga'] })
    qc.invalidateQueries({ queryKey: ['dashboard'] })
    qc.invalidateQueries({ queryKey: ['warga-stats'] })
  }

  const create = useMutation({
    mutationFn: (payload: Record<string, string>) => api.post('/warga', payload),
    onSuccess: () => { toast.success('Warga ditambahkan'); invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })

  const update = useMutation({
    mutationFn: ({ id, payload }: { id: number; payload: Record<string, string> }) => api.put(`/warga/${id}`, payload),
    onSuccess: () => { toast.success('Warga diperbarui'); invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })

  const remove = useMutation({
    mutationFn: (id: number) => api.delete(`/warga/${id}`),
    onSuccess: () => { toast.success('Warga dihapus'); invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })

  const verify = useMutation({
    mutationFn: ({ id, verified }: { id: number; verified?: boolean }) =>
      api.post(`/warga/${id}/verify`, verified !== undefined ? { verified } : undefined),
    onSuccess: (_d, v) => { toast.success(v.verified === false ? 'Verifikasi data dibatalkan' : 'Data warga diverifikasi'); invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })

  return { create, update, remove, verify }
}

export function useWargaStats() {
  return useQuery({
    queryKey: ['warga-stats'],
    queryFn: () => api.get<{ data: Record<string, number | Record<string, number>> }>('/warga/statistics'),
    staleTime: 60_000,
  })
}

/* ═══════════ Keluarga ═══════════ */
export interface KeluargaFilters {
  search?: string
  status?: string
  rt_id?: number
  page?: number
  per_page?: number
}

export function useKeluargaList(filters: KeluargaFilters) {
  return useQuery({
    queryKey: ['keluarga', filters],
    queryFn: () => api.get<Paginated<Keluarga>>(`/keluarga?${new URLSearchParams(clean(filters))}`),
    placeholderData: (prev) => prev,
  })
}

export function useKeluarga(id: number | null) {
  return useQuery({
    queryKey: ['keluarga', id],
    queryFn: () => api.get<{ data: Keluarga }>(`/keluarga/${id}`),
    enabled: !!id,
  })
}

export function useKeluargaMutations() {
  const qc = useQueryClient()
  const invalidate = () => {
    qc.invalidateQueries({ queryKey: ['keluarga'] })
    qc.invalidateQueries({ queryKey: ['dashboard'] })
  }

  const create = useMutation({
    mutationFn: (payload: Record<string, unknown>) => api.post('/keluarga', payload),
    onSuccess: () => { toast.success('Keluarga ditambahkan'); invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })

  const update = useMutation({
    mutationFn: ({ id, payload }: { id: number; payload: Record<string, unknown> }) => api.put(`/keluarga/${id}`, payload),
    onSuccess: () => { toast.success('Keluarga diperbarui'); invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })

  const remove = useMutation({
    mutationFn: (id: number) => api.delete(`/keluarga/${id}`),
    onSuccess: () => { toast.success('Keluarga dihapus'); invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })

  const addMember = useMutation({
    mutationFn: ({ id, warga_id, hubungan_keluarga }: { id: number; warga_id: number; hubungan_keluarga: string }) =>
      api.post(`/keluarga/${id}/members`, { warga_id, hubungan_keluarga }),
    onSuccess: () => { toast.success('Anggota ditambahkan'); invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })

  const removeMember = useMutation({
    mutationFn: ({ id, warga_id }: { id: number; warga_id: number }) =>
      api.delete(`/keluarga/${id}/members/${warga_id}`),
    onSuccess: () => { toast.success('Anggota dikeluarkan'); invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })

  const updateStatus = useMutation({
    mutationFn: ({ id, status_keluarga, keterangan_status }: { id: number; status_keluarga: string; keterangan_status?: string }) =>
      api.patch(`/keluarga/${id}/status`, { status_keluarga, keterangan_status }),
    onSuccess: () => { toast.success('Status keluarga diperbarui'); invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })

  const verify = useMutation({
    mutationFn: ({ id, verified }: { id: number; verified?: boolean }) =>
      api.post(`/keluarga/${id}/verify`, verified !== undefined ? { verified } : undefined),
    onSuccess: (_d, v) => {
      toast.success(v.verified === false ? 'Verifikasi KK dibatalkan (beserta anggota)' : 'KK diverifikasi beserta seluruh anggota')
      invalidate()
      qc.invalidateQueries({ queryKey: ['warga'] })
      qc.invalidateQueries({ queryKey: ['warga-stats'] })
    },
    onError: (e) => toast.error(errMsg(e)),
  })

  const uploadFotoRumah = useMutation({
    mutationFn: ({ id, file }: { id: number; file: File }) => {
      const form = new FormData()
      form.append('foto', file)
      return api.upload(`/keluarga/${id}/foto-rumah`, form)
    },
    onSuccess: () => { toast.success('Foto rumah tersimpan'); invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })

  const deleteFotoRumah = useMutation({
    mutationFn: (id: number) => api.delete(`/keluarga/${id}/foto-rumah`),
    onSuccess: () => { toast.success('Foto rumah dihapus'); invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })

  return { create, update, remove, addMember, removeMember, updateStatus, verify, uploadFotoRumah, deleteFotoRumah }
}

/* ═══════════ Wilayah ═══════════ */
export function useWilayahTree() {
  return useQuery({
    queryKey: ['wilayah-tree'],
    queryFn: () => api.get<{ data: WilayahRef[] }>('/wilayah/tree'),
    staleTime: 5 * 60_000,
  })
}

export function useRtOptions() {
  return useQuery({
    queryKey: ['rt-options'],
    queryFn: () => api.get<{ data: WilayahRef[] }>('/wilayah?tingkat=RT'),
    staleTime: 5 * 60_000,
  })
}

/* ═══════════ Iuran ═══════════ */
export interface IuranFilters {
  search?: string
  periode?: string
  status?: string
  keluarga_id?: number
  jenis_iuran_id?: number
  page?: number
  per_page?: number
}

export function useIuranList(filters: IuranFilters) {
  return useQuery({
    queryKey: ['iuran', filters],
    queryFn: () => api.get<Paginated<Iuran>>(`/iuran?${new URLSearchParams(clean(filters))}`),
    placeholderData: (prev) => prev,
  })
}

export function useIuranStats(filters?: Partial<IuranFilters>) {
  return useQuery({
    queryKey: ['iuran-stats', filters],
    queryFn: () => api.get<{ data: Record<string, number> }>(`/iuran/statistics?${new URLSearchParams(clean(filters ?? {}))}`),
  })
}

export function useBayar() {
  const qc = useQueryClient()
  return useMutation({
    // Tanpa jumlah — pembayaran selalu nominal penuh (tidak ada bayar sebagian)
    mutationFn: ({ id, ...body }: { id: number; metode_pembayaran: string; keterangan?: string }) =>
      api.post(`/iuran/${id}/bayar`, body),
    onSuccess: () => {
      toast.success('Pembayaran dicatat')
      qc.invalidateQueries({ queryKey: ['iuran'] })
      qc.invalidateQueries({ queryKey: ['dashboard'] })
    },
    onError: (e) => toast.error(errMsg(e)),
  })
}

/** Multi pembayaran (rapelan) — beberapa tagihan lunas sekaligus, satu metode. */
export function useBayarBatch() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: (body: { payments: { iuran_id: number }[]; metode_pembayaran: string; keterangan?: string }) =>
      api.post<{ data: { dibayar: number; total: number; gagal: { iuran_id: number; alasan: string }[] } }>('/iuran/bayar-batch', body),
    onSuccess: (r) => {
      const d = r.data
      if (d.gagal.length > 0) toast.warning(`${d.dibayar} tagihan lunas · ${d.gagal.length} dilewati`)
      else toast.success(`${d.dibayar} tagihan lunas · total ${d.total.toLocaleString('id-ID')}`)
      qc.invalidateQueries({ queryKey: ['iuran'] })
      qc.invalidateQueries({ queryKey: ['iuran-stats'] })
      qc.invalidateQueries({ queryKey: ['dashboard'] })
    },
    onError: (e) => toast.error(errMsg(e)),
  })
}

export function usePayments(iuranId: number | null) {
  return useQuery({
    queryKey: ['payments', iuranId],
    queryFn: () => api.get<{ data: Pembayaran[] }>(`/iuran/${iuranId}/payments`),
    enabled: !!iuranId,
  })
}

/* ── Generate ── */
export function useGenerationPreview(params: { periode_bulan?: string; rt_id?: number; jenis_iuran_ids?: number[] }, enabled: boolean) {
  return useQuery({
    queryKey: ['gen-preview', params],
    queryFn: () => {
      // Array harus dikirim sebagai jenis_iuran_ids[]=a&jenis_iuran_ids[]=b (URLSearchParams meringkus array jadi "1,2")
      const qs = new URLSearchParams(clean({ periode_bulan: params.periode_bulan, rt_id: params.rt_id }))
      ;(params.jenis_iuran_ids ?? []).forEach((id) => qs.append('jenis_iuran_ids[]', String(id)))
      return api.get<{ data: GenerationPreview }>(`/iuran/generation/preview?${qs}`)
    },
    enabled: enabled && !!params.periode_bulan,
  })
}

export interface GenerationPreview {
  preview: {
    kk_id: number
    no_kk: string
    kepala_keluarga: string
    rt: string | null
    iurans: { jenis_iuran_id: number; jenis_iuran: string; nominal: number }[]
    skip: { jenis_iuran_id: number; jenis_iuran: string; nominal: number; alasan: string }[]
    total: number
    sudah_ada: number
  }[]
  summary: { total_families: number; total_iuran: number; total_nominal: number; periode: string }
}

export function useGenerate() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: (params: { periode_bulan: string; rt_id?: number; jenis_iuran_ids?: number[] }) =>
      api.post<{ data: { generated: number; duplicates: number } }>('/iuran/generation/generate', params),
    onSuccess: (res) => {
      toast.success(`Generate selesai: ${res.data.generated} tagihan dibuat, ${res.data.duplicates} duplikat di-skip`)
      qc.invalidateQueries({ queryKey: ['iuran'] })
      qc.invalidateQueries({ queryKey: ['gen-preview'] })
      qc.invalidateQueries({ queryKey: ['dashboard'] })
    },
    onError: (e) => toast.error(errMsg(e)),
  })
}

export function useGenerationRtOptions() {
  return useQuery({
    queryKey: ['gen-rt-options'],
    queryFn: () => api.get<{ data: WilayahRef[] }>('/iuran/generation/rt-options'),
  })
}

/* ── Jenis Iuran ── */
export function useJenisIuranList(onlyActive = false) {
  return useQuery({
    queryKey: ['jenis-iuran', onlyActive],
    queryFn: () => api.get<{ data: JenisIuran[] }>(`/jenis-iuran${onlyActive ? '?only_active=1' : ''}`),
  })
}

export function useJenisIuranMutations() {
  const qc = useQueryClient()
  const invalidate = () => qc.invalidateQueries({ queryKey: ['jenis-iuran'] })

  const create = useMutation({
    mutationFn: (payload: Record<string, unknown>) => api.post('/jenis-iuran', payload),
    onSuccess: () => { toast.success('Jenis iuran ditambahkan'); invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })
  const update = useMutation({
    mutationFn: ({ id, payload }: { id: number; payload: Record<string, unknown> }) => api.put(`/jenis-iuran/${id}`, payload),
    onSuccess: () => { toast.success('Jenis iuran diperbarui'); invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })
  const remove = useMutation({
    mutationFn: (id: number) => api.delete(`/jenis-iuran/${id}`),
    onSuccess: () => { toast.success('Jenis iuran dihapus'); invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })
  const toggle = useMutation({
    mutationFn: (id: number) => api.put(`/jenis-iuran/${id}/toggle-status`),
    onSuccess: () => { invalidate() },
    onError: (e) => toast.error(errMsg(e)),
  })

  return { create, update, remove, toggle }
}

/* ── Keluarga Iuran connections ── */
export function useKeluargaIuranList(filters: { search?: string; jenis_iuran_id?: number; status_aktif?: string }) {
  return useQuery({
    queryKey: ['keluarga-iuran', filters],
    queryFn: () => api.get<{ data: KeluargaIuranConn[]; meta: { total: number; aktif: number; custom_nominal: number } }>(`/keluarga-iuran?${new URLSearchParams(clean(filters))}`),
  })
}

export function useAvailableJenisIuran(keluargaId: number | null) {
  return useQuery({
    queryKey: ['available-jenis', keluargaId],
    queryFn: () => api.get<{ data: JenisIuran[] }>(`/keluarga/${keluargaId}/iuran-available`),
    enabled: !!keluargaId,
  })
}

export function useConnectIuran() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: ({ keluargaId, ...body }: { keluargaId: number; jenis_iuran_id: number; nominal_custom?: number | null; status_aktif?: boolean }) =>
      api.post(`/keluarga/${keluargaId}/iuran`, body),
    onSuccess: () => {
      toast.success('Iuran dihubungkan')
      qc.invalidateQueries({ queryKey: ['keluarga'] })
      qc.invalidateQueries({ queryKey: ['keluarga-iuran'] })
      qc.invalidateQueries({ queryKey: ['available-jenis'] })
    },
    onError: (e) => toast.error(errMsg(e)),
  })
}

export function useUpdateConnIuran() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: ({ id, ...body }: { id: number; nominal_custom?: number | null; status_aktif?: boolean; alasan_custom?: string }) =>
      api.put(`/keluarga-iuran/${id}`, body),
    onSuccess: () => {
      toast.success('Koneksi diperbarui')
      qc.invalidateQueries({ queryKey: ['keluarga'] })
      qc.invalidateQueries({ queryKey: ['keluarga-iuran'] })
    },
    onError: (e) => toast.error(errMsg(e)),
  })
}

export function useDisconnectIuran() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: (id: number) => api.delete(`/keluarga-iuran/${id}`),
    onSuccess: () => {
      toast.success('Koneksi diputus')
      qc.invalidateQueries({ queryKey: ['keluarga'] })
      qc.invalidateQueries({ queryKey: ['keluarga-iuran'] })
      qc.invalidateQueries({ queryKey: ['available-jenis'] })
    },
    onError: (e) => toast.error(errMsg(e)),
  })
}

/* ═══════════ Helpers ═══════════ */
import { ApiError } from '@/lib/api-client'

function errMsg(e: unknown): string {
  if (e instanceof ApiError) {
    const first = e.errors ? Object.values(e.errors)[0]?.[0] : null
    return first ?? e.message
  }
  if (e instanceof Error && e.message) return e.message
  return 'Terjadi kesalahan'
}

/* ═══════════ Users (P3) ═══════════ */
export function useUserList(filters: { search?: string; role?: string; status?: string; page?: number }) {
  return useQuery({
    queryKey: ['users', filters],
    queryFn: () => api.get<Paginated<SiwaUser>>(`/users?${new URLSearchParams(clean(filters))}`),
    placeholderData: (prev) => prev,
  })
}
