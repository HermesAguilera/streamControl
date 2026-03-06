<?php

return [
    'central_connection' => env('TENANCY_CENTRAL_CONNECTION', env('DB_CONNECTION', 'mysql')),
    'tenant_database_prefix' => env('TENANCY_DB_PREFIX', 'tenant_'),
    'tenant_migrations_path' => env('TENANCY_MIGRATIONS_PATH', 'database/migrations/tenant'),
    'tenant_default_driver' => env('TENANCY_DB_DRIVER', env('DB_CONNECTION', 'mysql')),
];
