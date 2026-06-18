<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VenueResource\Pages;
use App\Models\Venue;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class VenueResource extends Resource
{
    protected static ?string $model = Venue::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'مکان‌ها';
    protected static ?string $modelLabel = 'مکان';
    protected static ?string $pluralModelLabel = 'مکان‌ها';
    protected static string|\UnitEnum|null $navigationGroup = 'دورهمی‌ها';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->isSuperAdmin() || $user?->isEventManager();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('اطلاعات مکان')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('نام مکان')
                    ->required()
                    ->placeholder('مثلاً: کافه پرده‌خوان'),
                Forms\Components\Textarea::make('address')
                    ->label('آدرس کامل')
                    ->required()
                    ->rows(2),
                Forms\Components\Textarea::make('access_notes')
                    ->label('توضیحات دسترسی')
                    ->rows(2)
                    ->placeholder('مثلاً: طبقه دوم، پلاک ۱۲'),
                \Filament\Schemas\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('map_link')
                        ->label('لینک نقشه')
                        ->url()
                        ->placeholder('https://...'),
                    Forms\Components\TextInput::make('phone')
                        ->label('شماره تماس'),
                ]),
                \Filament\Schemas\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('suggested_capacity')
                        ->label('ظرفیت پیشنهادی')
                        ->numeric(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('فعال')
                        ->default(true),
                ]),
                Forms\Components\Textarea::make('internal_note')
                    ->label('یادداشت داخلی (فقط ادمین)')
                    ->rows(2),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('address')
                    ->label('آدرس')
                    ->limit(40),
                Tables\Columns\TextColumn::make('suggested_capacity')
                    ->label('ظرفیت')
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
                Tables\Columns\TextColumn::make('events_count')
                    ->label('دورهمی‌ها')
                    ->counts('events'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVenues::route('/'),
            'create' => Pages\CreateVenue::route('/create'),
            'edit'   => Pages\EditVenue::route('/{record}/edit'),
        ];
    }
}
