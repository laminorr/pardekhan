<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        <div style="margin-top:1.5rem;display:flex;gap:0.75rem;">
            <x-filament::button type="submit" icon="heroicon-o-check">
                ذخیره تنظیمات
            </x-filament::button>
        </div>
    </form>

    <div style="margin-top:2rem;padding:1.25rem;background:rgba(212,175,106,0.06);border:1px solid rgba(212,175,106,0.2);border-radius:12px;">
        <div style="font-weight:bold;margin-bottom:0.5rem;">راهنما</div>
        <div style="font-size:0.85rem;line-height:1.9;opacity:0.85;">
            ۱. در پنل مدیرپیامک، یک پترن برای کد تایید بسازید با متن مثل: «کد تایید پرده‌خوان: %code%»<br>
            ۲. کد آن پترن را در فیلد «پترن کد تایید» وارد کنید<br>
            ۳. API Key را از بخش وب‌سرویس پنل کپی کنید<br>
            ۴. تا وقتی «ارسال پیامک» را روشن نکرده‌اید، کدها فقط در لاگ ثبت می‌شوند
        </div>
    </div>
</x-filament-panels::page>
