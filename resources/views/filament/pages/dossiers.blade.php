<x-filament-panels::page>
<style>
    .dossier-wrap { max-width: 1000px; }

    .dossier-search {
        width: 100%;
        max-width: 340px;
        padding: 0.6rem 0.9rem;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.12);
        background: rgba(0,0,0,0.25);
        color: inherit;
        font-family: 'Vazirmatn', sans-serif;
        font-size: 0.9rem;
        outline: none;
    }
    .dossier-search:focus { border-color: #d4af6a; }

    .dossier-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.4rem;
    }
    .dossier-table th {
        text-align: right;
        font-size: 0.78rem;
        opacity: 0.6;
        font-weight: 700;
        padding: 0.3rem 0.8rem;
    }
    .dossier-row {
        background: rgba(255,255,255,0.03);
    }
    .dossier-row td {
        padding: 0.75rem 0.8rem;
        font-size: 0.88rem;
        vertical-align: middle;
        border-top: 1px solid rgba(255,255,255,0.06);
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    .dossier-row td:first-child { border-radius: 0 11px 11px 0; border-right: 1px solid rgba(255,255,255,0.06); }
    .dossier-row td:last-child  { border-radius: 11px 0 0 11px; border-left: 1px solid rgba(255,255,255,0.06); }

    .dossier-meta { font-size: 0.76rem; opacity: 0.55; }

    .btn-dossier {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 0.45rem 1rem;
        background: #d4af6a; color: #1a1408;
        border-radius: 9px; text-decoration: none;
        font-weight: 700; font-size: 0.8rem; white-space: nowrap;
    }
    .btn-dossier:hover { filter: brightness(1.05); }

    /* ── دکمهٔ غیرفعال «خلاصه شخصیت» با افکت گلیچ ── */
    .btn-persona {
        position: relative;
        display: inline-flex; flex-direction: column; align-items: center;
        gap: 2px;
    }
    .glitch {
        position: relative;
        display: inline-block;
        padding: 0.45rem 1rem;
        border-radius: 9px;
        border: 1px dashed rgba(212,175,106,0.35);
        background: rgba(255,255,255,0.02);
        color: rgba(212,175,106,0.75);
        font-weight: 700; font-size: 0.8rem; white-space: nowrap;
        cursor: not-allowed;
        pointer-events: none;
        user-select: none;
        opacity: 0.85;
    }
    .glitch::before,
    .glitch::after {
        content: attr(data-text);
        position: absolute;
        top: 0; right: 0;
        width: 100%; height: 100%;
        padding: 0.45rem 1rem;
        overflow: hidden;
        pointer-events: none;
    }
    .glitch::before {
        color: #7ee7ff;
        clip-path: inset(0 0 55% 0);
        animation: glitch-top 2.6s infinite steps(2, jump-none);
    }
    .glitch::after {
        color: #ff5c8a;
        clip-path: inset(55% 0 0 0);
        animation: glitch-bottom 3.1s infinite steps(2, jump-none);
    }
    @keyframes glitch-top {
        0%, 84%, 100% { transform: translateX(0); }
        86% { transform: translateX(2px); }
        88% { transform: translateX(-2px); }
        90% { transform: translateX(1px); }
    }
    @keyframes glitch-bottom {
        0%, 80%, 100% { transform: translateX(0); }
        82% { transform: translateX(-2px); }
        85% { transform: translateX(2px); }
        87% { transform: translateX(-1px); }
    }
    @media (prefers-reduced-motion: reduce) {
        .glitch::before, .glitch::after { animation: none; }
    }
    .persona-hint { font-size: 0.66rem; opacity: 0.5; letter-spacing: 0.5px; }

    .dossier-pager {
        display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        margin-top: 1.25rem;
    }
    .dossier-pager a, .dossier-pager span {
        padding: 0.4rem 0.9rem; border-radius: 9px;
        font-size: 0.82rem; text-decoration: none;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .dossier-pager a { color: #d4af6a; }
    .dossier-pager .disabled { opacity: 0.35; }
    .dossier-empty { opacity: 0.55; padding: 2rem; text-align: center; }
</style>

<div class="dossier-wrap" wire:key="dossiers-root">
    {{-- جست‌وجو --}}
    <div style="margin-bottom:1rem;display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
        <input
            type="text"
            class="dossier-search"
            placeholder="جست‌وجو بر اساس نام یا موبایل…"
            wire:model.live.debounce.400ms="search"
        />
        <span class="dossier-meta">{{ fa($this->members->total()) }} عضو</span>
    </div>

    @if($this->members->isEmpty())
        <div class="dossier-empty">عضوی یافت نشد.</div>
    @else
    <table class="dossier-table">
        <thead>
            <tr>
                <th>عضو</th>
                <th>لایه</th>
                <th>تاریخ عضویت</th>
                <th style="text-align:center;">پرونده</th>
                <th style="text-align:center;">خلاصه شخصیت</th>
            </tr>
        </thead>
        <tbody>
            @foreach($this->members as $member)
            <tr class="dossier-row" wire:key="dossier-{{ $member->id }}">
                <td>
                    <div style="font-weight:700;">{{ $member->first_name }} {{ $member->last_name }}</div>
                    <div class="dossier-meta">{{ fa($member->phone) }}</div>
                </td>
                <td>{{ $member->layer?->name ?? '—' }}</td>
                <td>{{ pdate($member->created_at, 'Y/m/d') }}</td>
                <td style="text-align:center;">
                    <a class="btn-dossier" href="{{ route('admin.dossiers.export', $member) }}">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                        پرونده
                    </a>
                </td>
                <td style="text-align:center;">
                    <div class="btn-persona">
                        <span class="glitch" data-text="خلاصه شخصیت" aria-disabled="true" title="به‌زودی">خلاصه شخصیت</span>
                        <span class="persona-hint">به‌زودی</span>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- صفحه‌بندی --}}
    @if($this->members->hasPages())
    <div class="dossier-pager">
        @if($this->members->onFirstPage())
            <span class="disabled">قبلی</span>
        @else
            <a href="#" wire:click.prevent="previousPage" wire:key="prev">قبلی</a>
        @endif

        <span class="disabled">
            صفحهٔ {{ fa($this->members->currentPage()) }} از {{ fa($this->members->lastPage()) }}
        </span>

        @if($this->members->hasMorePages())
            <a href="#" wire:click.prevent="nextPage" wire:key="next">بعدی</a>
        @else
            <span class="disabled">بعدی</span>
        @endif
    </div>
    @endif
    @endif
</div>
</x-filament-panels::page>
