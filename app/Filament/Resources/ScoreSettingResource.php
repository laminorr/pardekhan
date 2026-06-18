<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScoreSettingResource\Pages;
use App\Models\ScoreSetting;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ScoreSettingResource extends Resource
{
    protected static ?string $model = ScoreSetting::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'تنظیمات امتیاز';
    protected static ?string $modelLabel = 'امتیاز';
    protected static ?string $pluralModelLabel = 'تنظیمات امتیاز';
    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه اعضا';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make()->schema([
                \Filament\Schemas\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('label')
                        ->label('نام رفتار')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, $set, $context) {
                            // فقط موقع ساخت، key رو خودکار بساز
                            if ($context === 'create') {
                                $set('key', 'custom_' . Str::random(8));
                            }
                        }),
                    Forms\Components\TextInput::make('key')
                        ->label('کلید سیستمی')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('کلید رفتارهای خودکار را تغییر ندهید')
                        ->disabled(fn ($context) => $context === 'edit'),
                ]),
                \Filament\Schemas\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('points')
                        ->label('امتیاز (منفی برای کسر)')
                        ->numeric()
                        ->required(),
                    Forms\Components\Select::make('type')
                        ->label('نوع')
                        ->options([
                            'auto'   => 'خودکار',
                            'manual' => 'دستی توسط ادمین',
                        ])
                        ->default('manual')
                        ->required(),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('رفتار')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('points')
                    ->label('امتیاز')
                    ->badge()
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => ($state >= 0 ? '+' : '') . $state),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('نوع')
                    ->formatStateUsing(fn ($state) => $state === 'auto' ? 'خودکار' : 'دستی')
                    ->colors(['success' => 'auto', 'warning' => 'manual']),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()->label('ویرایش'),
                \Filament\Actions\DeleteAction::make()
                    ->visible(fn (ScoreSetting $record) => str_starts_with($record->key, 'custom_')),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListScoreSettings::route('/'),
            'create' => Pages\CreateScoreSetting::route('/create'),
            'edit'   => Pages\EditScoreSetting::route('/{record}/edit'),
        ];
    }
}
