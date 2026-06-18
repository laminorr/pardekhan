@extends('panel.layouts.app')
@section('title', 'نیاز به اطلاعات بیشتر')

@section('content')
<div class="panel-card">
    <div class="status-icon">💬</div>
    <div class="status-text">
        <strong>پیام از مدیریت پرده‌خوان</strong>
    </div>
    <div style="margin-top:1.5rem;">
        @foreach($member->messages()->where('type','interactive')->whereNull('member_reply')->get() as $msg)
        <div style="background:#111;border:1px solid #333;border-radius:10px;padding:1rem;margin-bottom:1rem;">
            <p style="color:#ddd;line-height:1.7;">{{ $msg->body }}</p>
            <form method="POST" action="/panel/messages/{{ $msg->id }}/reply" style="margin-top:1rem;">
                @csrf
                <div class="form-group">
                    <textarea name="reply" rows="3" style="width:100%;background:#1a1a1a;border:1px solid #333;border-radius:8px;padding:0.75rem;color:#fff;font-family:inherit;resize:vertical;" placeholder="پاسخ خود را بنویسید..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">ارسال پاسخ</button>
            </form>
        </div>
        @endforeach
    </div>
    <form method="POST" action="{{ route('panel.logout') }}" style="margin-top:1rem;">
        @csrf
        <button type="submit" class="btn btn-secondary">خروج</button>
    </form>
</div>
@endsection
