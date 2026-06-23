<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use App\Models\Layer;
use App\Models\Venue;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'دورهمی‌ها';
    protected static ?string $modelLabel = 'دورهمی';
    protected static ?string $pluralModelLabel = 'دورهمی‌ها';
    protected static string|\UnitEnum|null $navigationGroup = 'دورهمی‌ها';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->isSuperAdmin() || $user?->isEventManager();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Tabs::make('event')->tabs([

                // تب ۱: اطلاعات اصلی
                \Filament\Schemas\Components\Tabs\Tab::make('اطلاعات اصلی')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('عنوان دورهمی')
                            ->required()
                            ->placeholder('مثلاً: دورهمی روانشناسی همدلی'),
                        Forms\Components\TextInput::make('subtitle')
                            ->label('زیرعنوان'),
                        Forms\Components\Textarea::make('description')
                            ->label('توضیحات کامل')
                            ->rows(5),
                        Forms\Components\FileUpload::make('image')
                            ->label('تصویر دورهمی')
                            ->image()
                            ->disk('public')
                            ->directory('events'),
                    ]),

                // تب ۲: زمان و مکان
                \Filament\Schemas\Components\Tabs\Tab::make('زمان و مکان')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\Select::make('venue_id')
                            ->label('مکان')
                            ->options(Venue::active()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('تاریخ و ساعت برگزاری')
                            ->required()
                            ->seconds(false)
                            ->live()
                            ->helperText(fn ($state) => $state
                                ? 'معادل شمسی: ' . pdate($state, 'l j F Y - H:i')
                                : 'تاریخ میلادی را انتخاب کنید؛ معادل شمسی اینجا نمایش داده می‌شود'),
                    ]),

                // تب ۳: ظرفیت و قیمت
                \Filament\Schemas\Components\Tabs\Tab::make('ظرفیت و قیمت')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('capacity')
                                ->label('ظرفیت')
                                ->numeric()
                                ->required()
                                ->minValue(1),
                            Forms\Components\TextInput::make('min_quorum')
                                ->label('حد نصاب برگزاری')
                                ->numeric()
                                ->default(1)
                                ->helperText('حداقل تعداد لازم برای برگزاری'),
                        ]),
                        Forms\Components\TextInput::make('base_price')
                            ->label('قیمت پایه (تومان)')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ]),

                // تب ۴: دسترسی لایه‌ها
                \Filament\Schemas\Components\Tabs\Tab::make('دسترسی و تخفیف')
                    ->icon('heroicon-o-rectangle-stack')
                    ->schema([
                        Forms\Components\CheckboxList::make('layers')
                            ->label('لایه‌های مجاز')
                            ->relationship('layers', 'name')
                            ->helperText('کدام لایه‌ها این دورهمی را می‌بینند'),
                        Forms\Components\Select::make('invitedMembers')
                            ->label('دعوت اختصاصی (افراد خاص)')
                            ->relationship('invitedMembers', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name . ' (' . $record->phone . ')')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('این افراد صرف‌نظر از لایه‌شان دورهمی را می‌بینند'),
                    ]),

                // تب ۵: وضعیت
                \Filament\Schemas\Components\Tabs\Tab::make('وضعیت')
                    ->icon('heroicon-o-flag')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('وضعیت')
                            ->options([
                                'draft'          => 'پیش‌نویس',
                                'active'         => 'فعال (باز برای ثبت‌نام)',
                                'full'           => 'تکمیل ظرفیت',
                                'closed'         => 'ثبت‌نام بسته',
                                'needs_decision' => 'نیازمند تصمیم مدیر',
                                'cancelled'      => 'لغو شده',
                                'completed'      => 'برگزار شده',
                            ])
                            ->default('draft')
                            ->required(),
                    ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->disk('public')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Event $r) => $r->subtitle),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('تاریخ')
                    ->formatStateUsing(fn ($state) => pdate($state, 'Y/m/d H:i'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->label('ظرفیت')
                    ->formatStateUsing(fn (Event $r) => $r->confirmedCount() . ' / ' . $r->capacity),
                Tables\Columns\TextColumn::make('base_price')
                    ->label('قیمت')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' ت'),
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'draft'          => 'پیش‌نویس',
                        'active'         => 'فعال',
                        'full'           => 'تکمیل',
                        'closed'         => 'بسته',
                        'needs_decision' => 'نیاز به تصمیم',
                        'cancelled'      => 'لغو شده',
                        'completed'      => 'برگزار شده',
                        default          => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'active'         => 'success',
                        'full'           => 'warning',
                        'needs_decision' => 'warning',
                        'cancelled'      => 'danger',
                        'completed'      => 'info',
                        default          => 'gray',
                    }),
            ])
            ->defaultSort('starts_at', 'desc')
            ->actions([
                \Filament\Actions\Action::make('attendance')
                    ->label('حضور و غیاب')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->url(fn ($record) => static::getUrl('attendance', ['record' => $record])),
                \Filament\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit'   => Pages\EditEvent::route('/{record}/edit'),
            'attendance' => Pages\EventAttendance::route('/{record}/attendance'),
        ];
    }
}
