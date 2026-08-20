'use client'

import { useState, useEffect } from 'react'
import { useRouter } from 'next/navigation'
import Link from 'next/link'
import Image from 'next/image'
import { Eye, EyeOff, Loader2, ArrowRight } from 'lucide-react'
import { toast } from 'sonner'
import { useAuth } from '@/stores/auth-store'

export default function LoginPage() {
  const router = useRouter()
  const login = useAuth((s) => s.login)
  const loading = useAuth((s) => s.loading)
  const [username, setUsername] = useState('')
  const [password, setPassword] = useState('')
  const [showPassword, setShowPassword] = useState(false)
  const [mounted, setMounted] = useState(false)

  useEffect(() => {
    setMounted(true)
  }, [])

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault()
    try {
      await login(username, password)
      toast.success('Login berhasil!')
      router.push('/')
    } catch {
      toast.error('Username atau password salah')
    }
  }

  return (
    <div className="flex min-h-screen bg-gradient-to-br from-brand-50 via-white to-slate-50">
      <style>{`
        @keyframes lux-fade-up { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        @keyframes lux-fade-in { from{opacity:0} to{opacity:1} }
        @keyframes lux-glow { 0%,100%{opacity:.4} 50%{opacity:.7} }
      `}</style>

      {/* ══════════════════ LEFT — Brand showcase ══════════════════ */}
      <div className="relative hidden w-1/2 overflow-hidden bg-gradient-to-br from-brand-600 via-brand-700 to-brand-900 lg:flex lg:flex-col lg:justify-between">
        {/* Decorative gradient orbs */}
        <div
          className="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full blur-3xl"
          style={{ background: 'radial-gradient(circle, rgba(147,197,253,0.35), transparent 70%)', animation: 'lux-glow 6s ease-in-out infinite' }}
        />
        <div
          className="pointer-events-none absolute -right-20 top-1/3 h-80 w-80 rounded-full blur-3xl"
          style={{ background: 'radial-gradient(circle, rgba(196,181,253,0.25), transparent 70%)', animation: 'lux-glow 8s ease-in-out infinite 2s' }}
        />
        <div
          className="pointer-events-none absolute bottom-0 left-1/4 h-72 w-72 rounded-full blur-3xl"
          style={{ background: 'radial-gradient(circle, rgba(134,239,172,0.15), transparent 70%)', animation: 'lux-glow 7s ease-in-out infinite 4s' }}
        />

        {/* Subtle grid overlay */}
        <div
          className="pointer-events-none absolute inset-0 opacity-[0.04]"
          style={{
            backgroundImage: `linear-gradient(rgba(255,255,255,0.6) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.6) 1px, transparent 1px)`,
            backgroundSize: '48px 48px',
          }}
        />

        {/* Top — Logo */}
        <div
          className="relative z-10 flex items-center gap-3 p-10"
          style={{ animation: mounted ? 'lux-fade-in .8s ease both' : 'none' }}
        >
          <div className="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15 backdrop-blur-sm ring-1 ring-white/30">
            <span className="text-lg font-extrabold text-white">SW</span>
          </div>
          <div className="flex flex-col leading-tight">
            <span className="text-lg font-bold tracking-tight text-white">SIWA</span>
            <span className="text-[10px] font-medium uppercase tracking-[0.18em] text-brand-200/70">
              Kelurahan Bendul Merisi
            </span>
          </div>
        </div>

        {/* Center — Tagline */}
        <div
          className="relative z-10 flex flex-col justify-center px-10 pb-20"
          style={{ animation: mounted ? 'lux-fade-up .9s ease .15s both' : 'none' }}
        >
          <div className="mb-6 flex items-center gap-2">
            <span className="h-px w-12 bg-gradient-to-r from-brand-300 to-transparent" />
            <span className="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-200">
              Kelurahan Digital
            </span>
          </div>

          <h1 className="max-w-md text-[42px] font-extrabold leading-[1.1] tracking-tight text-white">
            Data warga,
            <br />
            <span className="bg-gradient-to-r from-brand-200 via-white to-purple-200 bg-clip-text text-transparent">
              tertata rapi.
            </span>
          </h1>

          <p className="mt-5 max-w-md text-[15px] leading-relaxed text-brand-100/80">
            Sistem Informasi Warga — kependudukan, kartu keluarga, dan iuran
            dalam satu platform yang cepat dan aman.
          </p>

          {/* Feature pills */}
          <div className="mt-8 flex flex-wrap gap-2">
            {['Data Warga', 'Kartu Keluarga', 'Iuran', 'Laporan', 'Portal Publik'].map((f, i) => (
              <span
                key={f}
                className="rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-[12px] font-medium text-brand-50 backdrop-blur-sm"
                style={{ animation: mounted ? `lux-fade-up .5s ease ${0.4 + i * 0.08}s both` : 'none' }}
              >
                {f}
              </span>
            ))}
          </div>
        </div>

        {/* Bottom — Personalization watermark + Copyright */}
        <div
          className="relative z-10 flex flex-col gap-4 p-10"
          style={{ animation: mounted ? 'lux-fade-in 1s ease .6s both' : 'none' }}
        >
          <div className="flex items-center gap-3">
            <span className="text-[12px] font-bold uppercase tracking-[0.2em] text-white/80">
              Kec. Wonocolo
            </span>
            <span className="text-[10px] font-medium uppercase tracking-[0.2em] text-white/50">
              personalized
            </span>
            <div className="h-3 w-px bg-white/20" />
            <span className="text-[10px] font-medium uppercase tracking-[0.2em] text-white/50">by</span>
            <Image
              src="/images/logo-digital360.png"
              alt="Digital360"
              width={140}
              height={50}
              className="h-[18px] w-auto object-contain brightness-0 invert opacity-80"
              priority
            />
          </div>
          <p className="text-[12px] text-brand-200/60">© 2026 Kelurahan Bendul Merisi · Kecamatan Wonocolo, Kota Surabaya</p>
        </div>
      </div>

      {/* ══════════════════ RIGHT — Login form ══════════════════ */}
      <div className="flex w-full items-center justify-center px-6 lg:w-1/2">
        {/* Soft decorative blob behind form */}
        <div className="pointer-events-none absolute right-0 top-1/4 h-72 w-72 rounded-full bg-brand-100/40 blur-3xl" />
        <div className="pointer-events-none absolute bottom-0 right-1/3 h-64 w-64 rounded-full bg-purple-50/50 blur-3xl" />

        <div className="relative w-full max-w-[380px]">
          {/* Mobile logo */}
          <div
            className="mb-10 flex flex-col items-center gap-3 lg:hidden"
            style={{ animation: mounted ? 'lux-fade-up .6s ease both' : 'none' }}
          >
            <div className="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 shadow-lg shadow-brand-600/30">
              <span className="text-xl font-extrabold text-white">SW</span>
            </div>
            <div className="text-center">
              <h1 className="text-xl font-extrabold tracking-tight text-slate-900">SIWA</h1>
              <p className="text-xs text-slate-400">Kelurahan Bendul Merisi</p>
            </div>
          </div>

          {/* Heading */}
          <div style={{ animation: mounted ? 'lux-fade-up .6s ease .1s both' : 'none' }}>
            <h2 className="text-[26px] font-bold tracking-tight text-slate-900">
              Selamat datang
            </h2>
            <p className="mt-1.5 text-[14px] text-slate-500">
              Masuk untuk mengakses dashboard Anda
            </p>
          </div>

          {/* Form */}
          <form
            onSubmit={handleSubmit}
            className="mt-8 space-y-5"
            style={{ animation: mounted ? 'lux-fade-up .6s ease .2s both' : 'none' }}
          >
            <div>
              <label className="mb-2 block text-[13px] font-medium text-slate-700">
                Username
              </label>
              <input
                type="text"
                placeholder="mis. admin, rt01"
                value={username}
                onChange={(e) => setUsername(e.target.value)}
                required
                autoFocus
                autoComplete="username"
                className="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-[14px] text-slate-900 shadow-sm placeholder:text-slate-400 transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/15"
              />
            </div>

            <div>
              <label className="mb-2 block text-[13px] font-medium text-slate-700">
                Password
              </label>
              <div className="relative">
                <input
                  type={showPassword ? 'text' : 'password'}
                  placeholder="••••••••"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  required
                  autoComplete="current-password"
                  className="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-12 text-[14px] text-slate-900 shadow-sm placeholder:text-slate-400 transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/15"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword((v) => !v)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-slate-600"
                  tabIndex={-1}
                >
                  {showPassword ? <EyeOff size={18} /> : <Eye size={18} />}
                </button>
              </div>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="group flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-3 text-[14px] font-semibold text-white shadow-lg shadow-brand-600/25 transition-all hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
              {loading ? (
                <Loader2 size={16} className="animate-spin" />
              ) : (
                <>
                  Masuk
                  <ArrowRight
                    size={16}
                    className="transition-transform group-hover:translate-x-0.5"
                  />
                </>
              )}
            </button>
          </form>

          {/* Footer note */}
          <p
            className="mt-8 text-center text-[12px] leading-relaxed text-slate-400"
            style={{ animation: mounted ? 'lux-fade-up .6s ease .3s both' : 'none' }}
          >
            Akun petugas dibuat oleh Admin Kelurahan.
            <br />
            Warga dapat mengecek iuran via{' '}
            <Link href="/portal" className="font-medium text-brand-600 hover:underline">
              Portal Publik
            </Link>
          </p>

          {/* Personalization stamp */}
          <div
            className="mt-6 flex flex-col items-center gap-3"
            style={{ animation: mounted ? 'lux-fade-in .6s ease .5s both' : 'none' }}
          >
            <div className="flex items-center gap-2 opacity-70">
              <span className="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-500">
                Kec. Wonocolo
              </span>
              <span className="text-[9px] font-semibold uppercase tracking-[0.15em] text-slate-400">×</span>
              <Image
                src="/images/logo-digital360.png"
                alt="Digital360"
                width={80}
                height={28}
                className="h-[12px] w-auto object-contain"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
