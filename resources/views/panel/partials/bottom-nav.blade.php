@php $active = $active ?? 'home'; @endphp
<div class="bottom-nav">
    <a href="{{ route('panel.dashboard') }}" class="nav-i {{ $active === 'home' ? 'on' : '' }}">
        <div class="nav-ico">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10l9-7 9 7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
        </div>خانه
    </a>
    <a href="{{ route('panel.events.index') }}" class="nav-i {{ $active === 'events' ? 'on' : '' }}">
        <div class="nav-ico">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 4v16M17 4v16M2 9h5M2 15h5M17 9h5M17 15h5"/></svg>
        </div>دورهمی‌ها
    </a>
    <a href="{{ route('panel.tickets.index') }}" class="nav-i {{ $active === 'tickets' ? 'on' : '' }}">
        <div class="nav-ico">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9a2 2 0 0 0 0 6v2a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2a2 2 0 0 1 0-6V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z"/></svg>
        </div>بلیت‌ها
    </a>
    <a href="{{ route('panel.profile') }}" class="nav-i {{ $active === 'profile' ? 'on' : '' }}">
        <div class="nav-ico">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
        </div>پروفایل
    </a>
</div>
