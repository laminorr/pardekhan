<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private bool $enabled;
    private string $apiKey;
    private string $sender;

    public function __construct()
    {
        $this->enabled = Setting::get('sms_enabled') === '1';
        $this->apiKey  = Setting::get('sms_api_key', '');
        $this->sender  = Setting::get('sms_sender', '');
    }

    /**
     * ارسال پیامک پترن (برای OTP و پیام‌های قالب‌دار)
     */
    public function sendPattern(string $patternCode, string $recipient, array $params): bool
    {
        // اگه پیامک خاموشه یا تنظیم نشده، فقط لاگ کن
        if (! $this->enabled || empty($this->apiKey) || empty($patternCode)) {
            Log::info("SMS (disabled/unconfigured) → {$recipient} | pattern: {$patternCode} | params: " . json_encode($params, JSON_UNESCAPED_UNICODE));
            return false;
        }

        // اگه پکیج IPPanel نصب نیست، لاگ کن و خطا نده
        if (! class_exists(\Ippanel\Client::class)) {
            Log::warning("SMS package not installed. → {$recipient} | pattern: {$patternCode}");
            return false;
        }

        try {
            $client = new \Ippanel\Client($this->apiKey);
            $response = $client->sendPattern(
                $patternCode,
                $this->sender,
                $this->normalizePhone($recipient),
                $params
            );

            if ($response->isSuccessful()) {
                return true;
            }

            Log::error("SMS failed: " . $response->getMessage());
            return false;
        } catch (\Throwable $e) {
            Log::error("SMS exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ارسال پیامک متن آزاد (وب‌سرویس)
     */
    public function sendText(string $recipient, string $message): bool
    {
        if (! $this->enabled || empty($this->apiKey)) {
            Log::info("SMS text (disabled) → {$recipient}: {$message}");
            return false;
        }

        if (! class_exists(\Ippanel\Client::class)) {
            Log::warning("SMS package not installed.");
            return false;
        }

        try {
            $client = new \Ippanel\Client($this->apiKey);
            $response = $client->sendWebservice(
                $message,
                $this->sender,
                [$this->normalizePhone($recipient)]
            );
            return $response->isSuccessful();
        } catch (\Throwable $e) {
            Log::error("SMS exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * افزودن پیامک به صف (برای ارسال دسته‌ای پس‌زمینه)
     */
    public function queue(string $phone, string $patternCode, array $params, ?string $purpose = null): void
    {
        if (empty($patternCode)) {
            return; // اگه پترن تنظیم نشده، چیزی به صف اضافه نکن
        }

        \App\Models\SmsQueue::create([
            'phone'        => $phone,
            'pattern_code' => $patternCode,
            'params'       => $params,
            'purpose'      => $purpose,
            'status'       => 'pending',
        ]);
    }

    /**
     * پردازش یک دسته از صف (توسط Cron صدا زده می‌شود)
     */
    public function processBatch(int $limit = 50): int
    {
        $items = \App\Models\SmsQueue::where('status', 'pending')
            ->where('attempts', '<', 3)
            ->limit($limit)
            ->get();

        $sent = 0;
        foreach ($items as $item) {
            $ok = $this->sendPattern($item->pattern_code, $item->phone, $item->params);

            if ($ok) {
                $item->update(['status' => 'sent', 'sent_at' => now()]);
                $sent++;
            } else {
                $attempts = $item->attempts + 1;
                $item->update([
                    'attempts' => $attempts,
                    'status'   => $attempts >= 3 ? 'failed' : 'pending',
                ]);
            }
        }

        return $sent;
    }

    // تبدیل 09xxx به +989xxx
    private function normalizePhone(string $phone): string
    {
        $phone = trim($phone);
        if (str_starts_with($phone, '09')) {
            return '+98' . substr($phone, 1);
        }
        if (str_starts_with($phone, '9') && strlen($phone) === 10) {
            return '+98' . $phone;
        }
        return $phone;
    }
}
