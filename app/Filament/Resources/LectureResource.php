<?php

namespace App\Filament\Resources;


use App\Enums\WeekDays;
use App\Filament\Resources\LectureResource\Pages;
use App\Filament\Resources\LectureResource\RelationManagers;
use App\Models\ClassRoom;
use App\Models\Group;
use App\Models\Lecture;
use App\Models\Teacher;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LectureResource extends Resource
{
    protected static ?string $model = Lecture::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'ادارة المحاضرات';

    public static function getModelLabel(): string
    {
        return 'محاضرة'; // Directly writing the translation for "User"
    }

    public static function getPluralModelLabel(): string
    {
        return 'محاضرات'; // Directly writing the translation for "Users"
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TimePicker::make('start_time')
                    ->required()
                    ->label('وقت البدء'), // "Start Time"
                Forms\Components\TimePicker::make('end_time')
                    ->required()
                    ->label('وقت الانتهاء'), // "End Time"
                Forms\Components\Select::make('day_of_week')
                    ->required()
                    ->live()
                    ->options(WeekDays::class)
                    ->label('يوم الأسبوع'), // "Day of the Week"
                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->required()
                    ->label('المادة'), // "Subject"
                Forms\Components\Select::make('class_room_id')
                    ->relationship('classRoom', 'name')
                    ->options(function (Forms\Get $get) {
                        $start_time = (string)$get('start_time');
                        $end_time = (string)$get('end_time');
                        $day_of_week = (int)$get('day_of_week');

                        if ($start_time == null || $end_time == null || $day_of_week == null) {
                            return [];
                        }

                        $lecturesInTimeRange = Lecture::where('day_of_week', $day_of_week)
                            ->where('start_time', '<=', $start_time)
                            ->where('end_time', '>', $start_time)
                            ->orWhere(function ($query) use ($end_time) {
                                $query->where('start_time', '>', $end_time)
                                    ->where('end_time', '<=', $end_time);
                            })
                            ->get();

                        return ClassRoom::whereNotIn(
                            'id',
                            $lecturesInTimeRange->pluck('class_room_id')
                        )->pluck('name', 'id')->toArray();
                    })
                    ->required()
                    ->label('الصف الدراسي'), // "Class Room"
                Forms\Components\Select::make('group_id')
                    ->relationship('group', 'name')
                    ->options(Group::all()->pluck('name', 'id'))
                    ->required()
                    ->label('المجموعة'), // "Group"
                Forms\Components\Select::make('teacher_id')
                    ->label('الأستاذ') // "Teacher"
                    ->options(fn(Forms\Get $get) => $get('start_time'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('day_of_week')
                    ->formatStateUsing(fn($state) => WeekDays::from($state)->toArabic())
                    ->label('يوم '), // "Day of the Week"
                Tables\Columns\TextColumn::make('start_time')
                    ->time()
                    ->sortable()
                    ->label('وقت البدء'), // "Start Time"
                Tables\Columns\TextColumn::make('end_time')
                    ->time()
                    ->sortable()
                    ->label('وقت الانتهاء'), // "End Time"
                Tables\Columns\TextColumn::make('teacher.name')
                    ->searchable()
                    ->label('الأستاذ'), // "Teacher"

                Tables\Columns\TextColumn::make('subject.name')
                    ->searchable()
                    ->label('المادة'), // "Subject"
                Tables\Columns\TextColumn::make('classRoom.name')
                    ->searchable()
                    ->label('القاعة'), // "Class Room"
                Tables\Columns\TextColumn::make('group.name')
                    ->label('المجموعة'), // "Group"

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('تاريخ الإنشاء'), // "Created At"
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('تاريخ التحديث'), // "Updated At"
            ])
            ->filters([
                //
            ])
            // ->headerActions([
            //     Tables\Actions\CreateAction::make(),
            // ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListLectures::route('/'),
            'create' => Pages\CreateLecture::route('/create'),
            'edit' => Pages\EditLecture::route('/{record}/edit'),
        ];
    }

    public static function getAvailableClassRooms($start_time, $end_time, $day_of_week)
    {
        if ($start_time == null || $end_time == null || $day_of_week == null) {
            return [];
        }

        $lecturesInTimeRange = Lecture::where('day_of_week', $day_of_week)
            ->where('start_time', '<=', $start_time)
            ->where('end_time', '>', $start_time)
            ->orWhere(function ($query) use ($end_time) {
                $query->where('start_time', '>', $end_time)
                    ->where('end_time', '<=', $end_time);
            })
            ->get();


        $availableClassrooms = ClassRoom::whereNotIn(
            'id',
            $lecturesInTimeRange->pluck('class_room_id')
        )
            ->pluck('name', 'id');

        // dd($availableClassrooms);
        return $availableClassrooms;
    }
}
