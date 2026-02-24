<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LivewireRequestFixer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si es una petición Livewire y viene por GET
        if ($request->isMethod('get') && 
            (Str::contains($request->path(), 'livewire/update') || 
             Str::contains($request->path(), 'livewire/message'))) {
            
            // Cambiar el método a POST para que Livewire lo procese correctamente
            $request->setMethod('POST');
        }

        return $next($request);
    }
}
