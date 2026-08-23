/* ── Shared ── */
export interface Paginated<T> {
  data: T[]
  meta: { current_page: number; last_page: number; per_page: number; total: number }
}

export interface WilayahRef {
  id: number
  kode: string
  nama: string
  tingkat: 'Kelurahan' | 'RW' | 'RT'
  parent?: WilayahRef | null
  children?: WilayahRef[]
  total_kk?: number
}

/* ── Warga ── */
export interface Warga {
  id: number
  nik: string
  nama_lengkap: string
  tempat_lahir: string | null
  tanggal_lahir: string | null
  jenis_kelamin: 'L' | 'P'
  golongan_darah: string | null
  agama: string
  status_perkawinan: string
  pekerjaan: string
  kewarganegaraan: string
  pendidikan_terakhir: string
  hubungan_keluarga: string
  kk_id: number | null
  no_telepon: string | null
  email: string | null
  nama_ayah: string | null
  nama_ibu: string | null
  meninggal: boolean
  tanggal_meninggal: string | null
  is_verified: boolean
  verified_at: string | null
  foto_ktp: string | null
  keluarga?: KeluargaRef | null
  umur?: number
}

export interface KeluargaRef {
  id: number
  no_kk: string
  kepala_keluarga_id?: number | null
  rt_id?: number | null
  nama_kepala_keluarga?: string | null
  kepala_keluarga?: { id: number; nama_lengkap: string } | null
  wilayah?: WilayahRef | null
}

/* ── Keluarga ── */
export interface Keluarga {
  id: number
  no_kk: string
  kepala_keluarga_id: number | null
  kepala_keluarga?: Warga | null
  nama_kepala_keluarga?: string | null
  alamat_kk: string
  rt_kk: string | null
  rw_kk: string | null
  kelurahan_kk: string | null
  kecamatan_kk: string | null
  kabupaten_kk: string | null
  provinsi_kk: string | null
  alamat_domisili: string | null
  rt_id: number | null
  wilayah?: WilayahRef | null
  status_domisili_keluarga?: string | null // dipensiunkan — status keluarga kini berbasis domisili
  tanggal_mulai_domisili_keluarga?: string | null
  status_keluarga: string
  status_miskin?: 'Miskin' | 'Pra-Miskin' | 'Non' | null
  is_verified?: boolean
  verified_at?: string | null
  status_label?: string
  keterangan_status: string | null
  tanggal_status: string | null
  foto_kk?: string | null
  anggota_keluarga_count?: number
  anggota_keluarga?: Warga[]
  keluarga_iuran?: KeluargaIuranConn[]
}

export interface KeluargaIuranConn {
  id: number
  keluarga_id: number
  jenis_iuran_id: number
  nominal_custom: number | null
  alasan_custom: string | null
  status_aktif: boolean
  jenis_iuran?: JenisIuran
  keluarga?: KeluargaRef | null
}

/* ── Iuran ── */
export interface JenisIuran {
  id: number
  nama: string
  kode: string
  jumlah: number
  periode: 'bulanan' | 'tahunan' | 'sekali'
  periode_label?: string
  keterangan: string | null
  is_aktif: boolean
  koneksi_aktif?: number
}

export interface Iuran {
  id: number
  kk_id: number
  jenis_iuran_id: number
  periode_bulan: string
  nominal: number
  denda_terlambatan: number
  status: 'belum_bayar' | 'lunas'
  jatuh_tempo: string | null
  keterangan: string | null
  keluarga?: KeluargaRef | null
  jenis_iuran?: JenisIuran | null
  total_dibayar?: number
}

export interface Pembayaran {
  id: number
  jumlah_bayar: number
  metode_pembayaran: 'cash' | 'transfer' | 'qris' | 'ewallet'
  nomor_referensi: string | null
  keterangan: string | null
  petugas?: string | null
  created_at: string
}

/* ── Dashboard ── */
export interface DashboardData {
  role: string
  total_warga: number
  warga_laki: number
  warga_perempuan: number
  total_keluarga: number
  total_tagihan_iuran: number
  pemasukan_bulan_ini: number
  total_rt?: number
  total_rw?: number
  warga_per_rw?: Record<string, number>
  warga_per_rt?: Record<string, number>
  pembayaran_tren: { bulan: string; total: number }[]
  recent_activities: {
    id: number
    user: string
    action: string
    module: string
    description: string
    created_at: string
  }[]
  pending_iuran?: {
    id: number
    keluarga: string | null
    jenis: string | null
    periode: string
    nominal: number
    jatuh_tempo: string | null
  }[]
}

/* ── Users ── */
export interface SiwaUser {
  id: number
  name: string
  username: string
  email: string | null
  role: 'admin' | 'camat' | 'lurah' | 'rw' | 'rt'
  status_aktif: number | boolean
  avatar: string | null
  user_wilayah?: { id: number; wilayah: WilayahRef }[]
}

/* ── Aktivitas ── */
export interface Aktivitas {
  id: number
  user_id: number | null
  user?: { id: number; name: string; username: string; role: string } | null
  action: string
  module: string
  description: string
  ip_address: string | null
  created_at: string
}

/* ── Laporan ── */
export interface LaporanKependudukan {
  rows: { rt: string; rw: string; total_kk: number; laki: number; perempuan: number; total_warga: number }[]
  totals: { total_kk: number; laki: number; perempuan: number; total_warga: number }
}
