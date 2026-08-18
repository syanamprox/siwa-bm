'use client'

import * as React from 'react'
import { createPortal } from 'react-dom'
import { ChevronDown, Check, Search } from 'lucide-react'
import { cn } from '@/lib/utils'

/* ──────────────────────────────────────────────
 * Custom Select — button + portal popover, zero deps.
 * Drop-in replacement for native <Select>.
 * onChange receives `(value: string)` directly.
 * Auto-searchable when options > 8.
 * Popover renders in a portal (position: fixed) to
 * escape any overflow / stacking-context parent.
 * ────────────────────────────────────────────── */

export interface SelectOption {
  value: string
  label: React.ReactNode
  disabled?: boolean
}

interface SelectProps {
  value?: string
  onChange?: (value: string) => void
  placeholder?: string
  searchable?: boolean
  className?: string
  /** Button size — 'sm' = 28px (for tables/inline), 'md' = 40px (default) */
  size?: 'sm' | 'md'
  disabled?: boolean
  /** Pass options directly (alternative to <option> children) */
  options?: SelectOption[]
  children?: React.ReactNode
  /** @deprecated kept for API compat, ignored */
  required?: boolean
  name?: string
}

export const Select = React.forwardRef<HTMLButtonElement, SelectProps>(function Select(
  {
    value,
    onChange,
    placeholder = 'Pilih...',
    searchable: searchableProp,
    className,
    size = 'md',
    disabled,
    options: optionsProp,
    children,
    required: _required,
    name,
  },
  ref,
) {
  /* Parse options from prop or children */
  const options: SelectOption[] = React.useMemo(() => {
    if (optionsProp) return optionsProp
    return React.Children.toArray(children)
      .filter(React.isValidElement)
      .map((el) => {
        const p = (el as React.ReactElement<{ value?: string; children?: React.ReactNode; disabled?: boolean }>).props
        return { value: String(p.value ?? ''), label: p.children ?? '', disabled: p.disabled }
      })
  }, [optionsProp, children])

  const [open, setOpen] = React.useState(false)
  const [query, setQuery] = React.useState('')
  const [hi, setHi] = React.useState(0) // highlighted index
  const rootRef = React.useRef<HTMLDivElement>(null)
  const btnRef = React.useRef<HTMLButtonElement>(null)
  const popoverRef = React.useRef<HTMLDivElement>(null)
  const searchRef = React.useRef<HTMLInputElement>(null)
  const listRef = React.useRef<HTMLDivElement>(null)
  const instanceId = React.useId()

  // Popover position state
  const [popoverStyle, setPopoverStyle] = React.useState<React.CSSProperties>({})

  const selected = options.find((o) => o.value === value)
  const searchable = searchableProp ?? options.length > 8

  const filtered = React.useMemo(() => {
    if (!query) return options
    const q = query.toLowerCase()
    return options.filter((o) => String(o.label).toLowerCase().includes(q))
  }, [options, query])

  /* Compute popover position from trigger button rect */
  const computePosition = React.useCallback(() => {
    if (!btnRef.current) return
    const rect = btnRef.current.getBoundingClientRect()
    const spaceBelow = window.innerHeight - rect.bottom
    const spaceAbove = rect.top
    const POPOVER_MAX = 300 // approx max height incl search + list

    let top: number
    let maxHeight: number

    if (spaceBelow >= POPOVER_MAX || spaceBelow >= spaceAbove) {
      // Open downward
      top = rect.bottom + 4
      maxHeight = Math.min(spaceBelow - 8, 300)
    } else {
      // Open upward
      top = rect.top - 4
      maxHeight = Math.min(spaceAbove - 8, 300)
      // Will be transformed upward via bottom positioning
    }

    setPopoverStyle({
      position: 'fixed',
      top: spaceBelow >= POPOVER_MAX || spaceBelow >= spaceAbove ? top : undefined,
      bottom: spaceBelow >= POPOVER_MAX || spaceBelow >= spaceAbove ? undefined : window.innerHeight - top,
      left: rect.left,
      width: rect.width,
      maxHeight,
      zIndex: 9999,
    })
  }, [])

  /* Close other Select instances when this one opens */
  React.useEffect(() => {
    if (!open) return
    function onOtherOpen(e: Event) {
      if ((e as CustomEvent).detail !== instanceId) {
        setOpen(false)
        setQuery('')
      }
    }
    window.addEventListener('select:open', onOtherOpen)
    return () => window.removeEventListener('select:open', onOtherOpen)
  }, [open, instanceId])

  /* click outside — check root AND popover (popover is portaled, not inside root) */
  React.useEffect(() => {
    if (!open) return
    function onClick(e: MouseEvent) {
      const target = e.target as Node
      if (
        rootRef.current && !rootRef.current.contains(target) &&
        popoverRef.current && !popoverRef.current.contains(target)
      ) {
        setOpen(false)
        setQuery('')
      }
    }
    document.addEventListener('mousedown', onClick)
    return () => document.removeEventListener('mousedown', onClick)
  }, [open])

  /* reposition on scroll / resize */
  React.useEffect(() => {
    if (!open) return
    computePosition()
    function onScroll() { computePosition() }
    function onResize() { computePosition() }
    window.addEventListener('scroll', onScroll, true)
    window.addEventListener('resize', onResize)
    return () => {
      window.removeEventListener('scroll', onScroll, true)
      window.removeEventListener('resize', onResize)
    }
  }, [open, computePosition])

  /* focus search on open */
  React.useEffect(() => {
    if (open && searchable) setTimeout(() => searchRef.current?.focus(), 0)
    if (!open) setQuery('')
  }, [open, searchable])

  /* reset highlight */
  React.useEffect(() => setHi(0), [query])

  /* scroll highlighted item into view */
  React.useEffect(() => {
    if (!open || !listRef.current) return
    const el = listRef.current.children[hi] as HTMLElement | undefined
    el?.scrollIntoView({ block: 'nearest' })
  }, [hi, open])

  function pick(opt: SelectOption) {
    if (opt.disabled) return
    onChange?.(opt.value)
    setOpen(false)
  }

  function onKeyDown(e: React.KeyboardEvent) {
    if (!open) {
      if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
        e.preventDefault()
        computePosition()
        setOpen(true)
      }
      return
    }
    switch (e.key) {
      case 'ArrowDown':
        e.preventDefault()
        setHi((i) => Math.min(i + 1, filtered.length - 1))
        break
      case 'ArrowUp':
        e.preventDefault()
        setHi((i) => Math.max(i - 1, 0))
        break
      case 'Enter':
        e.preventDefault()
        if (filtered[hi]) pick(filtered[hi])
        break
      case 'Escape':
        e.preventDefault()
        setOpen(false)
        break
      case 'Tab':
        setOpen(false)
        break
    }
  }

  /* Merge forwarded ref with internal btnRef */
  const setBtnRef = (el: HTMLButtonElement | null) => {
    btnRef.current = el
    if (typeof ref === 'function') ref(el)
    else if (ref) (ref as React.MutableRefObject<HTMLButtonElement | null>).current = el
  }

  return (
    <div ref={rootRef} className={cn('relative', className)}>
      {/* hidden input for form compat */}
      {name && <input type="hidden" name={name} value={value ?? ''} />}

      {/* trigger */}
      <button
        ref={setBtnRef}
        type="button"
        disabled={disabled}
        onClick={() => {
          if (!open) {
            computePosition()
            // Tell all other Select instances to close
            window.dispatchEvent(new CustomEvent('select:open', { detail: instanceId }))
          }
          setOpen((o) => !o)
        }}
        onKeyDown={onKeyDown}
        className={cn(
          'flex w-full items-center justify-between gap-2 rounded-xl border border-line bg-white px-3 transition-shadow',
          size === 'sm' ? 'h-7 text-xs' : 'h-10 text-sm',
          'focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400',
          disabled && 'cursor-not-allowed opacity-50',
          !selected && 'text-slate-400',
        )}
      >
        <span className="truncate text-left">{selected ? selected.label : placeholder}</span>
        <ChevronDown size={16} className={cn('shrink-0 text-slate-400 transition-transform', open && 'rotate-180')} />
      </button>

      {/* popover — rendered via portal to escape overflow containers */}
      {open && typeof document !== 'undefined' && createPortal(
        <div
          ref={popoverRef}
          style={popoverStyle}
          className="overflow-hidden rounded-xl border border-line bg-white shadow-pop"
        >
          {searchable && (
            <div className="border-b border-line p-2">
              <div className="relative">
                <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400" />
                <input
                  ref={searchRef}
                  value={query}
                  onChange={(e) => setQuery(e.target.value)}
                  placeholder="Cari..."
                  className="h-8 w-full rounded-lg border border-line bg-slate-50 pl-8 pr-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30"
                  onKeyDown={onKeyDown}
                />
              </div>
            </div>
          )}

          <div ref={listRef} className="max-h-60 overflow-y-auto py-1">
            {filtered.length === 0 ? (
              <p className="px-3 py-4 text-center text-xs text-slate-400">Tidak ditemukan</p>
            ) : (
              filtered.map((opt, i) => (
                <button
                  key={opt.value + i}
                  type="button"
                  disabled={opt.disabled}
                  onClick={() => pick(opt)}
                  onMouseEnter={() => setHi(i)}
                  className={cn(
                    'flex w-full items-center justify-between px-3 py-2 text-left text-sm transition',
                    hi === i ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50',
                    opt.value === value && 'font-semibold',
                    opt.disabled && 'cursor-not-allowed opacity-40',
                  )}
                >
                  <span className="truncate">{opt.label}</span>
                  {opt.value === value && <Check size={14} className="shrink-0 text-brand-600" />}
                </button>
              ))
            )}
          </div>
        </div>,
        document.body,
      )}
    </div>
  )
})
