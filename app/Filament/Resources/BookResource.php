<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'معرفی کتاب';
    protected static string|\UnitEnum|null $navigationGroup = 'محتوا';
    protected static ?int $navigationSort = 5;
    protected static ?string $modelLabel = 'کتاب';
    protected static ?string $pluralModelLabel = 'کتاب‌ها';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\FileUpload::make('cover')
                ->label('کاور کتاب')
                ->image()
                ->disk('public')
                ->directory('books')
                ->imageEditor()
                ->required()
                ->helperText('عکس به‌صورت خودکار بهینه و کم‌حجم می‌شود'),
            Forms\Components\TextInput::make('buy_url')
                ->label('لینک خرید از دیجی‌کالا')
                ->url()
                ->nullable()
                ->placeholder('https://www.digikala.com/...'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('cover')->label('کاور')->disk('public')->square(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ افزودن')
                    ->formatStateUsing(fn ($state) => pdate($state, 'Y/m/d'))
                    ->sortable(),
            ])
            ->recordActions([
                \Filament\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit'   => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
