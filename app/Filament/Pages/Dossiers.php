<?php

namespace App\Filament\Pages;

use App\Models\Member;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

class Dossiers extends Page
{
    use WithPagination;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationLabel = 'پرونده‌ها';

    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه اعضا';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'پرونده‌های اعضا';

    protected string $view = 'filament.pages.dossiers';

    /** جست‌وجو بر اساس نام یا موبایل */
    public string $search = '';

    /**
     * فقط مدیر اصلی (super_admin) به این بخش دسترسی دارد.
     */
    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->isSuperAdmin();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function getMembersProperty(): LengthAwarePaginator
    {
        $term = trim($this->search);

        return Member::query()
            ->with('layer')
            ->when($term !== '', function ($q) use ($term) {
                $q->where(function ($sub) use ($term) {
                    $sub->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$term}%"])
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20);
    }
}
