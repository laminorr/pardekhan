<?php

namespace App\Services\ActivitySimulation;

/**
 * PRNGِ قطعیِ محلی — یک شیء با حالتِ خودش (بدونِ mt_srand و بدونِ حالتِ سراسری).
 *
 * پیاده‌سازی: xorshift128 روی چهار واژهٔ ۳۲-بیتی. فقط از shift و XOR استفاده می‌شود
 * تا سرریزِ ضربِ ۶۴-بیتی در PHP رخ ندهد؛ بنابراین جریانِ خروجی روی هر پلتفرمی یکسان
 * است. یک seed یکسان ⇒ جریانِ یکسان؛ seedِ متفاوت ⇒ جریانِ کاملاً متفاوت.
 */
final class DeterministicRng
{
    private const MASK = 0xFFFFFFFF;

    private int $x0;
    private int $x1;
    private int $x2;
    private int $x3;

    private ?float $spareGaussian = null;

    public function __construct(int $s0, int $s1, int $s2, int $s3)
    {
        $this->x0 = $s0 & self::MASK;
        $this->x1 = $s1 & self::MASK;
        $this->x2 = $s2 & self::MASK;
        $this->x3 = $s3 & self::MASK;

        // xorshift نمی‌تواند از حالتِ تمام‌صفر شروع کند.
        if (($this->x0 | $this->x1 | $this->x2 | $this->x3) === 0) {
            $this->x0 = 0x9E3779B9;
            $this->x3 = 0x243F6A88;
        }

        // چند گامِ گرم‌کردن تا بیت‌های seed درهم شوند.
        for ($i = 0; $i < 16; $i++) {
            $this->nextUint32();
        }
    }

    /**
     * ساخت از خروجیِ hexِ یک HMAC (حداقل ۳۲ رقمِ hex ⇒ چهار واژهٔ ۳۲-بیتی).
     */
    public static function fromHexDigest(string $hexDigest): self
    {
        return new self(
            (int) hexdec(substr($hexDigest, 0, 8)),
            (int) hexdec(substr($hexDigest, 8, 8)),
            (int) hexdec(substr($hexDigest, 16, 8)),
            (int) hexdec(substr($hexDigest, 24, 8)),
        );
    }

    /** عددِ صحیحِ بدون‌علامتِ ۳۲-بیتی. */
    public function nextUint32(): int
    {
        $t = $this->x3;
        $s = $this->x0;

        $this->x3 = $this->x2;
        $this->x2 = $this->x1;
        $this->x1 = $s;

        $t ^= ($t << 11) & self::MASK;
        $t ^= ($t >> 8);
        $this->x0 = ($t ^ $s ^ ($s >> 19)) & self::MASK;

        return $this->x0;
    }

    /** عددِ اعشاری در بازهٔ [0, 1). */
    public function nextFloat(): float
    {
        return $this->nextUint32() / 4294967296.0;
    }

    /** نمونهٔ نرمالِ استاندارد (Box–Muller) با ذخیرهٔ نمونهٔ جفت. */
    public function gaussian(): float
    {
        if ($this->spareGaussian !== null) {
            $g = $this->spareGaussian;
            $this->spareGaussian = null;

            return $g;
        }

        $u1 = $this->nextFloat();
        if ($u1 < 1e-12) {
            $u1 = 1e-12; // از log(0) جلوگیری کن
        }
        $u2 = $this->nextFloat();

        $radius = sqrt(-2.0 * log($u1));
        $angle  = 2.0 * M_PI * $u2;

        $this->spareGaussian = $radius * sin($angle);

        return $radius * cos($angle);
    }
}
