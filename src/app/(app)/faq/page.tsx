'use client'

import { useState } from 'react'
import {
  BookOpen, Home, Coins, Users, ShieldCheck, Landmark, CircleHelp,
} from 'lucide-react'
import { PageHeader } from '@/components/PageHeader'
import { Card, StatusBadge } from '@/components/ui/primitives'
import { cn } from '@/lib/utils'

/* ──────────────────────────────────────────────
 * FAQ & Dokumentasi SIWA — definisi & konsep sistem.
 * Accordion native <details> (zero JS), badge nyata sebagai contoh visual.
 * ────────────────────────────────────────────── */

interface FaqItem {
  q: string
  a: React.ReactNode
}

interface FaqSection {
  id: string
  title: string
  icon: React.ReactNode
  items: FaqItem[]
}

const SECTIONS: FaqSection[] = [
  {
    id: 'keluarga',
    title: 'Kartu Keluarga & Status',
    icon: <Home size={16} />,
    items: [
      {
        q: 'Apa arti 4 status keluarga (Tetap, Domisili, Non Domisili, Pendatang)?',
        a: (
          <div className="space-y-2">
            <p>Status keluarga berbasis <strong>domisili faktual</strong> — siapa yang benar-benar tinggal di wilayah:</p>
            <ul className="ml-4 list-disc space-y-1.5">
              <li><strong>Tetap</strong> — KTP lokal + tinggal di wilayah. Warga asli yang menetap.</li>
              <li><strong>Domisili</strong> — KTP luar + tinggal lama di wilayah (kerja/nikah, belum pindah KTP).</li>
              <li><strong>Non Domisili</strong> — KTP terdaftar di sini, tapi orangnya tinggal di luar (perantau).</li>
              <li><strong>Pendatang</strong> — KTP luar + baru saja pindah tinggal di wilayah.</li>
            </ul>
            <div className="mt-2 flex flex-wrap gap-2">
              <StatusBadge status="active" label="Tetap" />
              <StatusBadge status="approved" label="Domisili" />
              <StatusBadge status="draft" label="Non Domisili" />
              <StatusBadge status="pending" label="Pendatang" />
            </div>
            <p className="mt-2 text-slate-500">Semua status <strong>tetap ditagih iuran</strong>. Keluarga yang sudah tidak tercatat lagi = dihapus (arsip soft-delete), bukan diubah statusnya.</p>
          </div>
        ),
      },
      {
        q: 'Apa itu alamat KTP vs alamat domisili?',
        a: (
          <p>Alamat KTP adalah alamat resmi sesuai kartu keluarga (tetap, mengikuti KK). Alamat domisili adalah tempat tinggal nyata keluarga sekarang — ditentukan lewat wilayah RT tempat mereka tinggal. Keduanya bisa berbeda (mis. KTP masih alamat kampung, tapi tinggal di perumahan).</p>
        ),
      },
      {
        q: 'Bagaimana mengganti Kepala Keluarga?',
        a: (
          <p>Buka detail KK (klik baris keluarga) → pada kartu anggota, tombol mahkota <strong>Jadikan Kepala</strong> di samping anggota yang dituju. Sistem otomatis memindahkan status kepala keluarga dari anggota lama ke yang baru. Kepala Keluarga tidak bisa dihapus sebelum diganti.</p>
        ),
      },
      {
        q: 'Kenapa ada warga "tanpa KK"?',
        a: (
          <p>Warga tanpa KK adalah orang yang tercatat sebagai warga tapi belum terhubung ke kartu keluarga mana pun (mis. baru pindah, data belum lengkap). Warga ini tidak terlihat oleh login RT/RW — hanya admin & lurah — sampai dimasukkan ke sebuah KK.</p>
        ),
      },
    ],
  },
  {
    id: 'iuran',
    title: 'Iuran & Pembayaran',
    icon: <Coins size={16} />,
    items: [
      {
        q: 'Bagaimana alur kerja modul iuran?',
        a: (
          <div className="space-y-1.5">
            <p>Empat langkah:</p>
            <ol className="ml-4 list-decimal space-y-1">
              <li><strong>Jenis Iuran</strong> — admin mendefinisikan jenis (Kebersihan, Keamanan, dst) + nominal default &amp; periodenya.</li>
              <li><strong>Konfigurasi KK</strong> — hubungkan KK ke jenis iuran yang berlaku (nominal bisa custom per KK).</li>
              <li><strong>Generate Tagihan</strong> — tiap periode (bulanan/tahunan), tagihan dibuat massal per RT dengan pratinjau dulu.</li>
              <li><strong>Bayar</strong> — petugas mencatat pembayaran di halaman Tagihan (tunai/transfer, bisa bertahap).</li>
            </ol>
          </div>
        ),
      },
      {
        q: 'Apa arti status tagihan (Lunas, Belum Bayar)?',
        a: (
          <div className="space-y-2">
            <div className="flex flex-wrap gap-2">
              <StatusBadge status="lunas" label="Lunas" />
              <StatusBadge status="belum lunas" label="Belum Bayar" />
            </div>
            <ul className="ml-4 list-disc space-y-1">
              <li><strong>Lunas</strong> — tagihan sudah dibayar penuh.</li>
              <li><strong>Belum Bayar</strong> — belum ada pembayaran.</li>
            </ul>
            <p className="mt-1.5">Pembayaran selalu <strong>nominal penuh</strong> — tidak ada bayar sebagian/cicilan. Untuk menagih tunggakan beberapa bulan sekaligus, gunakan centang pada daftar tagihan lalu <strong>Bayar Terpilih</strong> (rapelan).</p>
          </div>
        ),
      },
      {
        q: 'Kenapa KK tertentu tidak ikut saat generate tagihan?',
        a: (
          <p>Generate hanya memproses KK yang <strong>terhubung ke jenis iuran tersebut dengan status aktif</strong> (lihat Konfigurasi KK). Jika KK tidak muncul di pratinjau, kemungkinan koneksinya belum dibuat atau sedang nonaktif. Generate juga idempotent — KK yang sudah punya tagihan di periode itu otomatis dilewati (ditandai "Sudah Ada").</p>
        ),
      },
      {
        q: 'Apa itu nominal custom pada konfigurasi KK?',
        a: (
          <p>Nominal default mengikuti jenis iuran (mis. Kebersihan Rp25.000). Nominal custom dipakai untuk pengecualian per KK — mis. keluarga tidak mampu dikenakan Rp10.000. Kosongkan berarti memakai default. Alasan pengecualian bisa dicatat untuk transparansi.</p>
        ),
      },
    ],
  },
  {
    id: 'kependudukan',
    title: 'Data Kependudukan',
    icon: <Users size={16} />,
    items: [
      {
        q: 'Apa itu hubungan keluarga (Kepala Keluarga, Suami, Istri, Anak, dll)?',
        a: (
          <p>Posisi seseorang dalam KK, mengikuti format resmi KK Dukcapil: <strong>Kepala Keluarga, Suami, Istri, Anak, Menantu, Cucu, Orang Tua, Mertua, Famili Lain,</strong> dan <strong>Lainnya</strong>. Satu KK hanya punya satu Kepala Keluarga; anggota lain mengikuti relasinya terhadap kepala.</p>
        ),
      },
      {
        q: 'Apa standar pengisian pendidikan terakhir?',
        a: (
          <p>Mengikuti pilihan standar SIAK: <strong>Tidak Sekolah · SD/sederajat · SMP/sederajat · SMA/sederajat · D1 · D2 · D3 · D4/S1 · S2 · S3</strong>. "Sederajat" berarti setara (mis. paket C setara SMA).</p>
        ),
      },
      {
        q: 'Bagaimana cara membaca NIK?',
        a: (
          <p>NIK 16 digit: <strong>3578 20</strong> (3578 = Kota Surabaya, 20 = Kecamatan Wonocolo — dipakai semua kelurahan di Wonocolo termasuk Bendul Merisi) + <strong>YYMMDD</strong> tanggal lahir — <em>tanggal +40 untuk perempuan</em> (mis. lahir 5 Maret tampil "05" pria / "45" wanita) + <strong>4 digit serial</strong> unik.</p>
        ),
      },
    ],
  },
  {
    id: 'roles',
    title: 'Hak Akses & Peran',
    icon: <ShieldCheck size={16} />,
    items: [
      {
        q: 'Apa saja peran pengguna dan batas datanya?',
        a: (
          <div className="space-y-1.5">
            <ul className="ml-4 list-disc space-y-1.5">
              <li><strong>Admin</strong> — akses penuh semua data &amp; pengaturan sistem (wilayah, pengguna, backup).</li>
              <li><strong>Lurah</strong> — melihat semua data kelurahan, tanpa akses pengaturan sistem.</li>
              <li><strong>RW</strong> — hanya data keluarga di RT-RT bawahannya.</li>
              <li><strong>RT</strong> — hanya data keluarga di RT-nya sendiri.</li>
            </ul>
            <p className="text-slate-500">Data di luar wilayah login otomatis tidak muncul di daftar maupun pencarian (detail akan 404) — warga tanpa KK pun hanya terlihat oleh admin &amp; lurah.</p>
          </div>
        ),
      },
      {
        q: 'Apakah warga biasa bisa login?',
        a: (
          <p>Tidak. Hanya petugas (admin/lurah/RW/RT) yang punya akun dengan username. Warga yang ingin mengecek data diri/keluarga/iurannya memakai <strong>Portal Publik</strong> tanpa login — cukup masukkan NIK atau nomor KK.</p>
        ),
      },
    ],
  },
  {
    id: 'sistem',
    title: 'Sistem & Keamanan',
    icon: <Landmark size={16} />,
    items: [
      {
        q: 'Apa fungsi Portal Publik?',
        a: (
          <p>Tiga layanan tanpa login di <code className="rounded bg-slate-100 px-1.5 py-0.5 text-[12px]">/portal</code>: <strong>Cek Warga</strong> (data diri by NIK/nama), <strong>Cek Keluarga</strong> (komposisi anggota KK by no. KK), dan <strong>Cek Iuran</strong> (ringkasan tagihan &amp; tunggakan by NIK kepala). Data sensitif dimasking (NIK sebagian tersembunyi) dan dibatasi 5 pencarian/menit.</p>
        ),
      },
      {
        q: 'Untuk apa backup dan seberapa sering sebaiknya dilakukan?',
        a: (
          <p>Backup menyimpan seluruh database (warga, KK, iuran, pengguna, pengaturan) menjadi satu file zip yang bisa diunduh. Disarankan <strong>minimal seminggu sekali</strong> atau sebelum aksi besar (generate massal, perubahan wilayah). Restore menimpa database — hanya lakukan dengan file backup yang dipercaya dan konfirmasi dua kali.</p>
        ),
      },
      {
        q: 'Apakah aktivitas petugas tercatat?',
        a: (
          <p>Ya. Setiap aksi penting (tambah/ubah/hapus data, pembayaran, generate, perubahan status) tercatat di <strong>Log Aktivitas</strong> — siapa melakukannya, kapan, dan apa yang berubah. Hanya admin &amp; lurah yang dapat melihatnya.</p>
        ),
      },
    ],
  },
]

export default function FaqPage() {
  const [active, setActive] = useState('keluarga')

  return (
    <div className="animate-fade-up">
      <PageHeader
        title="FAQ & Dokumentasi"
        subtitle="Definisi, konsep, dan cara kerja sistem SIWA Kelurahan Bendul Merisi"
      />

      <div className="grid grid-cols-1 gap-6 lg:grid-cols-[220px_1fr]">
        {/* Section nav */}
        <nav className="hidden lg:block">
          <Card className="sticky top-20 p-2">
            {SECTIONS.map((s) => (
              <a
                key={s.id}
                href={`#${s.id}`}
                onClick={() => setActive(s.id)}
                className={cn(
                  'flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-[13px] font-medium transition',
                  active === s.id ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50',
                )}
              >
                {s.icon}
                {s.title}
              </a>
            ))}
          </Card>
        </nav>

        {/* Content */}
        <div className="space-y-6">
          {SECTIONS.map((s) => (
            <section key={s.id} id={s.id} className="scroll-mt-20">
              <h2 className="mb-3 flex items-center gap-2 text-[15px] font-bold text-slate-900">
                <span className="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-50 text-brand-600">{s.icon}</span>
                {s.title}
              </h2>
              <Card className="divide-y divide-line overflow-hidden">
                {s.items.map((item, i) => (
                  <details key={i} className="group">
                    <summary className="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3.5 text-[13.5px] font-semibold text-slate-800 transition hover:bg-slate-50 [&::-webkit-details-marker]:hidden">
                      <span className="flex items-start gap-2.5">
                        <CircleHelp size={15} className="mt-0.5 shrink-0 text-brand-500" />
                        {item.q}
                      </span>
                      <span className="text-slate-300 transition-transform group-open:rotate-45 text-lg leading-none select-none">+</span>
                    </summary>
                    <div className="border-t border-line bg-slate-50/50 px-4 py-4 pl-11 text-[13px] leading-relaxed text-slate-600">
                      {item.a}
                    </div>
                  </details>
                ))}
              </Card>
            </section>
          ))}

          <Card className="flex items-start gap-3 bg-brand-50/60 p-4">
            <BookOpen size={16} className="mt-0.5 shrink-0 text-brand-600" />
            <p className="text-[13px] leading-relaxed text-slate-600">
              Halaman ini merangkum konsep SIWA (Sistem Informasi Warga). Untuk panduan langkah-demi-langkah operasional harian, hubungi admin kelurahan. Terakhir diperbarui: Agustus 2026.
            </p>
          </Card>
        </div>
      </div>
    </div>
  )
}
