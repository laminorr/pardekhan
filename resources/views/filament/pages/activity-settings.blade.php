<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        <div style="margin-top:1.5rem;display:flex;gap:0.75rem;">
            <x-filament::button type="submit" icon="heroicon-o-check">
                ذخیره تنظیمات
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
