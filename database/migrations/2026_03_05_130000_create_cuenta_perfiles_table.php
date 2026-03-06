<?php

use App\Models\Cuenta;
use App\Models\Perfil;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_perfiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_id')->constrained('cuentas')->cascadeOnDelete();
            $table->unsignedInteger('numero_perfil');
            $table->string('pin', 20)->nullable();
            $table->timestamps();

            $table->unique(['cuenta_id', 'numero_perfil'], 'cuenta_perfiles_cuenta_numero_unique');
        });

        Cuenta::query()
            ->with('plataforma:id,perfiles_por_cuenta')
            ->get(['id', 'plataforma_id'])
            ->each(function (Cuenta $cuenta): void {
                $defaultLimit = (int) ($cuenta->plataforma?->perfiles_por_cuenta ?: 5);

                $existingSlots = Perfil::query()
                    ->where('cuenta_id', $cuenta->id)
                    ->get(['nombre_perfil', 'pin'])
                    ->map(function (Perfil $perfil): array {
                        return [
                            'slot' => (int) $perfil->nombre_perfil,
                            'pin' => $perfil->pin,
                        ];
                    })
                    ->filter(fn (array $row): bool => $row['slot'] > 0)
                    ->groupBy('slot')
                    ->map(fn ($rows) => (string) ($rows->first()['pin'] ?? ''));

                $maxExisting = (int) ($existingSlots->keys()->max() ?: 0);
                $limit = max($defaultLimit, $maxExisting, 1);

                $rows = [];

                for ($slot = 1; $slot <= $limit; $slot++) {
                    $rows[] = [
                        'cuenta_id' => $cuenta->id,
                        'numero_perfil' => $slot,
                        'pin' => $existingSlots->get($slot) ?: null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                DB::table('cuenta_perfiles')->insert($rows);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_perfiles');
    }
};
