<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LayerResource\Pages;
use App\Models\Layer;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class LayerResource extends Resource
{
    protected static ?string $model = Layer::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'لایه‌ها';
    protected static ?string $modelLabel = 'لایه';
    protected static ?string $pluralModelLabel = 'لایه‌ها';
    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه اعضا';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('اطلاعات لایه')->schema([
                \Filament\Schemas\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('نام لایه')
                        ->required()
                        ->placeholder('مثلاً: همراه پرده‌خوان'),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('ترتیب نمایش')
                        ->numeric()
                        ->default(0),
                ]),
                \Filament\Schemas\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('min_score')
                        ->label('حداقل امتیاز')
                        ->numeric()
                        ->default(0)
                        ->helperText('کاربر با رسیدن به این امتیاز وارد این لایه می‌شود'),
                    Forms\Components\TextInput::make('discount_percent')
                        ->label('تخفیف پایه (%)')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->maxValue(100)
                        ->helperText('این تخفیف روی همه دورهمی‌ها اعمال می‌شود مگر override شود'),
                ]),
                Forms\Components\TextInput::make('early_access_hours')
                    ->label('دسترسی زودتر (ساعت)')
                    ->numeric()
                    ->default(0)
                    ->helperText('چند ساعت قبل از بقیه می‌توانند ثبت‌نام کنند'),
            ]),

            \Filament\Schemas\Components\Section::make('امکانات')->schema([
                Forms\Components\Toggle::make('has_exclusive_events')
                    ->label('دسترسی به دورهمی‌های اختصاصی')
                    ->default(false),
                Forms\Components\Toggle::make('has_special_invitations')
                    ->label('امکان دریافت دعوت ویژه')
                    ->default(false),
                Forms\Components\Toggle::make('is_active')
                    ->label('لایه فعال است')
                    ->default(true),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('نام لایه')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('min_score')
                    ->label('حداقل امتیاز')
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_percent')
                    ->label('تخفیف')
                    ->formatStateUsing(fn ($state) => $state . '%'),
                Tables\Columns\TextColumn::make('early_access_hours')
                    ->label('دسترسی زودتر')
                    ->formatStateUsing(fn ($state) => $state . ' ساعت'),
                Tables\Columns\IconColumn::make('has_exclusive_events')
                    ->label('دورهمی اختصاصی')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
                Tables\Columns\TextColumn::make('members_count')
                    ->label('اعضا')
                    ->counts('members'),
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
            'index'  => Pages\ListLayers::route('/'),
            'create' => Pages\CreateLayer::route('/create'),
            'edit'   => Pages\EditLayer::route('/{record}/edit'),
        ];
    }
}
