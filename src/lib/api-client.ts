'use client'

/**
 * API client — fetch wrapper untuk Laravel Sanctum.
 * Cookie-based stateful auth (supports_credentials: true).
 */

const API_BASE = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api'

export class ApiError extends Error {
  status: number
  data: unknown
  constructor(message: string, status: number, data?: unknown) {
    super(message)
    this.name = 'ApiError'
    this.status = status
    this.data = data
  }
}

async function request<T>(
  path: string,
  options: RequestInit = {},
): Promise<T> {
  // Read XSRF-TOKEN cookie and set as header (fetch doesn't do this automatically like axios)
  const headers: Record<string, string> = {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    ...options.headers as Record<string, string>,
  }

  if (typeof document !== 'undefined') {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
    if (match) {
      headers['X-XSRF-TOKEN'] = decodeURIComponent(match[1])
    }
  }

  const res = await fetch(`${API_BASE}${path}`, {
    credentials: 'include', // Sanctum cookie
    headers,
    ...options,
  })

  // Handle 401 — caller can catch and redirect
  if (res.status === 401) {
    throw new ApiError('Unauthorized', 401)
  }

  // Handle 419 — CSRF token expired (Sanctum SPA)
  if (res.status === 419) {
    throw new ApiError('CSRF token mismatch', 419)
  }

  // Parse JSON safely (some endpoints return empty body)
  const text = await res.text()
  const data = text ? JSON.parse(text) : null

  if (!res.ok) {
    const message =
      (data && typeof data === 'object' && 'message' in data && String(data.message)) ||
      `Request failed (${res.status})`
    throw new ApiError(message, res.status, data)
  }

  return data as T
}

export const api = {
  get: <T>(path: string) => request<T>(path, { method: 'GET' }),
  post: <T>(path: string, body?: unknown) =>
    request<T>(path, { method: 'POST', body: body ? JSON.stringify(body) : undefined }),
  put: <T>(path: string, body?: unknown) =>
    request<T>(path, { method: 'PUT', body: body ? JSON.stringify(body) : undefined }),
  patch: <T>(path: string, body?: unknown) =>
    request<T>(path, { method: 'PATCH', body: body ? JSON.stringify(body) : undefined }),
  delete: <T>(path: string) => request<T>(path, { method: 'DELETE' }),
}

/**
 * Fetch Sanctum CSRF cookie (call before login).
 * Laravel Sanctum SPA pattern: GET /sanctum/csrf-cookie first.
 */
export async function getCsrfToken() {
  const sanctumBase = process.env.NEXT_PUBLIC_API_URL?.replace('/api', '') || 'http://localhost:8000'
  await fetch(`${sanctumBase}/sanctum/csrf-cookie`, {
    credentials: 'include',
  })
}
