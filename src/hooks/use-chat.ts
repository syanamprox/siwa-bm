'use client'

import { useState, useRef, useCallback, useEffect } from 'react'
import type { Activity } from '@/components/chat/ActivityTimeline'

export interface ChatMessage {
  role: 'user' | 'assistant'
  content: string
  tools?: string[]
  error?: boolean
  streaming?: boolean
  /** Work steps derived from the stream (never persisted server-side). */
  activity?: Activity[]
  /** How long the turn took, in ms — set on `done`. */
  workedMs?: number
}

/* ── stream event → a human work step (Indonesian, no jargon) ── */
function toolActivity(name: string): Activity {
  if (name === 'data') return { kind: 'query', label: 'Mengambil data dari sistem' }
  if (name === 'write') return { kind: 'write', label: 'Menyiapkan perubahan data' }
  return { kind: 'query', label: `Menjalankan ${name}` }
}

/** append a step to the in-flight assistant message, skipping exact repeats */
function withActivity(msgs: ChatMessage[], step: Activity): ChatMessage[] {
  const next = [...msgs]
  const last = next[next.length - 1]
  if (last?.role !== 'assistant') return msgs
  const acts = last.activity ?? []
  if (acts.some((a) => a.label === step.label)) return msgs
  next[next.length - 1] = { ...last, activity: [...acts, step] }
  return next
}

export type ChatStatus = 'idle' | 'thinking' | 'querying' | 'writing'

export interface ChatSessionMeta {
  id: number
  title: string
  created_at: string
  updated_at: string
}

export const QUICK_SUGGESTIONS = [
  'Berapa omzet bulan lalu?',
  'Top 5 reseller dengan pembelian terbesar?',
  'Produk apa yang paling laris?',
  'Piutang yang sudah lewat jatuh tempo berapa?',
]

function getHeaders(json = false): Record<string, string> {
  const headers: Record<string, string> = { Accept: json ? 'application/json' : 'application/x-ndjson' }
  if (json) headers['Content-Type'] = 'application/json'
  const xsrf = typeof document !== 'undefined' ? document.cookie.match(/XSRF-TOKEN=([^;]+)/) : null
  if (xsrf) headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrf[1])
  return headers
}

const API_BASE = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api'

export function useChat() {
  const [messages, setMessages] = useState<ChatMessage[]>([])
  const [input, setInput] = useState('')
  const [status, setStatus] = useState<ChatStatus>('idle')
  const [chatSessionId, setChatSessionId] = useState<number | null>(null)
  const [canWrite, setCanWrite] = useState(false)
  const [historyLoaded, setHistoryLoaded] = useState(false)
  const [sessions, setSessions] = useState<ChatSessionMeta[]>([])
  const [turnStartedAt, setTurnStartedAt] = useState<number | undefined>(undefined)
  const scrollRef = useRef<HTMLDivElement>(null)
  const abortRef = useRef<AbortController | null>(null)
  const lastQueryRef = useRef<string>('')

  const loading = status !== 'idle'

  // ── Fetch session list ──
  const fetchSessions = useCallback(async () => {
    try {
      const res = await fetch(`${API_BASE}/intelligence/sessions`, {
        credentials: 'include',
        headers: getHeaders(true),
      })
      if (!res.ok) return
      setSessions(await res.json())
    } catch { /* ignore */ }
  }, [])

  // ── Load specific session by ID ──
  const loadSession = useCallback(async (id: number) => {
    try {
      const res = await fetch(`${API_BASE}/intelligence/sessions/${id}`, {
        credentials: 'include',
        headers: getHeaders(true),
      })
      if (!res.ok) return
      const data = await res.json()
      setChatSessionId(data.id)
      setCanWrite(Boolean(data.can_write)) // API bisa kirim 0/1 (tinyint) — paksa boolean biar {0 && …} tak render "0"
      setMessages(
        (data.messages ?? []).map((m: { role: string; content: string; tools?: string[] | null; is_error?: boolean }) => ({
          role: m.role,
          content: m.content,
          tools: m.tools ?? undefined,
          error: m.is_error ?? false,
        })),
      )
    } catch { /* ignore */ }
  }, [])

  // ── Delete session ──
  const deleteSession = useCallback(async (id: number) => {
    try {
      await fetch(`${API_BASE}/intelligence/sessions/${id}`, {
        method: 'DELETE',
        credentials: 'include',
        headers: getHeaders(true),
      })
      setSessions((prev) => prev.filter((s) => s.id !== id))
      if (id === chatSessionId) {
        setMessages([])
        setChatSessionId(null)
      }
    } catch { /* ignore */ }
  }, [chatSessionId])

  // ── Start fresh (new chat) on mount ──
  // User can browse past sessions via the Riwayat button.
  useEffect(() => {
    setHistoryLoaded(true)
  }, [])

  useEffect(() => {
    if (scrollRef.current) {
      scrollRef.current.scrollTop = scrollRef.current.scrollHeight
    }
  }, [messages, status])

  const send = useCallback(async (text: string) => {
    const q = text.trim()
    if (!q || status !== 'idle') return

    setInput('')
    setStatus('thinking')
    lastQueryRef.current = q

    const startedAt = Date.now()
    setTurnStartedAt(startedAt)

    setMessages((prev) => [
      ...prev,
      { role: 'user', content: q },
      {
        role: 'assistant',
        content: '',
        streaming: true,
        activity: [{ kind: 'think', label: 'Memahami pertanyaan' }],
      },
    ])

    // Client-side timeout: 120s total
    abortRef.current = new AbortController()
    const timeoutId = setTimeout(() => abortRef.current?.abort(), 120000)

    try {
      const res = await fetch(`${API_BASE}/intelligence/chat`, {
        method: 'POST',
        credentials: 'include',
        headers: { ...getHeaders(false), 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: q, chatSessionId }),
        signal: abortRef.current.signal,
      })

      if (!res.ok) {
        const friendly = res.status >= 500
          ? 'Server sedang sibuk. Coba lagi dalam sebentar ya.'
          : res.status === 401
            ? 'Sesi Anda berakhir. Silakan login ulang.'
            : `Terjadi kesalahan (${res.status}). Coba lagi ya.`
        throw new Error(friendly)
      }

      const reader = res.body?.getReader()
      const decoder = new TextDecoder()
      let buffer = ''

      if (reader) {
        while (true) {
          const { done, value } = await reader.read()
          if (done) break

          buffer += decoder.decode(value, { stream: true })
          const lines = buffer.split('\n')
          buffer = lines.pop() ?? ''

          for (const line of lines) {
            const trimmed = line.trim()
            if (!trimmed) continue

            try {
              const ev = JSON.parse(trimmed)

              if (ev.type === 'tool') {
                setStatus('querying')
                setMessages((prev) => {
                  const withStep = withActivity(prev, toolActivity(ev.name))
                  const next = [...withStep]
                  const last = next[next.length - 1]
                  if (last?.role === 'assistant') {
                    next[next.length - 1] = { ...last, tools: [...(last.tools ?? []), ev.name] }
                  }
                  return next
                })
              } else if (ev.type === 'text') {
                setStatus('writing')
                setMessages((prev) => {
                  const next = [...withActivity(prev, { kind: 'compose', label: 'Menyusun jawaban' })]
                  const last = next[next.length - 1]
                  if (last?.role === 'assistant') {
                    next[next.length - 1] = { ...last, content: last.content + ev.text }
                  }
                  return next
                })
              } else if (ev.type === 'done') {
                if (ev.chatSessionId) setChatSessionId(ev.chatSessionId)
                if (ev.canWrite) setCanWrite(true)
                setMessages((prev) => {
                  const next = [...prev]
                  const last = next[next.length - 1]
                  if (last?.role === 'assistant') {
                    const content = last.content || ev.text || ''
                    const activity = [...(last.activity ?? [])]
                    if (content.includes('```chart') && !activity.some((a) => a.kind === 'viz')) {
                      activity.push({ kind: 'viz', label: 'Membuat grafik' })
                    }
                    next[next.length - 1] = {
                      ...last,
                      streaming: false,
                      content,
                      activity,
                      workedMs: Date.now() - startedAt,
                      error: ev.isError === true ? true : last.error,
                    }
                  }
                  return next
                })
                // refresh session list
                fetchSessions()
              }
            } catch {
              // skip unparseable lines
            }
          }
        }
      }
    } catch (err) {
      let friendly = 'Terjadi kesalahan. Coba lagi ya.'

      if (err instanceof DOMException && err.name === 'AbortError') {
        friendly = 'Waktu respons habis — server terlalu lama memproses. Coba pertanyaan yang lebih singkat.'
      } else if (err instanceof TypeError) {
        friendly = 'Koneksi terputus. Periksa internet Anda lalu coba lagi.'
      } else if (err instanceof Error && err.message) {
        friendly = err.message
      }

      setMessages((prev) => {
        const next = [...prev]
        const last = next[next.length - 1]
        if (last?.role === 'assistant') {
          next[next.length - 1] = {
            ...last,
            content: friendly,
            streaming: false,
            workedMs: Date.now() - startedAt,
            error: true,
          }
        }
        return next
      })
    } finally {
      clearTimeout(timeoutId)
      abortRef.current = null
      setStatus('idle')
    }
  }, [status, chatSessionId, fetchSessions])

  // ── Retry last message (remove error bubble, resend) ──
  const retry = useCallback(async () => {
    if (!lastQueryRef.current || status !== 'idle') return
    // Remove the last error assistant message
    setMessages((prev) => {
      const last = prev[prev.length - 1]
      if (last?.role === 'assistant' && last.error) {
        return prev.slice(0, -1)
      }
      return prev
    })
    await send(lastQueryRef.current)
  }, [status, send])

  // ── Stop / cancel ongoing request ──
  const stop = useCallback(() => {
    abortRef.current?.abort()
  }, [])

  const reset = useCallback(() => {
    abortRef.current?.abort()
    setMessages([])
    setChatSessionId(null)
    setStatus('idle')
  }, [])

  const isEmpty = messages.length === 0

  return {
    messages, input, setInput, send, retry, stop, reset, status, loading, scrollRef,
    canWrite, isEmpty, historyLoaded, turnStartedAt,
    sessions, fetchSessions, loadSession, deleteSession, chatSessionId,
  }
}
