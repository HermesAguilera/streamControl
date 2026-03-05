#!/usr/bin/env bash
set -e

# Small helper to extract host and port from DATABASE_URL if present
if [ -n "$DATABASE_URL" ]; then
  # expected format: postgres://user:pass@host:port/dbname
  host=$(echo "$DATABASE_URL" | sed -E 's#.*@([^:/]+):([0-9]+).*$#\1#')
  port=$(echo "$DATABASE_URL" | sed -E 's#.*@([^:/]+):([0-9]+).*$#\2#')
else
  host=${DB_HOST:-127.0.0.1}
  port=${DB_PORT:-5432}
fi

echo "Waiting for database at $host:$port ..."
retries=0
max_retries=30
until nc -z "$host" "$port"; do
  retries=$((retries+1))
  if [ "$retries" -ge "$max_retries" ]; then
    echo "Timed out waiting for database after $retries attempts. Continuing anyway."
    break
  fi
  echo "Database not ready, retrying ($retries/$max_retries)..."
  sleep 2
done

# Ensure APP_KEY
if [ -z "$(php artisan key:generate --show 2>/dev/null || true)" ]; then
  php artisan key:generate --force || true
fi

# Run migrations (don't fail the container startup if migrations fail)
php artisan migrate --force || echo "migrate failed or DB not ready"

# Publish filament assets (safe to run)
php artisan filament:assets --no-interaction || true

# Seed admin user (firstOrCreate inside seeder prevents duplicates)
php artisan db:seed --class=AdminUserSeeder || echo "seeder failed or DB not ready"

# Cache optimizations (ignore failures if DB driver causes issues)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting server..."
php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
