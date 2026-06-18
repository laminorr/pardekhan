@extends('panel.layouts.app')
@section('title', 'تکمیل اطلاعات - سوال ' . $currentStep)

@section('content')
<div class="panel-card">

    {{-- Progress bar --}}
    <div style="margin-bottom:1.5rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
            <span style="font-size:0.8rem;color:#888;">سوال {{ $currentStep }} از {{ $totalSteps }}</span>
            <span style="font-size:0.8rem;color:#f59e0b;">{{ round(($currentStep / $totalSteps) * 100) }}%</span>
        </div>
        <div style="background:#2a2a2a;border-radius:999px;height:4px;overflow:hidden;">
            <div style="background:#f59e0b;height:100%;width:{{ round(($currentStep / $totalSteps) * 100) }}%;border-radius:999px;transition:width 0.3s;"></div>
        </div>
    </div>

    {{-- سوال --}}
    <h2 style="font-size:1.1rem;color:#fff;margin-bottom:1.5rem;line-height:1.7;">
        {{ $question->question }}
        @if($question->is_required)
            <span style="color:#ef4444;margin-right:2px;">*</span>
        @endif
    </h2>

    @if ($errors->any())
        <div style="color:#ef4444;background:#1f0000;border:1px solid #7f1d1d;border-radius:8px;padding:0.75rem 1rem;margin-bottom:1rem;font-size:0.85rem;">
            {{ $errors->first('answer') }}
        </div>
    @endif

    <form method="POST" action="{{ route('panel.questionnaire') }}">
        @csrf
        <input type="hidden" name="current_step" value="{{ $currentStep }}">

        <div class="form-group">
            <textarea
                name="answer"
                rows="4"
                style="width:100%;background:#111;border:1px solid #333;border-radius:10px;padding:0.75rem 1rem;color:#fff;font-size:1rem;font-family:inherit;resize:vertical;"
                placeholder="{{ $question->placeholder ?? 'پاسخ خود را بنویسید...' }}"
                {{ $question->is_required ? 'required' : '' }}
            >{{ old('answer', $previousAnswer) }}</textarea>
        </div>

        <div style="display:flex;gap:0.75rem;margin-top:0.5rem;">
            @if($currentStep > 1)
                <a href="{{ route('panel.questionnaire', ['step' => $currentStep - 1]) }}"
                   style="flex:1;display:block;text-align:center;padding:0.85rem;background:#2a2a2a;color:#ddd;border-radius:10px;text-decoration:none;">
                    قبلی
                </a>
            @endif

            <button type="submit" class="btn btn-primary" style="flex:2;">
                {{ $currentStep === $totalSteps ? 'ارسال و منتظر بررسی بمانید ✓' : 'بعدی ←' }}
            </button>
        </div>
    </form>
</div>

{{-- نشانگر مراحل --}}
<div style="display:flex;justify-content:center;gap:6px;margin-top:1.5rem;">
    @for($i = 1; $i <= $totalSteps; $i++)
        <div style="width:8px;height:8px;border-radius:50%;background:{{ $i === $currentStep ? '#f59e0b' : ($i < $currentStep ? '#78350f' : '#2a2a2a') }};"></div>
    @endfor
</div>
@endsection
