#!/bin/bash
# Safe test DB migration — clears config cache first
# Root cause: config cache forces production DB even with --env=testing

set -e
cd ~/felagi_app

echo "→ Clearing config cache..."
php artisan config:clear > /dev/null 2>&1
php artisan cache:clear > /dev/null 2>&1

echo "→ Running migration with explicit env var..."
APP_ENV=testing php artisan migrate:fresh --force "$@"

echo "→ Verifying test DB..."
APP_ENV=testing php artisan tinker --execute='
echo "  DB: " . config("database.connections.mysql.database") . "\n";
' 2>&1 | grep -v Unsuccessful

echo "✅ Test migration complete"
