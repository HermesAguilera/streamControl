<?php

namespace App\Filament\Concerns;

trait RedirectsToResourceIndex
{
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
