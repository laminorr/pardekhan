<div>
    @if($getRecord()->avatar)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($getRecord()->avatar) }}"
             style="max-width:200px;border-radius:12px;border:2px solid #2a2a2a;">
    @endif
</div>
