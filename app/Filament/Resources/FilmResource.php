<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FilmResource\Pages;
use App\Models\DailyFilm;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class FilmResource extends Resource
{
    protected static ?string $model = DailyFilm::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-film';
    protected static ?string $navigationLabel = 'فیلم امروز';
    protected static string|\UnitEnum|null $navigationGroup = 'محتوا';
    protected static ?int $navigationSort = 5;
    protected static ?string $modelLabel = 'فیلم';
    protected static ?string $pluralModelLabel = 'فیلم‌ها';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('title')
                ->label('عنوان فارسی')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('original_title')
                ->label('عنوان اصلی (انگلیسی)')
                ->maxLength(255),
            Forms\Components\TextInput::make('year')
                ->label('سال ساخت')
                ->maxLength(10),
            Forms\Components\TextInput::make('director')
                ->label('کارگردان')
                ->maxLength(255),
            Forms\Components\TextInput::make('genre')
                ->label('ژانر')
                ->maxLength(255),
            Forms\Components\FileUpload::make('cover')
                ->label('کاور (آپلود)')
                ->image()
                ->disk('public')
                ->directory('films')
                ->helperText('یا به‌جای آپلود، لینک عکس را در فیلد زیر بگذارید'),
            Forms\Components\TextInput::make('cover_url')
                ->label('لینک عکس کاور')
                ->url()
                ->maxLength(500),
            Forms\Components\Textarea::make('description')
                ->label('معرفی فیلم')
                ->rows(5)
                ->maxLength(3000),
            Forms\Components\TextInput::make('link')
                ->label('لینک (تریلر / تماشا)')
                ->url()
                ->maxLength(500),
            Forms\Components\DatePicker::make('show_date')
                ->label('تاریخ نمایش')
                ->default(now())
                ->required()
                ->live()
                ->helperText(fn ($state) => $state ? 'معادل شمسی: ' . pdate($state, 'Y/m/d') : null),
            Forms\Components\Toggle::make('is_active')
                ->label('فعال (نمایش در داشبورد)')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('show_date', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                    ->label('کاور')
                    ->square(),
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('show_date')
                    ->label('تاریخ نمایش')
                    ->formatStateUsing(fn ($state) => pdate($state, 'Y/m/d'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('وضعیت'),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFilms::route('/'),
            'create' => Pages\CreateFilm::route('/create'),
            'edit'   => Pages\EditFilm::route('/{record}/edit'),
        ];
    }
}
