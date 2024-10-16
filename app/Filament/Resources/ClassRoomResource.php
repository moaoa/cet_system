<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassRoomResource\Pages;
use App\Filament\Resources\ClassRoomResource\RelationManagers;
use App\Models\ClassRoom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClassRoomResource extends Resource
{
    protected static ?string $model = ClassRoom::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'ادارة القاعات';
    protected static ?string $navigationGroup = 'عام';
    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return 'قاعة'; // Directly writing the translation for "User"
    }

    public static function getPluralModelLabel(): string
    {
        return 'قاعات'; // Directly writing the translation for "Users"
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('room')
                    ->label('نوع القاعة')
                    ->options([
                        'القاعة' => 'القاعة',
                        'معمل الشبكات' => 'معمل الشبكات',
                        'معمل الكهربائية' => 'معمل الكهربائية',
                        'معمل الألكترونية' => 'معمل الألكترونية',
                        'معمل الانجليزي' => 'معمل الانجليزي',
                        'معمل الرقمية' => 'معمل الرقمية',
                        'المكتبة' => 'المكتبة',
                        'المسرح' => 'المسرح',
                    ])
                    ->required()
                    ->reactive(),


                Forms\Components\TextInput::make('Number_room')
                    ->label('رقم القاعة')
                    ->rules('numeric')
                    ->maxLength(2000)
                    ->required(function (callable $get) {
                        // Make it required only if 'معمل' or 'القاعة' is selected
                        $selectedRoom = $get('room');
                        return in_array($selectedRoom, ['القاعة', 'معمل الشبكات', 'معمل الكهربائية', 'معمل الألكترونية', 'معمل الانجليزي', 'معمل الرقمية']);
                    })
                    ->disabled(function (callable $get) {
                        // Disable the input if the selected room is not 'معمل' or 'القاعة'
                        $selectedRoom = $get('room');
                        return !in_array($selectedRoom, ['القاعة', 'معمل الشبكات', 'معمل الكهربائية', 'معمل الألكترونية', 'معمل الانجليزي', 'معمل الرقمية']);
                    }),
                // Make it reactive to enable interaction
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
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
                //
            ])
            ->actions([

                Tables\Actions\EditAction::make()
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
            'index' => Pages\ListClassRooms::route('/'),
            'create' => Pages\CreateClassRoom::route('/create'),
            'edit' => Pages\EditClassRoom::route('/{record}/edit'),
        ];
    }
}
