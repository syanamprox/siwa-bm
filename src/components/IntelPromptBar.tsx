'use client'

import { useState } from 'react'
import { useRouter } from 'next/navigation'
import { ArrowUp, Sparkles } from 'lucide-react'
import { cn } from '@/lib/utils'
import { useAuth } from '@/stores/auth-store'

/** Role yang boleh akses Intelligence — sinkron dgn middleware BE role:admin,camat,lurah */
const INTEL_ROLES = ['admin', 'camat', 'lurah']

/**
 * Prompt bar Intelligence di dashboard — Enter/kirim → pindah ke workspace /intelligence?q=…
 * Tersembunyi utk role di luar kantor kelurahan (rw/rt).
 */
export function IntelPromptBar() {
  const router = useRouter()
  const user = useAuth((s) => s.user)
  const [value, setValue] = useState('')

  if (!user || !INTEL_ROLES.includes(user.role)) return null

  function openWorkspace(text?: string) {
    const q = (text ?? value).trim()
    router.push(q ? `/intelligence?q=${encodeURIComponent(q)}` : '/intelligence')
  }

  return (
    <div className="animate-fade-up">
      <div
        className="flex items-end gap-2 rounded-[20px] border border-slate-200 bg-white px-3.5 py-2.5 shadow-[0_2px_14px_rgba(16,24,40,.06)] transition focus-within:border-brand-300 focus-within:shadow-[0_4px_22px_rgba(16,24,40,.10)]"
        onClick={(e) => {
          // klik di mana pun selain tombol → fokus input
          const target = e.target as HTMLElement
          if (target.tagName !== 'TEXTAREA' && target.tagName !== 'BUTTON') {
            target.querySelector('textarea')?.focus()
          }
        }}
      >
        <Sparkles size={17} className="mb-2 shrink-0 text-brand-500" />
        <textarea
          value={value}
          onChange={(e) => setValue(e.target.value)}
          onKeyDown={(e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
              e.preventDefault()
              openWorkspace()
            }
          }}
          placeholder="Tanya data kelurahan… (warga, iuran, kas)"
          rows={1}
          className="block max-h-24 min-h-[24px] flex-1 resize-none bg-transparent py-1 text-[14px] leading-6 text-slate-800 placeholder:text-slate-400 focus:outline-none"
        />
        <button
          onClick={() => openWorkspace()}
          disabled={!value.trim()}
          title="Tanya Intelligence"
          className={cn(
            'flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-white transition',
            value.trim() ? 'bg-brand-600 hover:brightness-110' : 'cursor-not-allowed bg-slate-200 text-slate-400',
          )}
        >
          <ArrowUp size={16} strokeWidth={2.6} />
        </button>
      </div>
      <p className="mt-1.5 px-1 text-[10.5px] text-slate-400">
        Dijawab oleh Intelligence — asisten AI baca-saja di atas data SIWA.
      </p>
    </div>
  )
}
