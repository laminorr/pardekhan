<?php

namespace App\Services\ActivitySimulation;

/**
 * موتورِ متریکِ «کاربران آنلاین». فقط با نامِ متریک از پایه فرق دارد؛
 * override‌های config (نوسانِ بیشتر) در config/activity_simulation.php هستند.
 */
final class OnlineActivityEngine extends AbstractActivityEngine
{
    protected function metric(): string
    {
        return 'online';
    }
}
