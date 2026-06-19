<?php

namespace App\Filament\Pages;

use App\Models\Layer;
use App\Models\Member;
use App\Services\MessagingService;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class SendMessage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationLabel = 'ارسال پیام';
    protected static string|\UnitEnum|null $navigationGroup = 'پیام‌ها';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'ارسال پیام به اعضا';

    protected string $view = 'filament.pages.send-message';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user?->isSuperAdmin() || $user?->isEventManager();
    }

    public function mount(): void
    {
        $this->form->fill(['audience_type' => 'all', 'is_replyable' => true]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('گیرنده')->schema([
                Forms\Components\Select::make('audience_type')
                    ->label('ارسال به')
                    ->options([
                        'all'    => 'همه اعضا',
                        'layer'  => 'یک لایه خاص',
                        'single' => 'یک نفر خاص',
                    ])
                    ->default('all')
                    ->live()
                    ->required(),

                Forms\Components\Select::make('layer_id')
                    ->label('انتخاب لایه')
                    ->options(Layer::active()->pluck('name', 'id'))
                    ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('audience_type') === 'layer')
                    ->required(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('audience_type') === 'layer'),

                Forms\Components\Select::make('member_id')
                    ->label('انتخاب عضو')
                    ->options(fn () => Member::where('status', 'approved')
                        ->get()
                        ->mapWithKeys(fn ($m) => [$m->id => $m->first_name . ' ' . $m->last_name . ' (' . $m->phone . ')']))
                    ->searchable()
                    ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('audience_type') === 'single')
                    ->required(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('audience_type') === 'single'),
            ]),

            \Filament\Schemas\Components\Section::make('متن پیام')->schema([
                Forms\Components\TextInput::make('subject')
                    ->label('موضوع')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Textarea::make('body')
                    ->label('متن')
                    ->required()
                    ->rows(5),
                Forms\Components\Toggle::make('is_replyable')
                    ->label('اعضا می‌توانند به این پیام پاسخ دهند')
                    ->default(true)
                    ->helperText('اگر خاموش باشد، پیام فقط اطلاع‌رسانی است'),
            ]),
        ])->statePath('data');
    }

    public function send(): void
    {
        $data = $this->form->getState();

        $broadcast = app(MessagingService::class)->broadcast(
            $data['subject'],
            $data['body'],
            $data['audience_type'],
            $data['member_id'] ?? null,
            $data['layer_id'] ?? null,
            $data['is_replyable'] ?? true,
            auth()->id(),
        );

        $count = $broadcast->recipients()->count();

        Notification::make()
            ->success()
            ->title("پیام برای {$count} نفر ارسال شد")
            ->send();

        $this->form->fill(['audience_type' => 'all', 'is_replyable' => true]);
    }
}
