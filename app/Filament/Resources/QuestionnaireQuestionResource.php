<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionnaireQuestionResource\Pages;
use App\Models\QuestionnaireQuestion;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionnaireQuestionResource extends Resource
{
    protected static ?string $model = QuestionnaireQuestion::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationLabel = 'سوالات فرم عضویت';
    protected static ?string $modelLabel = 'سوال';
    protected static ?string $pluralModelLabel = 'سوالات';
    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه اعضا';
    protected static ?int $navigationSort = 3;


    public static function canViewAny(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('سوال')->schema([
                Forms\Components\TextInput::make('question')
                    ->label('متن سوال')
                    ->required()
                    ->placeholder('مثلاً: چطور با پرده‌خوان آشنا شدید؟'),
                Forms\Components\TextInput::make('placeholder')
                    ->label('راهنمای فیلد')
                    ->placeholder('مثلاً: پاسخ خود را بنویسید...'),
                \Filament\Schemas\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('sort_order')
                        ->label('ترتیب')
                        ->numeric()
                        ->default(0),
                    Forms\Components\Toggle::make('is_required')
                        ->label('اجباری')
                        ->default(true),
                    Forms\Components\Toggle::make('is_active')
                        ->label('فعال')
                        ->default(true),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(50),
                Tables\Columns\TextColumn::make('question')
                    ->label('سوال')
                    ->wrap(),
                Tables\Columns\IconColumn::make('is_required')
                    ->label('اجباری')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit'   => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
