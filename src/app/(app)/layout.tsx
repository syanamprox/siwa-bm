'use client'

import { useEffect } from 'react'
import { useRouter } from 'next/navigation'
import { useAuth } from '@/stores/auth-store'
import { AppShell } from '@/components/layout/AppShell'
import { Spinner } from '@/components/ui/primitives'

export default function AppLayout({ children }: { children: React.ReactNode }) {
  const router = useRouter()
  const user = useAuth((s) => s.user)
  const initialized = useAuth((s) => s.initialized)
  const fetchMe = useAuth((s) => s.fetchMe)

  // Check auth on mount
  useEffect(() => {
    if (!initialized) fetchMe()
  }, [initialized, fetchMe])

  // Redirect to login if not authenticated
  useEffect(() => {
    if (initialized && !user) {
      router.replace('/login')
    }
  }, [initialized, user, router])

  if (!initialized || !user) {
    return (
      <div className="flex h-screen items-center justify-center bg-canvas">
        <Spinner className="h-8 w-8" />
      </div>
    )
  }

  return <AppShell>{children}</AppShell>
}
