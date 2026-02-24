<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('plataformas', 'perfiles_por_cuenta')) {
            Schema::table('plataformas', function (Blueprint $table) {
                $table->unsignedTinyInteger('perfiles_por_cuenta')->default(5)->after('activa');
            });
        }

        if (!$this->hasIndex('perfiles', 'perfiles_plataforma_correo_nombre_unique')) {
            if (!$this->hasIndex('perfiles', 'perfiles_plataforma_id_idx_tmp')) {
                Schema::table('perfiles', function (Blueprint $table) {
                    $table->index('plataforma_id', 'perfiles_plataforma_id_idx_tmp');
                });
            }

            if ($this->hasIndex('perfiles', 'perfiles_plataforma_id_nombre_perfil_unique')) {
                Schema::table('perfiles', function (Blueprint $table) {
                    $table->dropUnique('perfiles_plataforma_id_nombre_perfil_unique');
                });
            }

            Schema::table('perfiles', function (Blueprint $table) {
                $table->unique(['plataforma_id', 'correo_cuenta', 'nombre_perfil'], 'perfiles_plataforma_correo_nombre_unique');
            });
        }

        if ($this->hasIndex('perfiles', 'perfiles_plataforma_id_idx_tmp')) {
            Schema::table('perfiles', function (Blueprint $table) {
                $table->dropIndex('perfiles_plataforma_id_idx_tmp');
            });
        }
    }

    public function down(): void
    {
        if (!$this->hasIndex('perfiles', 'perfiles_plataforma_id_nombre_perfil_unique')) {
            if (!$this->hasIndex('perfiles', 'perfiles_plataforma_id_idx_tmp')) {
                Schema::table('perfiles', function (Blueprint $table) {
                    $table->index('plataforma_id', 'perfiles_plataforma_id_idx_tmp');
                });
            }

            if ($this->hasIndex('perfiles', 'perfiles_plataforma_correo_nombre_unique')) {
                Schema::table('perfiles', function (Blueprint $table) {
                    $table->dropUnique('perfiles_plataforma_correo_nombre_unique');
                });
            }

            Schema::table('perfiles', function (Blueprint $table) {
                $table->unique(['plataforma_id', 'nombre_perfil']);
            });
        }

        if ($this->hasIndex('perfiles', 'perfiles_plataforma_id_idx_tmp')) {
            Schema::table('perfiles', function (Blueprint $table) {
                $table->dropIndex('perfiles_plataforma_id_idx_tmp');
            });
        }

        if (Schema::hasColumn('plataformas', 'perfiles_por_cuenta')) {
            Schema::table('plataformas', function (Blueprint $table) {
                $table->dropColumn('perfiles_por_cuenta');
            });
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) {
            return false;
        }

        $result = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);

        return count($result) > 0;
    }
};
