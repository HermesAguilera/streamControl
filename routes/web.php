<?php

use Illuminate\Support\Facades\Route;

// Rutas de Filament y otras personalizadas
if (file_exists(base_path('routes/filament.php'))) {
    require base_path('routes/filament.php');
}

Route::redirect('/', '/admin/login');