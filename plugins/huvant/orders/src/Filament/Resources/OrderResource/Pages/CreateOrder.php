<?php

namespace Huvant\Orders\Filament\Resources\OrderResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Huvant\Orders\Filament\Resources\OrderResource;
use Huvant\Orders\Support\Orders;
use Illuminate\Database\Eloquent\Model;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return Orders::open($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
