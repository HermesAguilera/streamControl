<?php

namespace App\Filament\Pages;

use App\Support\UserPreferenceState;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class AjustesGenerales extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = 'Ajustes';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'ajustes';

    protected static string $view = 'filament.pages.ajustes-generales';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public function mount(): void
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'preference')) {
            $user->preference()->firstOrCreate(
                ['user_id' => $user->id],
                UserPreferenceState::defaults(),
            );
        }

        $this->form->fill(UserPreferenceState::forUser($user));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Apariencia')
                    ->schema([
                        Forms\Components\Select::make('theme_mode')
                            ->label('Tema')
                            ->required()
                            ->options([
                                'system' => 'Sistema',
                                'light' => 'Claro',
                                'dark' => 'Oscuro',
                            ]),
                        Forms\Components\Toggle::make('colorize_accounts')
                            ->label('Mostrar cuentas por color')
                            ->helperText('Resalta visualmente las cuentas para identificar agrupaciones más rápido.'),
                    ])
                    ->columns(1),
                Forms\Components\Section::make('Comodidad')
                    ->schema([
                        Forms\Components\Toggle::make('dense_interface')
                            ->label('Interfaz compacta')
                            ->helperText('Reduce espacios verticales para mostrar más información en pantalla.'),
                        Forms\Components\Toggle::make('reduced_motion')
                            ->label('Reducir animaciones')
                            ->helperText('Disminuye transiciones para una experiencia más estable y cómoda.'),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $user = auth()->user();

        if (! $user || ! method_exists($user, 'preference')) {
            Notification::make()
                ->title('Este usuario no tiene preferencias configurables en este panel.')
                ->warning()
                ->send();

            return;
        }

        $state = $this->data ?? [];
        $defaults = UserPreferenceState::defaults();

        $payload = [
            'theme_mode' => (string) ($state['theme_mode'] ?? $defaults['theme_mode']),
            'colorize_accounts' => (bool) ($state['colorize_accounts'] ?? false),
            'dense_interface' => (bool) ($state['dense_interface'] ?? false),
            'reduced_motion' => (bool) ($state['reduced_motion'] ?? false),
        ];

        $user->preference()->updateOrCreate(
            ['user_id' => $user->id],
            $payload,
        );

        $user->unsetRelation('preference');

        Notification::make()
            ->title('Ajustes guardados correctamente.')
            ->success()
            ->send();
    }
}
