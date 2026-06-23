<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Layer;
use App\Models\Member;
use App\Models\QuestionnaireAnswer;
use App\Models\QuestionnaireQuestion;
use App\Models\ScoreLog;
use BackedEnum;
use Filament\Forms;
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


    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->isSuperAdmin() || $user?->isEventManager();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('اطلاعات پایه')->schema([
                \Filament\Schemas\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('first_name')->label('نام')->required(),
                    Forms\Components\TextInput::make('last_name')->label('نام خانوادگی')->required(),
                ]),
                \Filament\Schemas\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('phone')->label('شماره موبایل')->required(),
                    Forms\Components\Select::make('status')
                        ->label('وضعیت')
                        ->options([
                            'otp_pending'           => 'در انتظار تایید OTP',
                            'questionnaire_pending'  => 'در انتظار تکمیل فرم',
                            'pending_review'         => 'در انتظار بررسی',
                            'needs_more_info'        => 'نیاز به اطلاعات بیشتر',
                            'approved'               => 'تایید شده',
                            'rejected'               => 'رد شده',
                            'suspended'              => 'تعلیق شده',
                        ])
                        ->required(),
                ]),
                \Filament\Schemas\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('layer_id')
                        ->label('لایه عضویت')
                        ->options(Layer::active()->pluck('name', 'id'))
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('خودکار از روی امتیاز تعیین می‌شود'),
                    Forms\Components\TextInput::make('score')
                        ->label('امتیاز')
                        ->numeric()
                        ->default(0)
                        ->helperText('با ذخیره، لایه خودکار به‌روز می‌شود'),
                ]),
            ]),

            \Filament\Schemas\Components\Section::make('عکس پروفایل')
                ->visible(fn ($record) => $record && $record->avatar)
                ->schema([
                    Forms\Components\Placeholder::make('avatar_status')
                        ->label('وضعیت')
                        ->content(fn ($record) => $record->avatar_approved ? '✅ تایید شده' : '⏳ در انتظار تایید'),
                    \Filament\Forms\Components\ViewField::make('avatar_preview')
                        ->label('')
                        ->dehydrated(false)
                        ->view('filament.member-avatar'),
                ]),

            \Filament\Schemas\Components\Section::make('پاسخ‌های فرم عضویت')
                ->description('پاسخ‌هایی که کاربر در فرم ثبت‌نام داده است')
                ->schema(function ($record) {
                    if (! $record) return [];
                    
                    $answers = QuestionnaireAnswer::with('question')
                        ->where('member_id', $record->id)
                        ->get();
                    
                    if ($answers->isEmpty()) {
                        return [
                            Forms\Components\Placeholder::make('no_answers')
                                ->label('')
                                ->content('هنوز پاسخی ثبت نشده است'),
                        ];
                    }
                    
                    return $answers->map(fn ($answer) => 
                        Forms\Components\Placeholder::make('answer_' . $answer->id)
                            ->label($answer->question->question ?? 'سوال')
                            ->content($answer->answer)
                    )->toArray();
                }),

            \Filament\Schemas\Components\Section::make('تاریخچه امتیاز')
                ->collapsed()
                ->schema(function ($record) {
                    if (! $record) return [];
                    $logs = ScoreLog::where('member_id', $record->id)->latest()->limit(20)->get();
                    if ($logs->isEmpty()) {
                        return [Forms\Components\Placeholder::make('no_logs')->label('')->content('هنوز تغییری ثبت نشده')];
                    }
                    return $logs->map(fn ($log) =>
                        Forms\Components\Placeholder::make('log_' . $log->id)
                            ->label($log->created_at->format('Y/m/d H:i') . ' — ' . ($log->points >= 0 ? '+' : '') . $log->points)
                            ->content($log->reason_label . ' (امتیاز نهایی: ' . $log->score_after . ')')
                    )->toArray();
                }),

            \Filament\Schemas\Components\Section::make('یادداشت خصوصی ادمین')
                ->description('این یادداشت فقط در پنل مدیریت قابل مشاهده است')
                ->schema([
                    Forms\Components\Textarea::make('admin_note')
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
                Tables\Columns\TextColumn::make('first_name')
                    ->label('نام')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('نام خانوادگی')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('موبایل')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'approved'               => 'success',
                        'rejected', 'suspended'  => 'danger',
                        'needs_more_info'        => 'info',
                        default                  => 'warning',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'otp_pending'            => 'در انتظار OTP',
                        'questionnaire_pending'   => 'در انتظار فرم',
                        'pending_review'          => 'در انتظار بررسی',
                        'needs_more_info'         => 'نیاز به اطلاعات',
                        'approved'                => 'تایید شده',
                        'rejected'                => 'رد شده',
                        'suspended'               => 'تعلیق شده',
                        default                   => $state,
                    }),
                Tables\Columns\TextColumn::make('layer.name')
                    ->label('لایه')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('score')
                    ->label('امتیاز')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ عضویت')
                    ->formatStateUsing(fn ($state) => jdate($state, 'Y/m/d'))
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
