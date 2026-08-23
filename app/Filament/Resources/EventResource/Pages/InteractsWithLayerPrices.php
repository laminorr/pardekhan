<?php

namespace App\Filament\Resources\EventResource\Pages;

/**
 * مدیریت دستی pivot لایه‌ها (event_layer) شامل تخفیف درصدی و قیمت ویژه.
 * فیلد فرم: layer_prices (آرایه‌ای از {layer_id, discount_percent, price_override})
 */
trait InteractsWithLayerPrices
{
    /** بافر موقت داده‌های لایه‌ها بین mutate و after */
    protected array $layerPricesBuffer = [];

    /** جدا کردن layer_prices از داده‌های قابل ذخیره روی خود رکورد */
    protected function extractLayerPrices(array $data): array
    {
        $this->layerPricesBuffer = $data['layer_prices'] ?? [];
        unset($data['layer_prices']);
        return $data;
    }

    /** sync کردن pivot لایه‌ها روی رکورد */
    protected function syncLayerPrices(): void
    {
        $sync = [];
        foreach ($this->layerPricesBuffer as $item) {
            if (empty($item['layer_id'])) {
                continue;
            }

            $discount = $item['discount_percent'] ?? null;
            $price    = $item['price_override'] ?? null;

            $sync[(int) $item['layer_id']] = [
                // خالی → null (استفاده از تخفیف پایهٔ لایه)
                'discount_percent' => ($discount === null || $discount === '') ? null : (int) $discount,
                // خالی → null (تخفیف)، «۰» → 0 (رایگان)، عدد → قیمت مطلق
                'price_override'   => ($price === null || $price === '') ? null : (int) $price,
            ];
        }

        $this->record->layers()->sync($sync);
    }

    /** پرکردن فرم از pivot موجود هنگام ویرایش */
    protected function fillLayerPrices(array $data): array
    {
        $data['layer_prices'] = $this->record->layers
            ->map(fn ($layer) => [
                'layer_id'         => $layer->id,
                'discount_percent' => $layer->pivot->discount_percent,
                'price_override'   => $layer->pivot->price_override,
            ])
            ->values()
            ->all();

        return $data;
    }
}
