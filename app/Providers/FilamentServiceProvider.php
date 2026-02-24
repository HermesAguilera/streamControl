use Filament\Facades\Filament;

public function boot(): void
{
    Filament::serving(function () {
        Filament::registerStyles([
            asset('resources/css/filament/admin/theme.css'),
        ]);
    });
}
