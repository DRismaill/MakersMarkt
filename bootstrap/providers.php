<?php

$providers = [
    App\Providers\AppServiceProvider::class,
];

// Register Filament admin panel provider only when Filament is installed.
// This prevents a hard fatal error during artisan commands when the
// Filament package (and its PanelProvider class) is not available.
if (class_exists(\Filament\PanelProvider::class) && file_exists(app_path('Providers/Filament/AdminPanelProvider.php'))) {
    $providers[] = App\Providers\Filament\AdminPanelProvider::class;
}

$providers[] = App\Providers\FortifyServiceProvider::class;

return $providers;
