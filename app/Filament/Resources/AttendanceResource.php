<?php

namespace App\Filament\Resources;

use App\Enums\AttendanceStatus;
use App\Enums\Major;
use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\Semester;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'حضور وغياب الطلبة';
    protected static ?string $navigationGroup = 'الطالب';
    protected static ?int $navigationSort = 8;

    public static function getModelLabel(): string
    {
        return 'الحضور'; // Directly writing the translation for "User"
    }

    public static function getPluralModelLabel(): string
    {
        return 'حضور وغياب الطلبة'; // Directly writing the translation for "Users"
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('note')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\Select::make('lecture_id')
                    ->relationship('lecture', 'id')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->label('التاريخ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.ref_number')
                    ->searchable()
                    ->label('رقم القيد')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->label('اسم الطالب')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lecture.subject.name')
                    ->label('المادة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lecture.group.name')
                    ->label('المجموعة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn($state) => AttendanceStatus::from($state)->getLabel()),
                Tables\Columns\TextColumn::make('lecture.teacher.name')
                    ->label('اسم الاستاذ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        Forms\Components\Select::make('Major')
                            ->options(Major::class)
                            ->label('القسم')
                            ->live(),
                        Forms\Components\Select::make('semester_id')
                            ->options(fn(Forms\Get $get) => Semester::where('major', $get('Major'))->pluck('name', 'id'))
                            ->label('الفصل')
                            ->live(),
                        Forms\Components\Select::make('subject_id')
                            ->label('المادة')
                            ->options(
                                fn(Forms\Get $get) => Subject::where('semester_id', $get('semester_id'))->pluck('name', 'id')
                            ),
                        Forms\Components\Select::make('group_id')
                            ->label('المجموعة')
                            ->options(
                                fn(Forms\Get $get) => DB::table('group_subject')
                                    ->join('groups', 'groups.id', '=', 'group_subject.group_id')
                                    ->where('group_subject.subject_id', $get('subject_id'))
                                    ->pluck('groups.name', 'groups.id')
                            )
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->whereHas('lecture', function (Builder $query) use ($data) {
                                if ($data['subject_id'] == null) return $query;
                                $query->where('subject_id', $data['subject_id']);
                            })
                            ->whereHas('lecture', function (Builder $query) use ($data) {
                                if ($data['group_id'] == null) return $query;
                                $query->where('group_id', $data['group_id']);
                            });
                    })
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            // 'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
