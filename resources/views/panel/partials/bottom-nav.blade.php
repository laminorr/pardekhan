@php $active = $active ?? 'home'; @endphp
<nav class="bottom-nav" aria-label="ناوبری اصلی">
    <a href="{{ route('panel.dashboard') }}" class="nav-i {{ $active === 'home' ? 'on' : '' }}">
        <span class="nav-ico"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 4l9 6.5"/><path d="M5 9.5V20h14V9.5"/></svg></span>
        <span class="nav-lbl">خانه</span>
    </a>
    <a href="{{ route('panel.tickets.index') }}" class="nav-i {{ $active === 'tickets' ? 'on' : '' }}">
        <span class="nav-ico"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4z"/></svg></span>
        <span class="nav-lbl">بلیت‌ها</span>
    </a>
    <a href="{{ route('panel.events.index') }}" class="nav-i nav-center {{ $active === 'events' ? 'on' : '' }}">
        <span class="nav-fab"><svg width="27" height="27" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="2.6"/><circle cx="16" cy="9" r="2.2"/><path d="M4 19c0-2.8 2.2-4.5 5-4.5s5 1.7 5 4.5"/><path d="M14.5 15c2.3.2 4 1.8 4 4"/></svg></span>
        <span class="nav-lbl">دورهمی</span>
    </a>
    <a href="{{ route('panel.podcast') }}" class="nav-i {{ $active === 'podcast' ? 'on' : '' }}">
        <span class="nav-ico"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="3" width="6" height="11" rx="3"/><path d="M6 11a6 6 0 0 0 12 0"/><path d="M12 17v3"/><path d="M9 20h6"/></svg></span>
        <span class="nav-lbl">پادکست</span>
    </a>
    <a href="{{ route('panel.profile') }}" class="nav-i {{ $active === 'profile' ? 'on' : '' }}">
        <span class="nav-ico"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.4"/><path d="M5 20c0-3.6 3-5.5 7-5.5s7 1.9 7 5.5"/></svg></span>
        <span class="nav-lbl">پروفایل</span>
    </a>
</nav>
