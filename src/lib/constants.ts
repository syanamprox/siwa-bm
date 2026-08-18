import {
  Users,
  Home,
  Landmark,
  Coins,
  Map,
  FileBarChart,
  ShieldCheck,
  type LucideIcon,
} from 'lucide-react'

export type SiwaRole = 'admin' | 'lurah' | 'rw' | 'rt'

export interface SiwaModule {
  key: string
  label: string
  href: string
  icon: LucideIcon
  match: string[]
  /** role yang boleh melihat modul ini di sidebar */
  roles: SiwaRole[]
  nav?: (ModuleNavItem | { section: string })[]
}

export interface ModuleNavItem {
  label: string
  href: string
  match: string[]
  icon: LucideIcon
  roles?: SiwaRole[]
}

/**
 * Registry modul SIWA — urutan ini = urutan di sidebar rail.
 * Role gating: RT paling restriktif, admin melihat semua.
 */
export const MODULES: SiwaModule[] = [
  {
    key: 'kependudukan',
    label: 'Warga',
    href: '/warga',
    icon: Users,
    match: ['/warga'],
    roles: ['admin', 'lurah', 'rw', 'rt'],
    nav: [
      { label: 'Data Warga', href: '/warga', match: ['/warga'], icon: Users },
      { label: 'Kartu Keluarga', href: '/keluarga', match: ['/keluarga'], icon: Home },
    ],
  },
  {
    key: 'iuran',
    label: 'Iuran',
    href: '/iuran',
    icon: Coins,
    match: ['/iuran'],
    roles: ['admin', 'lurah', 'rw', 'rt'],
    nav: [
      { label: 'Tagihan', href: '/iuran', match: ['/iuran'], icon: Coins },
      { label: 'Jenis Iuran', href: '/iuran/jenis', match: ['/iuran/jenis'], icon: Landmark },
      { label: 'Konfigurasi KK', href: '/iuran/keluarga', match: ['/iuran/keluarga'], icon: Home },
      { label: 'Generate Tagihan', href: '/iuran/generate', match: ['/iuran/generate'], icon: FileBarChart },
    ],
  },
  {
    key: 'wilayah',
    label: 'Wilayah',
    href: '/wilayah',
    icon: Map,
    match: ['/wilayah'],
    roles: ['admin'],
    nav: [
      { label: 'Struktur Wilayah', href: '/wilayah', match: ['/wilayah'], icon: Map },
    ],
  },
  {
    key: 'laporan',
    label: 'Laporan',
    href: '/laporan',
    icon: FileBarChart,
    match: ['/laporan'],
    roles: ['admin', 'lurah', 'rw'],
    nav: [
      { label: 'Kependudukan', href: '/laporan/kependudukan', match: ['/laporan/kependudukan'], icon: FileBarChart },
      { label: 'Wilayah', href: '/laporan/wilayah', match: ['/laporan/wilayah'], icon: Map },
    ],
  },
  {
    key: 'admin',
    label: 'Admin',
    href: '/users',
    icon: ShieldCheck,
    match: ['/users', '/pengaturan', '/backup', '/changelog', '/aktivitas'],
    roles: ['admin'],
    nav: [
      { label: 'Pengguna', href: '/users', match: ['/users'], icon: ShieldCheck },
      { label: 'Pengaturan', href: '/pengaturan', match: ['/pengaturan'], icon: ShieldCheck },
      { label: 'Backup', href: '/backup', match: ['/backup'], icon: ShieldCheck },
      { label: 'Log Aktivitas', href: '/aktivitas', match: ['/aktivitas'], icon: ShieldCheck },
    ],
  },
]

export function modulesForRole(role: SiwaRole | undefined): SiwaModule[] {
  if (!role) return []
  return MODULES.filter((m) => m.roles.includes(role))
}
