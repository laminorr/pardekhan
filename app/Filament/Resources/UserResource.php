<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'مدیران';
    protected static ?string $modelLabel = 'مدیر';
    protected static ?string $pluralModelLabel = 'مدیران';
    protected static string|\UnitEnum|null $navigationGroup = 'تنظیمات';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('اطلاعات مدیر')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('نام')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('ایمیل')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('role')
                    ->label('نقش')
                    ->options([
                        'super_admin'   => 'مدیر اصلی (دسترسی کامل)',
                        'event_manager' => 'مدیر دورهمی (دورهمی و مالی)',
                        'reception'     => 'مسئول پذیرش (فقط اسکن QR)',
                    ])
                    ->required()
                    ->default('reception'),
                Forms\Components\TextInput::make('password')
                    ->label('رمز عبور')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context) => $context === 'create')
                    ->helperText('برای تغییر نکردن رمز، خالی بگذارید'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->label('ایمیل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('نقش')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'super_admin'   => 'مدیر اصلی',
                        'event_manager' => 'مدیر دورهمی',
                        'reception'     => 'مسئول پذیرش',
                        default         => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'super_admin'   => 'danger',
                        'event_manager' => 'warning',
                        'reception'     => 'info',
                        default         => 'gray',
                    }),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()->label('ویرایش'),
                \Filament\Actions\DeleteAction::make()
                    ->visible(fn (User $record) => $record->id !== auth()->id()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
