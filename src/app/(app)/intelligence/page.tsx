'use client'

import { useEffect, useRef, useState } from 'react'
import { useRouter } from 'next/navigation'
import {
  ArrowLeft, ArrowUp, Loader2, MessageSquare, PanelLeft,
  Plus, RotateCcw, Shield, Sparkles, Trash2,
} from 'lucide-react'
import { cn } from '@/lib/utils'
import { useChat, type ChatSessionMeta } from '@/hooks/use-chat'
import { Markdown } from '@/components/chat/Markdown'
import { ActivityTimeline } from '@/components/chat/ActivityTimeline'
import { useAuth } from '@/stores/auth-store'

/* ── Suggestions (short label → full question) ── */
const SUGGESTIONS = [
  { label: 'Jumlah warga', fill: 'Berapa jumlah warga saat ini?' },
  { label: 'Komposisi umur', fill: 'Bagaimana komposisi umur warga?' },
  { label: 'Tunggakan iuran', fill: 'Berapa total tunggakan iuran?' },
  { label: 'Saldo kas', fill: 'Berapa saldo kas masing-masing unit?' },
  { label: 'Keluarga miskin', fill: 'Ada berapa keluarga miskin di tiap RT?' },
]

/** Akses Intelligence = kantor kelurahan saja (sinkron dgn middleware role BE) */
const INTEL_ROLES = ['admin', 'camat', 'lurah']

function greetingNow(): string {
  const h = new Date().getHours()
  if (h < 11) return 'Selamat pagi'
  if (h < 15) return 'Selamat siang'
  if (h < 18) return 'Selamat sore'
  return 'Selamat malam'
}

function timeAgo(dateStr: string): string {
  const d = new Date(dateStr)
  const diff = Math.floor((Date.now() - d.getTime()) / 1000)
  if (diff < 60) return 'baru saja'
  if (diff < 3600) return `${Math.floor(diff / 60)} menit lalu`
  if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`
  if (diff < 604800) return `${Math.floor(diff / 86400)} hari lalu`
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
}

interface SessionRow extends ChatSessionMeta {
  user?: { name?: string }
}

export default function IntelligencePage() {
  const router = useRouter()
  const user = useAuth((s) => s.user)
  const userRole = user?.role
  const allowed = userRole ? INTEL_ROLES.includes(userRole) : false
  const {
    messages, input, setInput, send, reset, status, loading, scrollRef,
    canWrite, isEmpty, turnStartedAt,
    sessions, fetchSessions, loadSession, deleteSession, chatSessionId,
  } = useChat()

  const inputRef = useRef<HTMLTextAreaElement>(null)
  const [sidebarOpen, setSidebarOpen] = useState(false)
  const firedRef = useRef(false)

  const activeStatus = loading && status !== 'idle' ? status : null
  const typedSessions = sessions as SessionRow[]
  // names the CSV a table exports to
  const activeTitle =
    typedSessions.find((s) => s.id === chatSessionId)?.title ||
    messages.find((m) => m.role === 'user')?.content ||
    'intelligence'

  // ── Gate client-side (source of truth: middleware BE role:admin,camat,lurah) ──
  useEffect(() => {
    if (user && !allowed) router.replace('/')
  }, [user, allowed, router])

  // ── Session list on mount ──
  useEffect(() => {
    if (allowed) fetchSessions()
  }, [allowed, fetchSessions])

  // ── Hand-off from the dashboard prompt bar: /intelligence?q=… fires once ──
  useEffect(() => {
    if (firedRef.current || !allowed) return
    const q = new URLSearchParams(window.location.search).get('q')
    if (!q) return
    firedRef.current = true
    window.history.replaceState({}, '', '/intelligence')
    send(q)
  }, [allowed, send])

  function onKeyDown(e: React.KeyboardEvent) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault()
      send(input)
    }
  }

  function newChat() {
    reset()
    setSidebarOpen(false)
    setTimeout(() => inputRef.current?.focus(), 50)
  }

  async function pickSession(id: number) {
    setSidebarOpen(false)
    await loadSession(id)
  }

  async function handleDelete(e: React.MouseEvent, id: number) {
    e.stopPropagation()
    await deleteSession(id)
  }

  return (
    <div className="fixed inset-0 z-40 flex bg-white">
      <style>{`
        @keyframes iw-fade-in { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
        @keyframes iw-bubble-in { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
        @keyframes iw-pill-in { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
        @keyframes iw-blink { 0%,100%{opacity:1} 50%{opacity:.15} }
        .iw-cursor::after{content:'';display:inline-block;width:2px;height:1em;margin-left:1px;background:#2563eb;vertical-align:text-bottom;animation:iw-blink 1s steps(1) infinite}
      `}</style>

      {/* ── Sidebar ── */}
      {sidebarOpen && (
        <div
          onClick={() => setSidebarOpen(false)}
          className="absolute inset-0 z-10 bg-slate-900/20 md:hidden"
        />
      )}
      <aside
        className={cn(
          'absolute inset-y-0 left-0 z-20 flex w-[260px] shrink-0 flex-col border-r border-slate-200 bg-slate-50/80 transition-transform md:relative md:translate-x-0',
          sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full',
        )}
      >
        {/* Brand */}
        <div className="flex items-center gap-2.5 px-4 pb-3 pt-4">
          <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-sm shadow-brand-500/25">
            <Sparkles size={18} />
          </span>
          <div className="min-w-0">
            <p className="truncate text-[13.5px] font-extrabold tracking-tight text-slate-900">Intelligence</p>
            <p className="truncate text-[10.5px] text-slate-400">Sistem Informasi Warga</p>
          </div>
        </div>

        {/* New chat */}
        <div className="px-3 pb-2">
          <button
            onClick={newChat}
            className="flex w-full items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-[13px] font-semibold text-slate-700 shadow-sm transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700"
          >
            <Plus size={15} /> Chat baru
          </button>
        </div>

        {/* Sessions */}
        <p className="px-4 pb-1.5 pt-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
          Riwayat
        </p>
        <div className="min-h-0 flex-1 space-y-0.5 overflow-y-auto px-2 pb-2 [scrollbar-width:thin]">
          {typedSessions.length === 0 ? (
            <div className="flex flex-col items-center justify-center px-4 py-10 text-center">
              <MessageSquare size={24} className="mb-2 text-slate-300" />
              <p className="text-[11.5px] leading-relaxed text-slate-400">
                Belum ada riwayat. Mulai dengan satu pertanyaan.
              </p>
            </div>
          ) : (
            typedSessions.map((s) => (
              <div
                key={s.id}
                onClick={() => pickSession(s.id)}
                className={cn(
                  'group flex w-full cursor-pointer items-center gap-2.5 rounded-xl border px-2.5 py-2 text-left transition',
                  s.id === chatSessionId
                    ? 'border-brand-200 bg-brand-50'
                    : 'border-transparent hover:bg-white hover:shadow-sm',
                )}
              >
                <div
                  className={cn(
                    'flex h-7 w-7 shrink-0 items-center justify-center rounded-lg',
                    s.id === chatSessionId ? 'bg-brand-100 text-brand-600' : 'bg-slate-200/70 text-slate-400',
                  )}
                >
                  <MessageSquare size={12} />
                </div>
                <div className="min-w-0 flex-1">
                  <p
                    className={cn(
                      'truncate text-[12.5px] font-medium',
                      s.id === chatSessionId ? 'text-brand-700' : 'text-slate-600',
                    )}
                  >
                    {s.title}
                  </p>
                  <div className="flex items-center gap-1.5 truncate text-[10px] text-slate-400">
                    {s.user?.name && s.user.name !== user?.name && (
                      <>
                        <span className="truncate font-medium text-slate-500">{s.user.name}</span>
                        <span>·</span>
                      </>
                    )}
                    <span>{timeAgo(s.updated_at || s.created_at)}</span>
                  </div>
                </div>
                <button
                  onClick={(e) => handleDelete(e, s.id)}
                  title="Hapus"
                  className="flex h-6 w-6 shrink-0 items-center justify-center rounded-md text-slate-300 opacity-0 transition hover:bg-rose-50 hover:text-rose-500 group-hover:opacity-100"
                >
                  <Trash2 size={12} />
                </button>
              </div>
            ))
          )}
        </div>

        {/* Back to dashboard */}
        <div className="border-t border-slate-200 p-2">
          <button
            onClick={() => router.push('/')}
            className="flex w-full items-center gap-2 rounded-xl px-3 py-2.5 text-[12.5px] font-semibold text-slate-500 transition hover:bg-white hover:text-slate-800"
          >
            <ArrowLeft size={15} /> Kembali ke Dashboard
          </button>
        </div>
      </aside>

      {/* ── Main ── */}
      <main className="flex min-w-0 flex-1 flex-col">
        {/* Topbar */}
        <header className="flex shrink-0 items-center justify-between gap-3 border-b border-slate-200 px-4 py-2.5">
          <div className="flex min-w-0 items-center gap-2">
            <button
              onClick={() => setSidebarOpen((v) => !v)}
              title="Riwayat"
              className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 md:hidden"
            >
              <PanelLeft size={16} />
            </button>
            <button
              onClick={() => router.push('/')}
              className="flex shrink-0 items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-[12.5px] font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-800"
            >
              <ArrowLeft size={15} /> Dashboard
            </button>
            <span className="hidden h-4 w-px bg-slate-200 sm:block" />
            <span className="hidden items-center gap-1.5 text-[11.5px] text-slate-400 sm:flex">
              <span
                className={cn('h-1.5 w-1.5 rounded-full', loading ? 'bg-amber-500' : 'bg-emerald-500')}
                style={loading ? { animation: 'iw-blink 1s infinite' } : undefined}
              />
              {loading
                ? activeStatus === 'querying'
                  ? 'lagi cari data…'
                  : activeStatus === 'thinking'
                    ? 'lagi mikir…'
                    : 'lagi nulis…'
                : 'online'}
            </span>
            {canWrite ? (
              <span className="hidden items-center gap-1 rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-semibold text-brand-600 sm:flex">
                <Shield size={10} /> Akses Penuh
              </span>
            ) : null}
          </div>

          {!isEmpty && !loading && (
            <button
              onClick={newChat}
              title="Mulai baru"
              className="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
            >
              <RotateCcw size={14} />
            </button>
          )}
        </header>

        {isEmpty ? (
          /* ── Entry state ── */
          <div className="flex min-h-0 flex-1 flex-col items-center justify-center overflow-y-auto px-5 py-8">
            <div className="w-full max-w-[680px]" style={{ animation: 'iw-fade-in .4s ease' }}>
              <div className="flex flex-col items-center">
                <span className="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-500/20">
                  <Sparkles size={26} />
                </span>
                <h1 className="text-center text-[28px] font-extrabold tracking-tight text-slate-900">
                  {greetingNow()}
                  {user?.name ? `, ${user.name.split(' ')[0]}` : ''}
                </h1>
                <p className="mt-1.5 text-center text-[14px] text-slate-500">
                  Tanya apa saja tentang data kependudukan, iuran, dan kas kelurahan.
                </p>
              </div>

              {/* Composer */}
              <div className="mt-6 flex w-full items-end gap-2 rounded-[24px] border border-slate-200 bg-white px-3.5 py-2.5 shadow-[0_2px_14px_rgba(16,24,40,.06)] transition focus-within:border-brand-300 focus-within:shadow-[0_4px_22px_rgba(16,24,40,.10)] focus-within:ring-4 focus-within:ring-brand-500/10">
                <textarea
                  ref={inputRef}
                  autoFocus
                  value={input}
                  onChange={(e) => {
                    setInput(e.target.value)
                    e.target.style.height = 'auto'
                    e.target.style.height = Math.min(e.target.scrollHeight, 160) + 'px'
                  }}
                  onKeyDown={onKeyDown}
                  placeholder="Tanya Intelligence…"
                  rows={1}
                  disabled={loading}
                  className="block max-h-40 min-h-[36px] flex-1 resize-none bg-transparent px-1 py-1.5 text-[15px] leading-6 text-slate-800 placeholder:text-slate-400 focus:outline-none disabled:opacity-50"
                />
                <button
                  onClick={() => send(input)}
                  disabled={!input.trim() || loading}
                  title="Kirim"
                  className={cn(
                    'flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-white transition',
                    input.trim() && !loading
                      ? 'bg-brand-600 hover:brightness-110'
                      : 'cursor-not-allowed bg-slate-200 text-slate-400',
                  )}
                >
                  {loading ? <Loader2 size={17} className="animate-spin" /> : <ArrowUp size={18} strokeWidth={2.6} />}
                </button>
              </div>

              {/* Suggestions */}
              <div className="mt-3 flex flex-wrap items-center justify-center gap-2">
                {SUGGESTIONS.map((s, i) => (
                  <button
                    key={s.label}
                    onClick={() => {
                      setInput(s.fill)
                      inputRef.current?.focus()
                    }}
                    className="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3.5 py-2 text-[13px] font-medium text-slate-500 shadow-sm transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700"
                    style={{ animation: `iw-pill-in .35s ease ${i * 70}ms both` }}
                  >
                    <Sparkles size={13} className="text-slate-400" />
                    {s.label}
                  </button>
                ))}
              </div>

              <p className="mx-auto mt-6 max-w-[420px] text-center text-[11px] leading-relaxed text-slate-400">
                Intelligence adalah asisten AI. Jawaban bisa kurang akurat. Selalu cek informasi penting
                sebelum mengambil keputusan.
              </p>
            </div>
          </div>
        ) : (
          /* ── Thread ── */
          <>
            <div
              ref={scrollRef}
              className="min-h-0 flex-1 overflow-y-auto bg-white px-5 pb-8 pt-6 sm:px-6 [scrollbar-width:thin]"
            >
              <div className="mx-auto w-full max-w-[720px]">
                {messages.map((msg, i) => (
                  <div
                    key={i}
                    className={cn(
                      'flex',
                      msg.role === 'user' ? 'justify-end' : 'justify-start',
                      // the air between turns: reply → next question breathes most
                      i > 0 && (msg.role === 'user' ? 'mt-10' : 'mt-8'),
                    )}
                    style={{ animation: 'iw-bubble-in .3s ease both' }}
                  >
                    {msg.role === 'user' ? (
                      <div className="max-w-[85%] rounded-2xl bg-[#E9F0FE] px-4 py-2.5 text-[14px] leading-relaxed text-slate-800">
                        {msg.content}
                      </div>
                    ) : (
                      <div
                        className={cn(
                          'min-w-0 max-w-full flex-1',
                          msg.error && 'rounded-xl bg-rose-50 px-4 py-3 ring-1 ring-rose-200',
                        )}
                      >
                        {msg.activity && msg.activity.length > 0 && (
                          <ActivityTimeline
                            items={msg.activity}
                            live={!!msg.streaming}
                            startedAt={turnStartedAt}
                            workedMs={msg.workedMs}
                          />
                        )}

                        {msg.content ? (
                          <Markdown text={msg.content} exportName={activeTitle} />
                        ) : !msg.activity?.length ? (
                          <span className="flex items-center gap-2 text-[13px] text-slate-500">
                            <Loader2 size={13} className="animate-spin text-brand-500" /> memproses…
                          </span>
                        ) : null}

                        {msg.streaming && msg.content && activeStatus === 'writing' && <span className="iw-cursor" />}
                      </div>
                    )}
                  </div>
                ))}
              </div>
            </div>

            {/* Composer pinned bottom */}
            <div className="shrink-0 border-t border-slate-200 bg-white px-4 py-3">
              <div className="mx-auto w-full max-w-[680px]">
                <div className="flex items-end gap-2 rounded-[24px] border border-slate-200 bg-white px-3.5 py-2.5 shadow-[0_2px_14px_rgba(16,24,40,.06)] transition focus-within:border-brand-300 focus-within:shadow-[0_4px_22px_rgba(16,24,40,.10)] focus-within:ring-4 focus-within:ring-brand-500/10">
                  <textarea
                    ref={inputRef}
                    value={input}
                    onChange={(e) => setInput(e.target.value)}
                    onKeyDown={onKeyDown}
                    placeholder="Tanya lanjutan…"
                    rows={1}
                    disabled={loading}
                    className="block max-h-28 min-h-[36px] flex-1 resize-none bg-transparent px-1 py-1.5 text-[14px] leading-6 text-slate-800 placeholder:text-slate-400 focus:outline-none disabled:opacity-50"
                  />
                  <button
                    onClick={() => send(input)}
                    disabled={!input.trim() || loading}
                    title="Kirim"
                    className={cn(
                      'flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white transition',
                      input.trim() && !loading
                        ? 'bg-brand-600 hover:brightness-110'
                        : 'cursor-not-allowed bg-slate-200 text-slate-400',
                    )}
                  >
                    {loading ? <Loader2 size={16} className="animate-spin" /> : <ArrowUp size={18} strokeWidth={2.6} />}
                  </button>
                </div>
                <p className="mt-2 text-center text-[10px] text-slate-400">
                  Intelligence bisa keliru. Cek informasi penting sebelum mengambil keputusan.
                </p>
              </div>
            </div>
          </>
        )}
      </main>
    </div>
  )
}
