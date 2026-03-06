<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class TenantRole extends SpatieRole
{
    protected $connection = 'tenant';

    protected $guard_name = 'tenant_web';
}
