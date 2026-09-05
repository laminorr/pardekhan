# موتورِ شبیه‌سازیِ فعالیت (Synthetic Activity Engine)

## هدف

نمایشِ یک آمارِ زنده و باورپذیر برای دو متریکِ مستقل روی سایت:

- **آنلاین** (`online`) — تعدادِ کاربرانِ به‌ظاهر آنلاین.
- **در حالِ دیدنِ فیلمِ هفته** (`watching`) — تعدادِ به‌ظاهر در حالِ تماشا.

این آمار یک نمایشِ محیطی (ambient) است تا سایت زنده به‌نظر برسد؛ **تحلیل نیست**.

> **این سیستم هیچ‌گاه نباید بدون تصمیم صریح Product Owner به داده واقعی Presence یا Analytics متصل شود.**

## بدونِ دادهٔ واقعی

موتور **فقط** از این‌ها استفاده می‌کند:

1. **مقادیرِ لنگرِ ادمین** — ۵ مقدار برای هر متریک + یک `tolerance`، که در صفحهٔ «آمار»
   (`ActivitySettings`) ذخیره می‌شوند و از طریقِ مدلِ `Setting` خوانده می‌شوند.
2. **`config/activity_simulation.php`** — همهٔ پارامترهای الگوریتم.
3. **یک seedِ قطعی** — از تاریخ + متریک + نسخهٔ config + secret.

موتور **هرگز** کاربران/سشن‌ها/آنالیتیکس/Presence/درخواست‌های واقعی را نمی‌خواند و
هیچ کوئریِ دیتابیسی داخلِ حلقهٔ ۱۴۴۰-دقیقه‌ای اجرا نمی‌کند (فقط لنگرها یک بار خوانده می‌شوند).

## فرمولِ کلی

برای هر متریک، مسیرِ کاملِ یک روز (`int[1440]`، یک مقدار در دقیقه) در **یک گذرِ ترتیبی**
ساخته می‌شود. هر لایه قطعی از seedِ روز است:

```
پایهٔ روزانه = baseline(minute) × (1 + drift)          ← منحنیِ هموارِ لنگرها، دریفتِ یک‌بار-در-روز
            + slowWave(minute)                          ← جمعِ سینوس‌های seedشده (پریودِ jitterشده)
            + arNoise_t                                 ← AR(1): n_t = φ·n_{t-1} + σ·gauss (همبسته)
            + regimeBias_t                              ← بایاسِ ماندگارِ گاه‌به‌گاه، بازگردنده
→ applyPauseHold                                        ← گاهی چند دقیقه خروجی صاف می‌ماند
→ applyMicroBurst                                       ← جهشِ کوچکِ نادر
→ clamp به [baseline − tol, baseline + tol]             ← کلمپِ سخت
→ rateLimit: |out_t − out_{t-1}| ≤ maxDelta             ← سقفِ تغییرِ دقیقه‌ای
→ round + floor(min_floor)                              ← کوانتشِ صحیح و کف
```

- **baseline**: درون‌یابیِ **smootherstep** (`f(t)=6t⁵−15t⁴+10t³`) بینِ لنگرها؛ قطعهٔ
  شب→نیمه‌شب از ۲۴:۰۰ عبور می‌کند تا منحنی پیوسته و متناوب بماند (بدونِ شکست/overshoot).
- **نویزِ همبسته**: نویز از نوعِ AR(1) است، **نه سفید**؛ یعنی آرام و طبیعی بالا/پایین می‌رود.
- **بدونِ الگوی ساعتی**: پریودهای موجِ آهسته اول‌عددند و هر روز jitter می‌خورند، پس هیچ
  الگوی تکراریِ قابل‌تشخیص در ساعت‌ها دیده نمی‌شود.

## لنگرها (Anchors)

نقاطِ کنترلِ زمانی در `config/activity_simulation.php → anchor_times` (به وقتِ `Asia/Tehran`):

| لنگر | ساعت | دقیقهٔ روز |
|------|------|-----------|
| midnight | 02:00 | 120 |
| morning | 09:00 | 540 |
| noon | 13:30 | 810 |
| evening | 17:30 | 1050 |
| night | 21:30 | 1290 |

مقدارِ هر لنگر از `Setting` خوانده می‌شود (کلیدها در `setting_keys`)؛ اگر ردیفی نباشد،
از `defaults` استفاده می‌شود.

## Seed و قطعیت (Determinism)

```
seed = HMAC-SHA256("{date}|{metric}|{configuration_version}", secret)
```

- `date` = `Y-m-d` به وقتِ `Asia/Tehran`؛ `metric` ∈ {`online`, `watching`}.
- `secret` از `ACTIVITY_SIMULATION_SECRET` یا در نبودِ آن `APP_KEY`.
- PRNG یک شیءِ محلی (`DeterministicRng`، xorshift128) با حالتِ خودش است — **بدونِ
  `mt_srand` و بدونِ حالتِ سراسری**. `nextFloat()` در [0,1) و `gaussian()` (Box–Muller).
- seedِ یکسان ⇒ جریانِ یکسان؛ روزِ متفاوت یا نسخهٔ config متفاوت ⇒ جریانِ کاملاً متفاوت.

یک استریمِ جداگانه به‌نامِ `boundary` برای مقدارِ مرزِ نیمه‌شب استفاده می‌شود تا انتهای
روزِ D و ابتدای روزِ D+1 به مقدارِ مشترکی میل کنند و **در مرزِ روز پرشی رخ ندهد**.

## کش (Cache)

- مسیرِ کاملِ هر متریک برای هر روز کش می‌شود؛ کلید شاملِ `date + metric + configuration_version`.
- **تنبل**: در اولین درخواستِ آن روز ساخته می‌شود.
- **قفل**: ساختِ هم‌زمان با `Cache::lock(...)->block(5)` محافظت می‌شود.
- **TTL** ≈ ۲۵ ساعت.
- **مقاوم**: هر خطای کش/قفل نادیده گرفته می‌شود و مسیر درجا محاسبه می‌شود (چون قطعی است،
  همچنان درست است). هرگز استثنایی به فراخواننده throw نمی‌شود.

## گاردِ نسبت (Ratio Guard)

تنها جایی که دو متریک با هم تعامل دارند `ActivitySimulationManager` است. اگر
`watching ≥ watching_max_fraction_of_online × online` باشد، `watching` به زیرِ آن کسر
کشیده می‌شود (اما هرگز زیرِ کفِ خودش). موتورها هیچ حالتِ مشترکی ندارند.

## چطور تنظیم کنیم (Tuning)

همهٔ knobها در `config/activity_simulation.php → engine`:

- `shared` مقدارِ پایه برای هر دو متریک است؛ `online` و `watching` آن را deep-merge/override
  می‌کنند (online نوسانِ بیشتر، watching آرام‌تر و پایین‌تر با `tolerance_scale`).
- دامنه‌ها به‌صورتِ نسبتی از `tolerance` بیان می‌شوند؛ با تغییرِ `tolerance` همهٔ لایه‌ها
  خود-به‌خود مقیاس می‌گیرند.
- knobهای مهم: `daily_drift`, `slow_wave.periods_minutes/amp_tolerance_ratio`,
  `noise.ar1_phi/sigma_tolerance_ratio`, `regime.*`, `pause.*`, `micro_burst.*`,
  `rate_limit.max_delta_tolerance_ratio`, `boundary.*`, `min_floor`, `ratio_guard`.

**هر تغییرِ عمدی در خروجی** باید با بالا بردنِ `configuration_version` همراه شود (تا کش
باطل شود و snapshotِ طلایی بازتولید گردد).

## چطور دیباگ/پیش‌نمایش کنیم

- **صفحهٔ ادمین**: در صفحهٔ «آمار» دکمهٔ **«پیش‌نمایش امروز»** مسیرِ امروزِ هر دو متریک را
  هر ۳۰ دقیقه به‌صورتِ جدولِ فقط‌خواندنی نشان می‌دهد (چیزی ذخیره نمی‌کند).
- **دستورِ QA**:

  ```bash
  php artisan pardekhan:activity-simulate-week [--start=YYYY-MM-DD]
  ```

  ۷ روزِ متوالی را برای هر دو متریک تولید و در `storage/app/activity-sim/`
  (`day-1.json … day-7.json` + `activity-week.csv` با ستون‌های `minute,online,watching`)
  برای رسمِ نمودار می‌نویسد. این فقط پیش‌نمایش/QA است، نه تولیدِ محصولی.

## چطور تست کنیم

```bash
php artisan test --filter=ActivitySimulation
```

- **واحد/یکپارچه**: هموارسازِ smootherstep، قطعیت، کلمپ در دامنه، سقفِ نرخ، بازگشتِ AR(1)،
  کف، گاردِ نسبت، نبودِ پرشِ نیمه‌شب، fallbackِ کش.
- **طلایی (snapshot)**: `tests/Feature/ActivitySimulationGoldenTest.php` خروجی را برای یک
  تاریخ/seed/configِ ثابت با `tests/Fixtures/activity_sim_golden.json` مقایسه می‌کند؛ اگر
  خروجی **ناخواسته** تغییر کند تست می‌شکند. برای تغییرِ **عمدی**:

  ```bash
  php tests/Fixtures/regenerate-golden.php   # سپس configuration_version را بالا ببر
  ```

## بازگردانی (Rollback)

این فاز کاملاً افزایشی است: **بدونِ migration، بدونِ تغییرِ اسکیمای دیتابیس، بدونِ تغییرِ
دادهٔ ذخیره‌شده، و بدونِ تغییر در پنلِ اعضا**. برای بازگردانی کافی است برنچ/کامیت‌های این
فاز revert شوند.
