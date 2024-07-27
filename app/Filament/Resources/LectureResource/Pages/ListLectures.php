<?php

namespace App\Filament\Resources\LectureResource\Pages;

use App\Enums\UserType;
use App\Filament\Resources\LectureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLectures extends ListRecords
{
    protected static string $resource = LectureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(condition: auth()->user()->type === UserType::Admin),
        ];
    }
}
