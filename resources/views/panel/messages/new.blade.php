@extends('panel.layouts.app')
@section('title', 'پیام جدید')

@section('content')
<div class="page-head" style="display:flex;align-items:center;gap:0.75rem;">
    <a href="{{ route('panel.messages.index') }}" class="icon-btn" style="flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    <div class="page-title" style="font-size:1.2rem;">پیام به مدیریت</div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

<div class="card">
    <form method="POST" action="{{ route('panel.messages.store') }}">
        @csrf
        <div class="field">
            <label>موضوع</label>
            <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="موضوع پیام">
        </div>
        <div class="field">
            <label>متن پیام</label>
            <textarea name="body" rows="5" required placeholder="پیام خود را بنویسید..."
                style="width:100%;background:#0d0d0f;border:1px solid var(--border);border-radius:13px;padding:0.85rem 1rem;color:var(--text);font-family:inherit;resize:vertical;">{{ old('body') }}</textarea>
        </div>
        <button type="submit" class="btn btn-gold">ارسال به مدیریت</button>
    </form>
</div>
@endsection
