<?php

namespace App\Filament\SuperAdmin\Resources\TenantResource\Pages;

use App\Filament\SuperAdmin\Resources\TenantResource;
use App\Models\Empresa;
use App\Models\Tenant;
use App\Support\Tenancy\TenantConnectionManager;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Tenant $tenant */
        $tenant = $this->record;
        $empresa = $tenant->empresa;

        return array_merge($data, [
            'company_name' => $tenant->name,
            'database_name' => $tenant->db_database,
            'direccion' => $empresa?->direccion,
            'telefono' => $empresa?->telefono,
            'pais_id' => $empresa?->pais_id,
            'departamento_id' => $empresa?->departamento_id,
            'municipio_id' => $empresa?->municipio_id,
            'admin_name' => $tenant->bootstrap_admin_name,
            'admin_email' => $tenant->bootstrap_admin_email,
            'admin_password' => $tenant->bootstrap_admin_password,
        ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Tenant $tenant */
        $tenant = $record;
        $oldAdminEmail = (string) ($tenant->bootstrap_admin_email ?? '');

        DB::transaction(function () use ($tenant, $data): void {
            $empresa = $tenant->empresa;

            if (! $empresa) {
                $empresa = Empresa::query()->create([
                    'nombre' => (string) ($data['company_name'] ?? $tenant->name),
                    'pais_id' => $data['pais_id'] ?? null,
                    'departamento_id' => $data['departamento_id'] ?? null,
                    'municipio_id' => $data['municipio_id'],
                    'direccion' => (string) ($data['direccion'] ?? ''),
                    'telefono' => $data['telefono'] ?? null,
                    'rtn' => 'AUTO'.str_pad((string) $tenant->id, 16, '0', STR_PAD_LEFT),
                ]);

                $tenant->empresa_id = $empresa->id;
            }

            $empresa->fill([
                'nombre' => (string) ($data['company_name'] ?? $tenant->name),
                'pais_id' => $data['pais_id'] ?? null,
                'departamento_id' => $data['departamento_id'] ?? null,
                'municipio_id' => $data['municipio_id'],
                'direccion' => (string) ($data['direccion'] ?? $empresa->direccion),
                'telefono' => $data['telefono'] ?? null,
            ])->save();

            $tenant->fill([
                'name' => (string) ($data['company_name'] ?? $tenant->name),
                'slug' => (string) ($data['slug'] ?? $tenant->slug),
                'db_driver' => (string) ($data['db_driver'] ?? $tenant->db_driver),
                'db_host' => (string) ($data['db_host'] ?? $tenant->db_host),
                'db_port' => (int) ($data['db_port'] ?? $tenant->db_port),
                'db_database' => (string) (($data['database_name'] ?? '') !== '' ? $data['database_name'] : $tenant->db_database),
                'bootstrap_admin_name' => (string) ($data['admin_name'] ?? $tenant->bootstrap_admin_name),
                'bootstrap_admin_email' => mb_strtolower(trim((string) ($data['admin_email'] ?? $tenant->bootstrap_admin_email))),
                'bootstrap_admin_password' => (string) ($data['admin_password'] ?? $tenant->bootstrap_admin_password),
            ])->save();
        });

        $this->syncTenantAdminCredentials($tenant->refresh(), $oldAdminEmail);

        return $tenant->refresh();
    }

    private function syncTenantAdminCredentials(Tenant $tenant, string $oldAdminEmail): void
    {
        /** @var TenantConnectionManager $manager */
        $manager = app(TenantConnectionManager::class);
        $manager->connect($tenant);

        try {
            if (! Schema::connection('tenant')->hasTable('users')) {
                return;
            }

            $query = DB::connection('tenant')->table('users');

            $normalizedOldEmail = mb_strtolower(trim($oldAdminEmail));
            $newEmail = mb_strtolower(trim((string) ($tenant->bootstrap_admin_email ?? '')));
            $newName = (string) ($tenant->bootstrap_admin_name ?: 'Administrador');
            $newPassword = (string) ($tenant->bootstrap_admin_password ?? '');

            $user = null;

            if ($normalizedOldEmail !== '') {
                $user = $query->where('email', $normalizedOldEmail)->first();
            }

            if (! $user && $newEmail !== '') {
                $user = $query->where('email', $newEmail)->first();
            }

            if (! $user) {
                $user = $query->orderBy('id')->first();
            }

            if (! $user) {
                return;
            }

            $update = [
                'name' => $newName,
                'email' => $newEmail,
                'updated_at' => now(),
            ];

            if ($newPassword !== '') {
                $update['password'] = Hash::make($newPassword);
            }

            $query->where('id', $user->id)->update($update);
        } finally {
            $manager->disconnect();
        }
    }
}
