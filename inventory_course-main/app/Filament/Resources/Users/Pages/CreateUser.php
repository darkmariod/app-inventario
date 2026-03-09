<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        if ($this->record->roles->isEmpty()) {
            $guestRole = Role::firstOrCreate(['name' => 'guest']);
            $this->record->assignRole($guestRole);
        }
    }
}
