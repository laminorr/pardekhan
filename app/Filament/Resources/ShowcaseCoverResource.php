<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShowcaseCoverResource\Pages;
use App\Models\ShowcaseCover;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ShowcaseCoverResource extends Resource
{
    protected static ?string $model = ShowcaseCover::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'ویترین کاورها';
    protected static string|\UnitEnum|null $navigationGroup = 'محتوا';
    protected static ?int $navigationSort = 6;
    protected static ?string $modelLabel = 'کاور ویترین';
    protected static ?string $pluralModelLabel = 'کاورهای ویترین';

    public static function canViewAny(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\FileUpload::make('image')
                ->label('کاور')
                ->image()
                ->disk('public')
                ->directory('showcase')
                ->imageEditor()
                ->required()
                ->helperText('عکس به‌صورت خودکار بهینه می‌شود'),
            Forms\Components\Select::make('type')
                ->label('ردیف')
                ->options([
                    'film' => 'فیلم (ردیف بالا)',
                    'book' => 'کتاب (ردیف پایین)',
                ])
                ->required()
                ->default('film'),
            Forms\Components\TextInput::make('imdb_url')
                ->label('لینک IMDB')
                ->url()
                ->nullable()
                ->placeholder('https://www.imdb.com/...'),
            Forms\Components\TextInput::make('sort_order')
                ->label('ترتیب')
                ->numeric()
                ->default(0),
            Forms\Components\Toggle::make('is_active')
                ->label('فعال')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('کاور')->disk('public')->square(),
                Tables\Columns\TextColumn::make('type')
                    ->label('ردیف')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'book' ? 'کتاب' : 'فیلم')
                    ->color(fn (string $state) => $state === 'book' ? 'warning' : 'info'),
                Tables\Columns\TextColumn::make('sort_order')->label('ترتیب')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('فعال'),
            ])
            ->recordActions([
                \Filament\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListShowcaseCovers::route('/'),
            'create' => Pages\CreateShowcaseCover::route('/create'),
            'edit'   => Pages\EditShowcaseCover::route('/{record}/edit'),
        ];
    }
}
