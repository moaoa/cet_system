<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): User
    {
        $record->ref_number = $record->ref_number; // Preserve the original ref_number
        $record->fill($data)->save();

        return $record;
    }
    // protected function getCreatedNotification(): ?Notification
    // {
    //     return null; // Disable the default notification
    // }
    // public  function getSavedNotification(): ?Notification
    // {
    //     return null;
    // }
}
