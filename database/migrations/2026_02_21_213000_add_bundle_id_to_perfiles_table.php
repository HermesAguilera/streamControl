<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('perfiles', 'bundle_id')) {
            Schema::table('perfiles', function (Blueprint $table) {
                $table->uuid('bundle_id')->nullable()->after('plataforma_id');
                $table->index(['plataforma_id', 'bundle_id'], 'perfiles_plataforma_bundle_idx');
            });
        }

        $rows = DB::table('perfiles')
            ->select('id', 'plataforma_id', 'cliente_nombre', 'correo_cuenta')
            ->orderBy('id')
            ->get();

        $bundleMap = [];

        foreach ($rows as $row) {
            $correo = strtolower(trim((string) ($row->correo_cuenta ?? '')));
            $cliente = mb_strtolower(trim((string) ($row->cliente_nombre ?? '')));
            $key = $row->plataforma_id . '|' . $correo . '|' . $cliente;

            if (!isset($bundleMap[$key])) {
                $bundleMap[$key] = (string) Str::uuid();
            }

            DB::table('perfiles')
                ->where('id', $row->id)
                ->update(['bundle_id' => $bundleMap[$key]]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('perfiles', 'bundle_id')) {
            Schema::table('perfiles', function (Blueprint $table) {
                $table->dropIndex('perfiles_plataforma_bundle_idx');
                $table->dropColumn('bundle_id');
            });
        }
    }
};
