'use client'

import { useEffect, useLayoutEffect, useRef, useState } from 'react'
import { usePathname, useRouter } from 'next/navigation'
import Link from 'next/link'
import { LogOut, Home, Settings, type LucideIcon } from 'lucide-react'
import { cn } from '@/lib/utils'
import { modulesForRole } from '@/lib/constants'
import { useAuth } from '@/stores/auth-store'

export function Sidebar() {
  const pathname = usePathname()
  const router = useRouter()
  const user = useAuth((s) => s.user)
  const logout = useAuth((s) => s.logout)
  const [menu, setMenu] = useState(false)
  const ref = useRef<HTMLDivElement>(null)

  // Items: Dashboard + modul yang boleh diakses role ini
  const items: { to: string; label: string; icon?: LucideIcon; match: string[] }[] = [
    { to: '/', label: 'Home', icon: Home, match: ['/'] },
    ...modulesForRole(user?.role).map((m) => ({
      to: m.href,
      label: m.label,
      icon: m.icon,
      match: m.match,
    })),
  ]

  // computed active index + sliding indicator geometry
  const activeIdx = items.findIndex((it) =>
    it.match.some(
      (m) =>
        (m === '/' && pathname === '/') ||
        pathname === m ||
        pathname.startsWith(m + '/'),
    ),
  )

  const rowRefs = useRef<(HTMLButtonElement | null)[]>([])
  const [ind, setInd] = useState<{ top: number; left: number; width: number; height: number } | null>(null)

  useLayoutEffect(() => {
    const el = rowRefs.current[activeIdx]
    setInd(el ? { top: el.offsetTop, left: el.offsetLeft, width: el.offsetWidth, height: el.offsetHeight } : null)
  }, [activeIdx])

  useEffect(() => {
    function onClick(e: MouseEvent) {
      if (ref.current && !ref.current.contains(e.target as Node)) setMenu(false)
    }
    document.addEventListener('mousedown', onClick)
    return () => document.removeEventListener('mousedown', onClick)
  }, [])

  return (
    <aside className="flex w-[104px] flex-shrink-0 flex-col items-center border-r border-line bg-white py-4">
      {/* logo */}
      <Link href="/" className="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-600 text-white shadow-sm">
        <span className="text-base font-extrabold">SW</span>
      </Link>

      <nav className="relative flex w-full flex-1 flex-col items-center gap-2.5 overflow-y-auto px-2.5">
        {/* sliding indicator — exact match design system */}
        {ind && (
          <span
            className="pointer-events-none absolute rounded-2xl bg-gradient-to-b from-brand-500 to-brand-600 shadow-[0_4px_12px_rgba(37,99,235,0.22)] transition-[top,left,width,height] duration-300 ease-[cubic-bezier(.22,1,.36,1)]"
            style={{ top: ind.top, left: ind.left, width: ind.width, height: ind.height }}
          />
        )}
        {items.map((item, i) => {
          const isActive = i === activeIdx
          return (
            <button
              key={item.label}
              ref={(el) => { rowRefs.current[i] = el }}
              onClick={() => router.push(item.to)}
              className={cn(
                'relative z-10 flex aspect-square w-[70px] flex-col items-center justify-center gap-1.5 rounded-2xl px-1 transition-colors',
                isActive ? 'text-white' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-700',
              )}
            >
              {item.label === 'Home' ? <Home size={23} /> : item.icon ? <item.icon size={23} /> : null}
              <span className="text-[11px] font-semibold leading-none">{item.label}</span>
            </button>
          )
        })}
      </nav>

      {/* account — avatar + dropdown */}
      <div className="relative mt-2 flex flex-col items-center" ref={ref}>
        <button
          onClick={() => setMenu((v) => !v)}
          className="overflow-hidden rounded-2xl ring-2 ring-white transition hover:ring-brand-100"
          title={user?.name || user?.username || 'User'}
        >
          {user?.avatar ? (
            // eslint-disable-next-line @next/next/no-img-element
            <img src={user.avatar} alt="" className="h-12 w-12 rounded-2xl object-cover" />
          ) : (
            <span className="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-600 text-sm font-bold text-white">
              {(user?.name || user?.username || '?').slice(0, 1).toUpperCase()}
            </span>
          )}
        </button>

        {menu && (
          <div className="absolute bottom-0 left-14 z-50 w-64 overflow-hidden rounded-2xl border border-line bg-white shadow-pop animate-bump-in">
            {/* profile header */}
            <div className="flex items-center gap-3 border-b border-line px-4 py-3">
              <span className="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-600 text-sm font-bold text-white">
                {(user?.name || '?').slice(0, 1).toUpperCase()}
              </span>
              <div className="min-w-0 flex-1">
                <div className="flex items-center gap-1.5">
                  <span className="truncate text-sm font-bold text-slate-900">{user?.name || 'User'}</span>
                  {user?.role && (
                    <span className="rounded-full bg-slate-100 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-slate-500">
                      {user.role}
                    </span>
                  )}
                </div>
                <div className="truncate text-[11px] text-slate-500">
                  @{user?.username}
                  {user?.wilayah ? ` · ${user.wilayah.nama}` : ''}
                </div>
              </div>
            </div>

            {/* actions */}
            <div className="py-1">
              {user?.role === 'admin' && (
                <MenuBtn icon={<Settings size={15} />} label="Pengaturan" onClick={() => { setMenu(false); router.push('/pengaturan') }} />
              )}
              <button
                onClick={() => { setMenu(false); logout(); router.push('/login') }}
                className="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-rose-600 hover:bg-rose-50"
              >
                <LogOut size={15} /> Keluar
              </button>
            </div>
          </div>
        )}
      </div>
    </aside>
  )
}

function MenuBtn({ icon, label, onClick }: { icon: React.ReactNode; label: string; onClick: () => void }) {
  return (
    <button onClick={onClick} className="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
      <span className="text-slate-400">{icon}</span> {label}
    </button>
  )
}
