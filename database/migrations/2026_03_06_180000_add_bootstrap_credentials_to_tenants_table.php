<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('bootstrap_admin_name')->nullable()->after('db_password');
            $table->string('bootstrap_admin_email')->nullable()->after('bootstrap_admin_name');
            $table->text('bootstrap_admin_password')->nullable()->after('bootstrap_admin_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'bootstrap_admin_name',
                'bootstrap_admin_email',
                'bootstrap_admin_password',
            ]);
        });
    }
};
