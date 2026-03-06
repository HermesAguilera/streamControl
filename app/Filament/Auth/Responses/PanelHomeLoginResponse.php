<?php

namespace App\Filament\Auth\Responses;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class PanelHomeLoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $panel = Filament::getCurrentPanel();
        $target = $panel?->getUrl() ?? '/';

        return redirect()->to($target);
    }
}
