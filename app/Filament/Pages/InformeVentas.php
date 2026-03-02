<?php

namespace App\Filament\Pages;

use App\Models\Perfil;
use App\Models\Plataforma;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class InformeVentas extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = 'Informe de ventas';

    protected static ?string $navigationLabel = 'Informe de ventas';

    protected static ?string $navigationGroup = 'Streaming';

    protected static ?int $navigationSort = 99;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string $view = 'filament.pages.informe-ventas';

    public ?array $data = [];

    public array $report = [];

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public function mount(): void
    {
        $savedSettings = $this->getSavedReportSettings();
        $ticketBase = 150;
        $periodo = 'mensual';
        $moneda = 'L';

        if (is_array($savedSettings)) {
            $ticketBase = max((float) ($savedSettings['ticket_promedio_base'] ?? $ticketBase), 0);
            $periodo = (string) ($savedSettings['periodo'] ?? $periodo);
            $moneda = (string) ($savedSettings['moneda'] ?? $moneda);
        }

        $this->form->fill([
            'periodo' => $periodo,
            'fecha_referencia' => now()->toDateString(),
            'ticket_promedio' => $ticketBase,
            'moneda' => $moneda,
            'tickets_por_plataforma' => $this->buildPlatformTicketsState(
                $ticketBase,
                $savedSettings['tickets_por_plataforma'] ?? [],
            ),
        ]);

        $this->generateReport();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Parámetros del informe')
                    ->schema([
                        Forms\Components\Select::make('periodo')
                            ->label('Periodo')
                            ->required()
                            ->options([
                                'semanal' => 'Semanal',
                                'quincenal' => 'Quincenal',
                                'mensual' => 'Mensual',
                                'anual' => 'Anual',
                            ]),
                        Forms\Components\DatePicker::make('fecha_referencia')
                            ->label('Fecha de referencia')
                            ->required(),
                        Forms\Components\TextInput::make('ticket_promedio')
                            ->label('Ticket promedio por cliente')
                            ->numeric()
                            ->minValue(0)
                            ->default(150)
                            ->required(),
                        Forms\Components\Select::make('moneda')
                            ->label('Moneda')
                            ->required()
                            ->default('L')
                            ->options([
                                'L' => 'Lempira (L)',
                                '$' => 'Dólar ($)',
                            ]),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Ticket promedio por plataforma')
                    ->description('Personaliza el ticket por plataforma para un cálculo económico más preciso.')
                    ->schema([
                        Forms\Components\Repeater::make('tickets_por_plataforma')
                            ->label('')
                            ->deletable(false)
                            ->addable(false)
                            ->reorderable(false)
                            ->schema([
                                Forms\Components\Hidden::make('plataforma_id')->required(),
                                Forms\Components\TextInput::make('plataforma_nombre')
                                    ->label('Plataforma')
                                    ->disabled()
                                    ->dehydrated(),
                                Forms\Components\TextInput::make('ticket_promedio')
                                    ->label('Ticket promedio')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),
                            ])
                            ->columns(2),
                    ]),
            ])
            ->statePath('data');
    }

    public function generateReport(): void
    {
        $periodo = (string) ($this->data['periodo'] ?? 'mensual');
        $fechaReferencia = (string) ($this->data['fecha_referencia'] ?? now()->toDateString());
        $ticketPromedio = (float) ($this->data['ticket_promedio'] ?? 150);
        $moneda = (string) ($this->data['moneda'] ?? 'L');
        $ticketsPorPlataforma = $this->normalizePlatformTickets(
            $this->data['tickets_por_plataforma'] ?? [],
            $ticketPromedio,
        );

        $this->persistReportSettings($periodo, $moneda, $ticketPromedio, $ticketsPorPlataforma);

        $this->data['tickets_por_plataforma'] = $this->buildPlatformTicketsState($ticketPromedio, $ticketsPorPlataforma);

        $this->report = $this->buildReport($periodo, $fechaReferencia, $ticketPromedio, $moneda, $ticketsPorPlataforma);
    }

    public function downloadPdf()
    {
        if (empty($this->report)) {
            $this->generateReport();
        }

        $report = $this->sanitizeUtf8Data($this->report);

        $pdf = Pdf::loadView('pdf.informe-ventas', [
            'report' => $report,
        ])->setPaper('a4', 'portrait');

        $fileName = 'informe-ventas-' . now()->format('Ymd_His') . '.pdf';

        return response()->streamDownload(function () use ($pdf): void {
            echo $pdf->output();
        }, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    protected function buildReport(
        string $periodo,
        string $fechaReferencia,
        float $ticketPromedio,
        string $moneda,
        array $ticketsPorPlataforma,
    ): array
    {
        $range = $this->resolveRange($periodo, $fechaReferencia);
        $start = $range['start'];
        $end = $range['end'];
        $ticketPromedio = max($ticketPromedio, 0);

        $baseClientes = Perfil::query()
            ->whereNotNull('cliente_nombre')
            ->where('cliente_nombre', '!=', '');

        $vendidos = (clone $baseClientes)
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->count();

        $dejados = (clone $baseClientes)
            ->whereNotNull('fecha_caducidad_cuenta')
            ->whereDate('fecha_caducidad_cuenta', '>=', $start->toDateString())
            ->whereDate('fecha_caducidad_cuenta', '<=', $end->toDateString())
            ->whereDate('fecha_caducidad_cuenta', '<=', now()->toDateString())
            ->count();

        $activos = (clone $baseClientes)
            ->whereNotNull('fecha_caducidad_cuenta')
            ->whereDate('fecha_caducidad_cuenta', '>=', now()->toDateString())
            ->count();

        $neto = $vendidos - $dejados;

        $retencion = $vendidos > 0
            ? max(min((($vendidos - $dejados) / $vendidos) * 100, 100), -100)
            : 0;

        $porPlataforma = $this->buildPlatformRows($start, $end, $ticketsPorPlataforma, $ticketPromedio);
        $ingresosVendidos = (float) $porPlataforma->sum('ingresos_vendidos');
        $ingresosPerdidos = (float) $porPlataforma->sum('ingresos_perdidos');
        $ingresosNetos = $ingresosVendidos - $ingresosPerdidos;

        return [
            'periodo' => $periodo,
            'periodo_label' => $range['label'],
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'ticket_promedio' => $ticketPromedio,
            'moneda' => $moneda,
            'tickets_por_plataforma' => $ticketsPorPlataforma,
            'generated_at' => now()->format('d/m/Y H:i'),
            'generated_by' => $this->sanitizeUtf8String(auth()->user()?->name ?? 'Sistema'),
            'resumen' => [
                'vendidos' => $vendidos,
                'dejados' => $dejados,
                'neto' => $neto,
                'activos' => $activos,
                'retencion' => round($retencion, 1),
                'ingresos_vendidos' => round($ingresosVendidos, 2),
                'ingresos_perdidos' => round($ingresosPerdidos, 2),
                'ingresos_netos' => round($ingresosNetos, 2),
            ],
            'plataformas' => $porPlataforma->values()->all(),
        ];
    }

    protected function buildPlatformRows(Carbon $start, Carbon $end, array $ticketsPorPlataforma, float $ticketBase): Collection
    {
        return Plataforma::query()
            ->withCount([
                'perfiles as vendidos_count' => function ($query) use ($start, $end): void {
                    $query
                        ->whereNotNull('cliente_nombre')
                        ->where('cliente_nombre', '!=', '')
                        ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()]);
                },
                'perfiles as dejados_count' => function ($query) use ($start, $end): void {
                    $query
                        ->whereNotNull('cliente_nombre')
                        ->where('cliente_nombre', '!=', '')
                        ->whereNotNull('fecha_caducidad_cuenta')
                        ->whereDate('fecha_caducidad_cuenta', '>=', $start->toDateString())
                        ->whereDate('fecha_caducidad_cuenta', '<=', $end->toDateString())
                        ->whereDate('fecha_caducidad_cuenta', '<=', now()->toDateString());
                },
            ])
            ->get()
            ->map(function (Plataforma $plataforma) use ($ticketsPorPlataforma, $ticketBase): array {
                $vendidos = (int) ($plataforma->vendidos_count ?? 0);
                $dejados = (int) ($plataforma->dejados_count ?? 0);
                $ticketPlataforma = (float) ($ticketsPorPlataforma[$plataforma->id] ?? $ticketBase);

                return [
                    'plataforma_id' => $plataforma->id,
                    'plataforma' => $this->sanitizeUtf8String($plataforma->nombre),
                    'ticket_promedio' => round($ticketPlataforma, 2),
                    'vendidos' => $vendidos,
                    'dejados' => $dejados,
                    'neto' => $vendidos - $dejados,
                    'ingresos_vendidos' => round($vendidos * $ticketPlataforma, 2),
                    'ingresos_perdidos' => round($dejados * $ticketPlataforma, 2),
                    'ingresos_netos' => round(($vendidos - $dejados) * $ticketPlataforma, 2),
                ];
            })
            ->sortByDesc(fn (array $row): int => $row['vendidos']);
    }

    protected function defaultPlatformTickets(float $ticketBase): array
    {
        return Plataforma::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre'])
            ->map(fn (Plataforma $plataforma): array => [
                'plataforma_id' => $plataforma->id,
                'plataforma_nombre' => $this->sanitizeUtf8String($plataforma->nombre),
                'ticket_promedio' => $ticketBase,
            ])
            ->values()
            ->all();
    }

    protected function normalizePlatformTickets(array $rows, float $ticketBase): array
    {
        $normalized = [];

        foreach ($rows as $key => $row) {
            if (is_numeric($key) && is_numeric($row)) {
                $normalized[(int) $key] = max((float) $row, 0);

                continue;
            }

            if (! is_array($row)) {
                continue;
            }

            $plataformaId = (int) ($row['plataforma_id'] ?? 0);

            if ($plataformaId <= 0) {
                continue;
            }

            $normalized[$plataformaId] = max((float) ($row['ticket_promedio'] ?? $ticketBase), 0);
        }

        foreach ($this->defaultPlatformTickets($ticketBase) as $item) {
            $platformId = (int) $item['plataforma_id'];

            if (! array_key_exists($platformId, $normalized)) {
                $normalized[$platformId] = (float) $item['ticket_promedio'];
            }
        }

        return $normalized;
    }

    protected function buildPlatformTicketsState(float $ticketBase, array $savedTickets): array
    {
        $normalized = $this->normalizePlatformTickets($savedTickets, $ticketBase);

        return collect($this->defaultPlatformTickets($ticketBase))
            ->map(function (array $item) use ($normalized): array {
                $platformId = (int) $item['plataforma_id'];

                return [
                    'plataforma_id' => $platformId,
                    'plataforma_nombre' => $item['plataforma_nombre'],
                    'ticket_promedio' => (float) ($normalized[$platformId] ?? $item['ticket_promedio']),
                ];
            })
            ->values()
            ->all();
    }

    protected function getSavedReportSettings(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        $settings = $user->preference()->first()?->report_sales_settings;

        return is_array($settings) ? $settings : [];
    }

    protected function persistReportSettings(string $periodo, string $moneda, float $ticketBase, array $ticketsPorPlataforma): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $user->preference()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'report_sales_settings' => [
                    'periodo' => $periodo,
                    'moneda' => $moneda,
                    'ticket_promedio_base' => $ticketBase,
                    'tickets_por_plataforma' => $ticketsPorPlataforma,
                ],
            ],
        );

        $user->unsetRelation('preference');
    }

    protected function sanitizeUtf8String(?string $value): string
    {
        $value ??= '';

        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        $cleaned = @iconv('UTF-8', 'UTF-8//IGNORE', $value);

        if (is_string($cleaned) && $cleaned !== '') {
            return $cleaned;
        }

        return mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
    }

    protected function sanitizeUtf8Data(mixed $value): mixed
    {
        if (is_string($value)) {
            return $this->sanitizeUtf8String($value);
        }

        if (is_array($value)) {
            $clean = [];

            foreach ($value as $key => $item) {
                $cleanKey = is_string($key) ? $this->sanitizeUtf8String($key) : $key;
                $clean[$cleanKey] = $this->sanitizeUtf8Data($item);
            }

            return $clean;
        }

        return $value;
    }

    protected function resolveRange(string $periodo, string $fechaReferencia): array
    {
        $reference = Carbon::parse($fechaReferencia);

        return match ($periodo) {
            'semanal' => [
                'start' => $reference->copy()->startOfWeek(Carbon::MONDAY),
                'end' => $reference->copy()->endOfWeek(Carbon::SUNDAY),
                'label' => 'Semanal',
            ],
            'quincenal' => $reference->day <= 15
                ? [
                    'start' => $reference->copy()->startOfMonth(),
                    'end' => $reference->copy()->startOfMonth()->addDays(14)->endOfDay(),
                    'label' => 'Quincenal (1-15)',
                ]
                : [
                    'start' => $reference->copy()->startOfMonth()->addDays(15)->startOfDay(),
                    'end' => $reference->copy()->endOfMonth(),
                    'label' => 'Quincenal (16-fin de mes)',
                ],
            'anual' => [
                'start' => $reference->copy()->startOfYear(),
                'end' => $reference->copy()->endOfYear(),
                'label' => 'Anual',
            ],
            default => [
                'start' => $reference->copy()->startOfMonth(),
                'end' => $reference->copy()->endOfMonth(),
                'label' => 'Mensual',
            ],
        };
    }
}
