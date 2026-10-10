@extends('panel.layouts.auth')
@section('title', 'تکمیل اطلاعات')

@section('content')
<div class="auth-card">
    {{-- Progress --}}
    <div style="margin-bottom:1.5rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
            <span style="font-size:0.78rem;color:var(--ink-dim);">سوال {{ $currentStep }} از {{ $totalSteps }}</span>
            <span style="font-size:0.78rem;color:var(--pine);font-weight:700;">{{ round(($currentStep / $totalSteps) * 100) }}%</span>
        </div>
        <div style="background:var(--green-soft);border-radius:99px;height:6px;overflow:hidden;border:1px solid var(--border);">
            <div style="background:linear-gradient(90deg,var(--pine-deep),var(--pine));height:100%;width:{{ round(($currentStep / $totalSteps) * 100) }}%;border-radius:99px;transition:width 0.3s;"></div>
        </div>
    </div>

    <h2 style="font-size:1.15rem;line-height:1.7;margin-bottom:1.5rem;">
        {{ $question->question }}
        @if($question->is_required)<span style="color:var(--danger);">*</span>@endif
    </h2>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first('answer') }}</div>
    @endif

    <form method="POST" action="{{ route('panel.questionnaire') }}">
        @csrf
        <input type="hidden" name="current_step" value="{{ $currentStep }}">
        <div class="field">
            <textarea name="answer" rows="4"
                style="width:100%;background:var(--surface);border:1px solid var(--border);border-radius:13px;padding:0.85rem 1rem;color:var(--ink);font-size:1rem;font-family:inherit;resize:vertical;transition:border-color 0.2s;"
                placeholder="{{ $question->placeholder ?? 'پاسخ خود را بنویسید...' }}"
                {{ $question->is_required ? 'required' : '' }}>{{ old('answer', $previousAnswer) }}</textarea>
        </div>

        <div style="display:flex;gap:0.75rem;">
            @if($currentStep > 1)
                <a href="{{ route('panel.questionnaire', ['step' => $currentStep - 1]) }}" class="btn btn-ghost" style="flex:1;margin-top:0.5rem;">قبلی</a>
            @endif
            <button type="submit" class="btn btn-gold" style="flex:2;">
                {{ $currentStep === $totalSteps ? 'ارسال نهایی' : 'بعدی' }}
            </button>
        </div>
    </form>
</div>

<div style="display:flex;justify-content:center;gap:6px;margin-top:1.5rem;">
    @for($i = 1; $i <= $totalSteps; $i++)
        <div style="width:8px;height:8px;border-radius:50%;background:{{ $i === $currentStep ? 'var(--pine)' : ($i < $currentStep ? 'var(--pine-deep)' : '#d7ddd8') }};"></div>
    @endfor
</div>
@endsection
