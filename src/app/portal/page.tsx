import Link from 'next/link'

export default function PortalPage() {
  return (
    <div className="flex min-h-screen flex-col items-center justify-center bg-canvas px-6">
      <div className="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-600 text-xl font-extrabold text-white">
        SW
      </div>
      <h1 className="text-2xl font-extrabold tracking-tight text-slate-900">
        Portal Publik SIWA
      </h1>
      <p className="mt-2 max-w-md text-center text-sm text-slate-500">
        Cek data iuran Anda tanpa login. Portal ini akan aktif di Phase 4 rework.
      </p>
      <Link
        href="/login"
        className="mt-6 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700"
      >
        Login Petugas
      </Link>
    </div>
  )
}
