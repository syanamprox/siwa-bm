import { NextRequest, NextResponse } from 'next/server'

/**
 * Gerbang file dokumen terkunci (public/kk/* dan public/rumah/*).
 *
 * File fisik ikut repo (KK) atau hasil upload petugas (foto rumah), tapi BUKAN
 * URL publik bebas: request tanpa signature valid ditolak 404. Signature
 * dihasilkan backend Laravel (endpoint /api/keluarga/doc-token) memakai secret
 * bersama KK_LINK_SECRET:
 *
 *   s = HMAC-SHA256("/{path}|{e}", KK_LINK_SECRET)   e = epoch detik
 *
 * TTL 5 menit, scoped per path persis — token satu dokumen tak bisa dipakai
 * dokumen lain. Verifikasi di edge pakai Web Crypto (async).
 */

const TTL_TOLERANCE = 0 // token expired = ditolak, tanpa tenggang

function reject(): NextResponse {
  // 404 polos — jangan bocorkan bahwa file ini ada
  return new NextResponse('Not Found', { status: 404 })
}

async function hmacSha256hex(secret: string, message: string): Promise<string> {
  const enc = new TextEncoder()
  const key = await crypto.subtle.importKey(
    'raw',
    enc.encode(secret),
    { name: 'HMAC', hash: 'SHA-256' },
    false,
    ['sign'],
  )
  const sig = await crypto.subtle.sign('HMAC', key, enc.encode(message))
  return Array.from(new Uint8Array(sig))
    .map((b) => b.toString(16).padStart(2, '0'))
    .join('')
}

/** konstanta waktu supaya tidak ter-cache aggressively oleh browser saat ditolak */
export async function middleware(request: NextRequest) {
  const secret = process.env.KK_LINK_SECRET
  if (!secret) return reject()

  const { pathname, searchParams } = request.nextUrl

  // Normalisasi: pathname selalu berawalan "/", cocok dengan payload sign backend
  const e = Number(searchParams.get('e'))
  const s = searchParams.get('s')
  if (!e || !s) return reject()

  const now = Math.floor(Date.now() / 1000)
  if (e < now - TTL_TOLERANCE) return reject()

  const expected = await hmacSha256hex(secret, `${pathname}|${e}`)
  // compare manual constant-time-ish (panjang hex sama, timing nyaris uniform)
  if (expected.length !== s.length || expected !== s) return reject()

  return NextResponse.next()
}

export const config = {
  // Hanya jalur file dokumen terkunci — statis lain tetap bebas
  matcher: ['/kk/:path*', '/rumah/:path*'],
}
