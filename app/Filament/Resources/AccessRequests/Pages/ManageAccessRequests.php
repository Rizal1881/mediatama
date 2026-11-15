<?php

namespace App\Filament\Resources\AccessRequests\Pages;

use App\Filament\Resources\AccessRequests\AccessRequestResource;
use Filament\Resources\Pages\ManageRecords;

class ManageAccessRequests extends ManageRecords
{
    protected static string $resource = AccessRequestResource::class;
}