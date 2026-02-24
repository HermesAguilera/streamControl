<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plataforma_id')->constrained('plataformas')->cascadeOnDelete();
            $table->string('proveedor', 120);
            $table->string('correo', 255);
            $table->string('contrasena', 255);
            $table->date('fecha_inicio');
            $table->date('fecha_corte');
            $table->timestamps();

            $table->unique(['plataforma_id', 'correo'], 'cuentas_plataforma_correo_unique');
        });

        Schema::table('perfiles', function (Blueprint $table) {
            $table->foreignId('cuenta_id')
                ->nullable()
                ->after('plataforma_id')
                ->constrained('cuentas')
                ->nullOnDelete();
        });

        $rows = DB::table('perfiles')
            ->selectRaw('MIN(id) as id')
            ->selectRaw('LOWER(TRIM(correo_cuenta)) as correo_normalizado')
            ->whereNotNull('plataforma_id')
            ->whereNotNull('correo_cuenta')
            ->where('correo_cuenta', '!=', '')
            ->groupBy('plataforma_id')
            ->groupByRaw('LOWER(TRIM(correo_cuenta))')
            ->get();

        foreach ($rows as $row) {
            $perfil = DB::table('perfiles')->where('id', $row->id)->first();

            if (! $perfil) {
                continue;
            }

            $correo = trim((string) ($row->correo_normalizado ?? ''));

            if ($correo === '') {
                continue;
            }

            $cuentaId = DB::table('cuentas')->insertGetId([
                'plataforma_id' => $perfil->plataforma_id,
                'proveedor' => trim((string) ($perfil->proveedor_nombre ?: 'Sin proveedor')),
                'correo' => $correo,
                'contrasena' => (string) ($perfil->contrasena_cuenta ?: ''),
                'fecha_inicio' => $perfil->fecha_inicio ?: now()->toDateString(),
                'fecha_corte' => $perfil->fecha_corte ?: now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('perfiles')
                ->where('plataforma_id', $perfil->plataforma_id)
                ->whereRaw('LOWER(TRIM(correo_cuenta)) = ?', [$correo])
                ->update(['cuenta_id' => $cuentaId]);
        }
    }

    public function down(): void
    {
        Schema::table('perfiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cuenta_id');
        });

        Schema::dropIfExists('cuentas');
    }
};
