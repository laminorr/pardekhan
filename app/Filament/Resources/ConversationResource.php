<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConversationResource\Pages;
use App\Models\Conversation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ConversationResource extends Resource
{
    protected static ?string $model = Conversation::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'گفتگوها';
    protected static ?string $modelLabel = 'گفتگو';
    protected static ?string $pluralModelLabel = 'گفتگوها';
    protected static string|\UnitEnum|null $navigationGroup = 'پیام‌ها';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->isSuperAdmin() || $user?->isEventManager();
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Conversation::where('admin_unread', true)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('admin_unread')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-s-envelope')
                    ->falseIcon('heroicon-o-envelope-open')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('member.first_name')
                    ->label('عضو')
                    ->formatStateUsing(fn ($record) => $record->member->first_name . ' ' . $record->member->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('subject')
                    ->label('موضوع')
                    ->limit(35),
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'open' ? 'باز' : 'بسته')
                    ->color(fn ($state) => $state === 'open' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('last_message_at')
                    ->label('آخرین پیام')
                    ->formatStateUsing(fn ($state) => pdate($state, 'Y/m/d H:i'))
                    ->sortable(),
            ])
            ->defaultSort('last_message_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(['open' => 'باز', 'closed' => 'بسته']),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label('مشاهده و پاسخ')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->url(fn (Conversation $record) => static::getUrl('view', ['record' => $record])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConversations::route('/'),
            'view'  => Pages\ViewConversation::route('/{record}'),
        ];
    }
}
