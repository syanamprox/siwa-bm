'use client'

import { create } from 'zustand'
import { api, getCsrfToken } from '@/lib/api-client'
import type { SiwaRole } from '@/lib/constants'

export interface AuthWilayah {
  wilayah_id: number
  nama: string
  tingkat: 'Kelurahan' | 'RW' | 'RT'
  rt_id: number | null
  rw_id: number | null
  rw_nama: string | null
  kelurahan_id: number | null
  kelurahan_nama: string | null
}

export interface AuthUser {
  id: number
  name: string
  username: string
  email: string | null
  role: SiwaRole
  avatar?: string | null
  wilayah: AuthWilayah | null
}

interface AuthState {
  user: AuthUser | null
  loading: boolean
  initialized: boolean
  login: (username: string, password: string) => Promise<void>
  logout: () => Promise<void>
  fetchMe: () => Promise<void>
}

export const useAuth = create<AuthState>((set) => ({
  user: null,
  loading: false,
  initialized: false,

  login: async (username, password) => {
    set({ loading: true })
    try {
      await getCsrfToken()
      await api.post('/login', { username, password })
      await useAuth.getState().fetchMe()
    } finally {
      set({ loading: false })
    }
  },

  logout: async () => {
    try {
      await api.post('/logout')
    } catch {
      // ignore — we clear local state anyway
    }
    set({ user: null, initialized: true })
  },

  fetchMe: async () => {
    try {
      const res = await api.get<{ data: AuthUser }>('/me')
      set({ user: res.data, initialized: true })
    } catch {
      set({ user: null, initialized: true })
    }
  },
}))
