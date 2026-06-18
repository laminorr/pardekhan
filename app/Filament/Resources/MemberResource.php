<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Layer;
use App\Models\Member;
use BackedEnum;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'اعضا';
    protected static ?string $modelLabel = 'عضو';
    protected static ?string $pluralModelLabel = 'اعضا';
    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه اعضا';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('اطلاعات پایه')->schema([
                Grid::make(2)->schema([
                    TextInput::make('first_name')->label('نام')->required(),
                    TextInput::make('last_name')->label('نام خانوادگی')->required(),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('phone')->label('شماره موبایل')->required(),
                    Select::make('status')
                        ->label('وضعیت')
                        ->options([
                            'otp_pending'          => 'در انتظار تایید OTP',
                            'questionnaire_pending' => 'در انتظار تکمیل فرم',
                            'pending_review'        => 'در انتظار بررسی',
                            'needs_more_info'       => 'نیاز به اطلاعات بیشتر',
                            'approved'              => 'تایید شده',
                            'rejected'              => 'رد شده',
                            'suspended'             => 'تعلیق شده',
                        ])
                        ->required(),
                ]),
                Grid::make(2)->schema([
                    Select::make('layer_id')
                        ->label('لایه عضویت')
                        ->options(Layer::active()->pluck('name', 'id'))
                        ->nullable()
                        ->placeholder('بدون لایه'),
                    TextInput::make('score')
                        ->label('امتیاز')
                        ->numeric()
                        ->default(0),
                ]),
            ]),

            Section::make('یادداشت خصوصی ادمین')
                ->description('این یادداشت فقط در پنل مدیریت قابل مشاهده است')
                ->schema([
                    Textarea::make('admin_note')
                        ->label('')
                        ->rows(3)
                        ->placeholder('یادداشت خصوصی...'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('نام و نام خانوادگی')
                    ->getStateUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('موبایل')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('وضعیت')
                    ->colors([
                        'warning' => fn ($state) => in_array($state, ['otp_pending', 'questionnaire_pending', 'pending_review']),
                        'info'    => 'needs_more_info',
                        'success' => 'approved',
                        'danger'  => fn ($state) => in_array($state, ['rejected', 'suspended']),
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'otp_pending'           => 'در انتظار OTP',
                        'questionnaire_pending'  => 'در انتظار فرم',
                        'pending_review'         => 'در انتظار بررسی',
                        'needs_more_info'        => 'نیاز به اطلاعات',
                        'approved'               => 'تایید شده',
                        'rejected'               => 'رد شده',
                        'suspended'              => 'تعلیق شده',
                        default                  => $state,
                    }),
                Tables\Columns\TextColumn::make('layer.name')
                    ->label('لایه')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('score')
                    ->label('امتیاز')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ عضویت')
                    ->dateTime('Y/m/d')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending_review'  => 'در انتظار بررسی',
                        'needs_more_info' => 'نیاز به اطلاعات',
                        'approved'        => 'تایید شده',
                        'rejected'        => 'رد شده',
                        'suspended'       => 'تعلیق شده',
                    ]),
                SelectFilter::make('layer_id')
                    ->label('لایه')
                    ->options(Layer::active()->pluck('name', 'id')),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()->label('ویرایش'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'edit'  => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
