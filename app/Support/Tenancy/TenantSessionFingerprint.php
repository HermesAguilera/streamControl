<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;

class TenantSessionFingerprint
{
    public static function make(Tenant $tenant, int|string|null $userId, ?string $userAgent): string
    {
        $payload = implode('|', [
            (string) $tenant->id,
            (string) $tenant->slug,
            (string) $tenant->db_database,
            (string) $userId,
            (string) $userAgent,
        ]);

        return hash_hmac('sha256', $payload, (string) config('app.key'));
    }
}
