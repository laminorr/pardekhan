#!/bin/bash
# ═══════════════════════════════════════════════════════════════
#  pardekhan — بررسی هم‌گامی سرور با گیت‌هاب
#  قبل از هر کار با CC یا بعد از هر تغییر روی سرور، این رو اجرا کن:
#      bash sync-check.sh
# ═══════════════════════════════════════════════════════════════

cd "$(dirname "$0")" || exit 1

echo ""
echo "──────────────────────────────────────────"
echo "  بررسی وضعیت pardekhan"
echo "──────────────────────────────────────────"

# آخرین اطلاعات گیت‌هاب رو بگیر (بدون تغییر چیزی)
git fetch origin --quiet 2>/dev/null

BRANCH=$(git rev-parse --abbrev-ref HEAD)
echo "  شاخهٔ فعلی: $BRANCH"

# تعداد commitهای جلو/عقب نسبت به گیت‌هاب
AHEAD=$(git rev-list --count origin/main..HEAD 2>/dev/null)
BEHIND=$(git rev-list --count HEAD..origin/main 2>/dev/null)

# آیا تغییر ذخیره‌نشده هست؟
DIRTY=$(git status --porcelain)

echo "──────────────────────────────────────────"

PROBLEM=0

if [ "$BRANCH" != "main" ]; then
    echo "  ⚠  روی شاخهٔ main نیستی (روی $BRANCH هستی)."
    echo "     برای کار عادی باید روی main باشی: git checkout main"
    PROBLEM=1
fi

if [ -n "$DIRTY" ]; then
    echo "  ⚠  تغییرات ذخیره‌نشده روی سرور داری:"
    git status --short | sed 's/^/       /'
    echo "     اینا یا باید commit بشن یا کنار گذاشته بشن."
    PROBLEM=1
fi

if [ "$AHEAD" -gt 0 ] 2>/dev/null; then
    echo "  ⚠  سرور $AHEAD کامیت جلوتر از گیت‌هابه."
    echo "     یعنی یه چیزی push نشده. این دستور رو بزن:"
    echo "         git push origin main"
    PROBLEM=1
fi

if [ "$BEHIND" -gt 0 ] 2>/dev/null; then
    echo "  ⚠  گیت‌هاب $BEHIND کامیت جلوتر از سروره."
    echo "     یعنی یه چیزی روی گیت‌هاب هست که نیاوردی. این دستور رو بزن:"
    echo "         git pull origin main"
    PROBLEM=1
fi

if [ "$PROBLEM" -eq 0 ]; then
    echo "  ✅  همه‌چیز هم‌گامه. سرور و گیت‌هاب یکی‌ان."
    echo "      با خیال راحت می‌تونی به CC کار بدی."
fi

echo "──────────────────────────────────────────"
echo ""
