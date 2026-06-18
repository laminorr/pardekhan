@extends('panel.layouts.app')
@section('title', 'تکمیل اطلاعات')

@section('content')
<div class="panel-card">
    <h2>تکمیل اطلاعات اولیه</h2>
    <p style="color:#aaa;font-size:0.85rem;margin-bottom:1.5rem;line-height:1.7;">
        لطفاً سوالات زیر را پاسخ دهید تا درخواست عضویت شما بررسی شود.
    </p>

    <form method="POST" action="{{ route('panel.questionnaire') }}">
        @csrf
        <div class="form-group">
            <label>چطور با پرده‌خوان آشنا شدید؟</label>
            <input type="text" name="answers[1]" value="{{ old('answers.1') }}" required placeholder="پاسخ خود را بنویسید...">
        </div>
        <div class="form-group">
            <label>چه نوع فیلم‌هایی بیشتر دوست دارید؟</label>
            <input type="text" name="answers[2]" value="{{ old('answers.2') }}" required placeholder="پاسخ خود را بنویسید...">
        </div>
        <div class="form-group">
            <label>آخرین فیلمی که دیدید و تحت تاثیرش قرار گرفتید چه بود؟</label>
            <input type="text" name="answers[3]" value="{{ old('answers.3') }}" required placeholder="پاسخ خود را بنویسید...">
        </div>
        <div class="form-group">
            <label>چه انتظاری از جلسات پرده‌خوان دارید؟</label>
            <input type="text" name="answers[4]" value="{{ old('answers.4') }}" required placeholder="پاسخ خود را بنویسید...">
        </div>
        <div class="form-group">
            <label>شغل یا حوزه فعالیت شما چیست؟</label>
            <input type="text" name="answers[5]" value="{{ old('answers.5') }}" required placeholder="پاسخ خود را بنویسید...">
        </div>
        <button type="submit" class="btn btn-primary">ارسال و منتظر بررسی بمانید</button>
    </form>
</div>
@endsection
