<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use BackedEnum;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-phone';
    protected static ?string $navigationLabel = 'مخاطبین';
    protected static ?string $modelLabel = 'مخاطب';
    protected static ?string $pluralModelLabel = 'مخاطبین';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('phone')
                    ->label('شماره تماس')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ عضویت')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
        ];
    }
}
