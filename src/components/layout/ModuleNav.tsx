'use client'

import Link from 'next/link'
import { usePathname, useRouter } from 'next/navigation'
import { ChevronLeft } from 'lucide-react'
import { MODULES, type ModuleNavItem } from '@/lib/constants'
import { useAuth } from '@/stores/auth-store'
import { cn } from '@/lib/utils'

export function ModuleNav() {
  const pathname = usePathname()
  const router = useRouter()
  const role = useAuth((s) => s.user?.role)

  // Find which module we're in
  const activeModule = MODULES.find((m) =>
    m.roles.includes(role ?? 'rt') &&
    m.match.some((matchPath) => pathname.startsWith(matchPath)),
  )

  if (!activeModule?.nav) return null

  return (
    <aside className="flex w-[220px] flex-shrink-0 flex-col border-r border-line bg-white">
      {/* brand row: module icon + back */}
      <div className="flex items-center gap-2 border-b border-line px-4 py-3.5">
        <span className="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
          <activeModule.icon size={16} />
        </span>
        <span className="text-sm font-bold text-slate-900">{activeModule.label}</span>
        <button
          onClick={() => router.push('/')}
          className="ml-auto rounded-lg p-1 text-slate-400 transition hover:bg-slate-50 hover:text-slate-700"
          title="Kembali ke Dashboard"
        >
          <ChevronLeft size={16} />
        </button>
      </div>

      {/* nav items */}
      <nav className="flex-1 overflow-y-auto px-2.5 py-3">
        {activeModule.nav!.map((item, i) => {
          if ('section' in item && item.section) {
            return (
              <div
                key={`sec-${i}`}
                className="mb-1 mt-3 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-300 first:mt-0"
              >
                {item.section}
              </div>
            )
          }

          const navItem = item as ModuleNavItem
          // role filter per item (kalau didefinisikan)
          if (navItem.roles && !navItem.roles.includes(role ?? 'rt')) return null

          const isActive = navItem.match.some((m) =>
            m === navItem.href
              ? pathname === m
              : pathname.startsWith(m + '/') || pathname === m,
          )

          return (
            <Link
              key={navItem.href}
              href={navItem.href}
              className={cn(
                'mb-0.5 flex items-center gap-2.5 rounded-xl px-3 py-2 text-[13px] font-medium transition-colors',
                isActive
                  ? 'bg-brand-50 text-brand-700'
                  : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
              )}
            >
              <navItem.icon size={16} className={isActive ? 'text-brand-600' : 'text-slate-400'} />
              {navItem.label}
            </Link>
          )
        })}
      </nav>
    </aside>
  )
}
