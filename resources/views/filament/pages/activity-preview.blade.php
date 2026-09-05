{{-- پیش‌نمایشِ فقط‌خواندنیِ آمارِ امروز؛ نمونه‌برداریِ هر ۳۰ دقیقه. --}}
<div class="fi-ta-content overflow-x-auto">
    <p style="margin-bottom:0.75rem;color:var(--gray-500);font-size:0.85rem;">
        این مقادیر شبیه‌سازی‌شده‌اند و از لنگرهای ذخیره‌شده ساخته می‌شوند؛ چیزی ذخیره نمی‌شود.
    </p>
    <table class="w-full text-sm" style="border-collapse:collapse;">
        <thead>
            <tr style="text-align:right;border-bottom:1px solid var(--gray-200);">
                <th style="padding:0.4rem 0.75rem;">ساعت</th>
                <th style="padding:0.4rem 0.75rem;">آنلاین</th>
                <th style="padding:0.4rem 0.75rem;">در حال دیدن فیلم هفته</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr style="border-bottom:1px solid var(--gray-100);">
                    <td style="padding:0.35rem 0.75rem;font-variant-numeric:tabular-nums;">{{ $row['time'] }}</td>
                    <td style="padding:0.35rem 0.75rem;font-variant-numeric:tabular-nums;">{{ $row['online'] }}</td>
                    <td style="padding:0.35rem 0.75rem;font-variant-numeric:tabular-nums;">{{ $row['watching'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
