<?php

namespace App\Filament\Resources\DestinationCategoryResource\Pages;

use App\Filament\Resources\DestinationCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDestinationCategories extends ListRecords
{
    protected static string $resource = DestinationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
