<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'مجله (وبلاگ)';
    protected static string|\UnitEnum|null $navigationGroup = 'محتوا';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'مطلب';
    protected static ?string $pluralModelLabel = 'مطالب مجله';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('title')
                ->label('عنوان')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('excerpt')
                ->label('خلاصه (اختیاری)')
                ->rows(2)
                ->maxLength(300)
                ->helperText('اگر خالی بماند، از ابتدای متن ساخته می‌شود'),
            Forms\Components\Textarea::make('body')
                ->label('متن کامل')
                ->rows(14)
                ->required(),
            Forms\Components\FileUpload::make('cover')
                ->label('عکس کاور')
                ->image()
                ->disk('public')
                ->directory('posts')
                ->imageEditor()
                ->helperText('عکس به‌صورت خودکار بهینه و کم‌حجم می‌شود'),
            Forms\Components\Toggle::make('is_published')
                ->label('منتشر شود')
                ->default(true),
            Forms\Components\DateTimePicker::make('published_at')
                ->label('تاریخ انتشار')
                ->default(now())
                ->live()
                ->helperText(fn ($state) => $state ? 'معادل شمسی: ' . pdate($state, 'Y/m/d H:i') : null),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('cover')->label('کاور')->square(),
                Tables\Columns\TextColumn::make('title')->label('عنوان')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('views')->label('بازدید')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->label('منتشر')->boolean(),
                Tables\Columns\TextColumn::make('published_at')
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
            'index'  => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit'   => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
