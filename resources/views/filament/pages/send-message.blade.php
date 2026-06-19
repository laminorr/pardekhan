<x-filament-panels::page>
    <form wire:submit="send">
        {{ $this->form }}
        <div style="margin-top:1.5rem;">
            <x-filament::button type="submit" icon="heroicon-o-paper-airplane">
                ارسال پیام
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
