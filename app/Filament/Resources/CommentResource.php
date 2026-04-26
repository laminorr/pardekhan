<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use BackedEnum;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'نظرات';
    protected static ?string $modelLabel = 'نظر';
    protected static ?string $pluralModelLabel = 'نظرات';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('episode_id')
                ->label('اپیزود')
                ->relationship('episode', 'title_fa')
                ->required(),
            Forms\Components\TextInput::make('name')
                ->label('نام')
                ->required(),
            Forms\Components\Textarea::make('body')
                ->label('متن نظر')
                ->rows(4)
                ->required(),
            Forms\Components\Toggle::make('is_approved')
                ->label('تأیید شده')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('episode.title_fa')
                    ->label('اپیزود')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                Tables\Columns\TextColumn::make('body')
                    ->label('متن')
                    ->limit(60)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('تأیید')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('وضعیت تأیید'),
                Tables\Filters\SelectFilter::make('episode_id')
                    ->label('اپیزود')
                    ->relationship('episode', 'title_fa'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('toggle_approve')
                    ->label(fn (Comment $record) => $record->is_approved ? 'رد' : 'تأیید')
                    ->icon(fn (Comment $record) => $record->is_approved ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Comment $record) => $record->is_approved ? 'danger' : 'success')
                    ->action(fn (Comment $record) => $record->update(['is_approved' => !$record->is_approved])),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
