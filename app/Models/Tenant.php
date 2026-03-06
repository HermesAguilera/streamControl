<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function getConnectionName(): ?string
    {
        return (string) config('tenancy.central_connection', env('DB_CONNECTION', 'mysql'));
    }

    protected $fillable = [
        'uuid',
        'empresa_id',
        'name',
        'slug',
        'status',
        'db_driver',
        'db_host',
        'db_port',
        'db_database',
        'db_username',
        'db_password',
        'bootstrap_admin_name',
        'bootstrap_admin_email',
        'bootstrap_admin_password',
        'db_schema',
        'provisioning_error',
        'provisioned_at',
        'created_by',
    ];

    protected $casts = [
        'db_password' => 'encrypted',
        'bootstrap_admin_password' => 'encrypted',
        'provisioned_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $tenant): void {
            if (! $tenant->uuid) {
                $tenant->uuid = (string) Str::uuid();
            }
        });
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
