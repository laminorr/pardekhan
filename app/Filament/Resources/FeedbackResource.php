<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages;
use App\Models\Feedback;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'بازخوردها';
    protected static ?string $modelLabel = 'بازخورد';
    protected static ?string $pluralModelLabel = 'بازخوردها';
    protected static string|\UnitEnum|null $navigationGroup = 'دورهمی‌ها';
    protected static ?int $navigationSort = 5;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->isSuperAdmin() || $user?->isEventManager();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('بازخورد عضو')->schema([
                Forms\Components\Placeholder::make('member_name')
                    ->label('عضو')
                    ->content(fn ($record) => $record?->member?->first_name . ' ' . $record?->member?->last_name),
                Forms\Components\Placeholder::make('event_title')
                    ->label('دورهمی')
                    ->content(fn ($record) => $record?->event?->title),
                Forms\Components\Placeholder::make('rating_display')
                    ->label('امتیاز')
                    ->content(fn ($record) => str_repeat('★', $record?->rating ?? 0) . str_repeat('☆', 5 - ($record?->rating ?? 0))),
                Forms\Components\Placeholder::make('comment_display')
                    ->label('نظر')
                    ->content(fn ($record) => $record?->comment ?: '—'),
            ]),
            \Filament\Schemas\Components\Section::make('پاسخ مدیریت')->schema([
                Forms\Components\Textarea::make('admin_reply')
                    ->label('پاسخ شما')
                    ->rows(3)
                    ->placeholder('پاسخ به این بازخورد...'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member.first_name')
                    ->label('عضو')
                    ->formatStateUsing(fn ($record) => $record->member->first_name . ' ' . $record->member->last_name)
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('event.title')
                    ->label('دورهمی')
                    ->limit(25),
                Tables\Columns\TextColumn::make('rating')
                    ->label('امتیاز')
                    ->formatStateUsing(fn ($state) => str_repeat('★', $state))
                    ->color('warning'),
                Tables\Columns\TextColumn::make('comment')
                    ->label('نظر')
                    ->limit(40)
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('admin_reply')
                    ->label('پاسخ داده')
                    ->getStateUsing(fn ($record) => ! empty($record->admin_reply))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ')
                    ->formatStateUsing(fn ($state) => pdate($state, 'Y/m/d'))
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('event_id')
                    ->label('دورهمی')
                    ->relationship('event', 'title'),
                SelectFilter::make('rating')
                    ->label('امتیاز')
                    ->options([5=>'۵ ستاره',4=>'۴ ستاره',3=>'۳ ستاره',2=>'۲ ستاره',1=>'۱ ستاره']),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()->label('مشاهده و پاسخ'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeedback::route('/'),
            'edit'  => Pages\EditFeedback::route('/{record}/edit'),
        ];
    }
}
