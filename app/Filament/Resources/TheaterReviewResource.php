<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TheaterReviewResource\Pages;
use App\Models\TheaterReview;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TheaterReviewResource extends Resource
{
    protected static ?string $model = TheaterReview::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'نقد تئاتر';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'نقد تئاتر';
    protected static ?string $pluralModelLabel = 'نقدهای تئاتر';

    public static function canViewAny(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('title')
                ->label('عنوان')
                ->required()
                ->maxLength(255),
            Forms\Components\FileUpload::make('cover')
                ->label('کاور / پوستر')
                ->image()
                ->disk('public')
                ->directory('theater')
                ->imageEditor()
                ->helperText('عکس به‌صورت خودکار بهینه می‌شود'),
            Forms\Components\RichEditor::make('body')
                ->label('متن نقد')
                ->columnSpanFull()
                // تصاویر داخل متن باید روی دیسک عمومی ذخیره شوند
                // (دیسک local/private روی این هاست برای تصاویر خطای 403 می‌دهد)
                ->fileAttachmentsDisk('public')
                ->fileAttachmentsDirectory('theater/inline'),
            Forms\Components\Toggle::make('is_published')
                ->label('منتشر شود')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('cover')->label('کاور')->disk('public')->square(),
                Tables\Columns\TextColumn::make('title')->label('عنوان')->searchable()->limit(50),
                Tables\Columns\IconColumn::make('is_published')->label('منتشر')->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ')
                    ->formatStateUsing(fn ($state) => pdate($state, 'Y/m/d'))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')->label('وضعیت انتشار'),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTheaterReviews::route('/'),
            'create' => Pages\CreateTheaterReview::route('/create'),
            'edit'   => Pages\EditTheaterReview::route('/{record}/edit'),
        ];
    }
}
