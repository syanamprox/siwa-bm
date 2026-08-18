'use client'

import { usePathname } from 'next/navigation'
import { Sidebar } from './Sidebar'
import { ModuleNav } from './ModuleNav'
import { Topbar } from './Topbar'

export function AppShell({ children }: { children: React.ReactNode }) {
  const pathname = usePathname()
  const isLauncher = pathname === '/'

  return (
    <div className="flex h-screen overflow-hidden bg-canvas">
      <Sidebar />
      {!isLauncher && <ModuleNav />}
      <div className="flex flex-1 flex-col overflow-hidden">
        {!isLauncher && <Topbar />}
        <main className="flex-1 overflow-y-auto">
          <div className="mx-auto max-w-[1500px] px-6 py-6">
            {children}
          </div>
        </main>
      </div>
    </div>
  )
}
