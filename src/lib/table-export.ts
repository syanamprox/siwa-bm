/** Pure table-export helpers — kept side-effect-free so they're trivially testable. */

/** CSV field: quote when it carries a comma, quote, or newline; double the quotes. */
function csvField(v: string): string {
  return /[",\n\r]/.test(v) ? '"' + v.replace(/"/g, '""') + '"' : v
}

export function rowsToCsv(rows: string[][]): string {
  return rows.map((r) => r.map(csvField).join(',')).join('\r\n')
}

/** TSV for clipboard — pastes straight into Excel/Sheets. Tabs/newlines inside cells become spaces. */
export function rowsToTsv(rows: string[][]): string {
  return rows.map((r) => r.map((c) => c.replace(/[\t\n\r]+/g, ' ')).join('\t')).join('\n')
}

/** a safe filename from a chat title: "Penjualan Q3 / Q4?" → "penjualan-q3-q4" */
export function fileSlug(name: string | undefined, fallback = 'table'): string {
  const s = (name || '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
    .slice(0, 60)
  return s || fallback
}

/** read the rendered rows back out of a table element (th + td, in DOM order) */
export function tableRows(table: HTMLTableElement): string[][] {
  return Array.from(table.querySelectorAll('tr')).map((tr) =>
    Array.from(tr.querySelectorAll('th,td')).map((c) => (c.textContent || '').trim()),
  )
}

/** client-side blob download */
export function downloadCsv(csv: string, filename: string): void {
  const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8' }) // BOM so Excel reads UTF-8
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = filename
  document.body.appendChild(a)
  a.click()
  document.body.removeChild(a)
  setTimeout(() => URL.revokeObjectURL(url), 1000)
}
