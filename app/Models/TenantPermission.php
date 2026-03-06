<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class TenantPermission extends SpatiePermission
{
    protected $connection = 'tenant';

    protected $guard_name = 'tenant_web';
}
