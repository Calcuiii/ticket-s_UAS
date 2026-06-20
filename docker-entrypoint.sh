#!/bin/bash
set -e

echo "=== Ticket Service Starting ==="

# Copy .env jika belum ada
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate app key jika belum ada
php artisan key:generate --force

# Tunggu database siap
echo "Menunggu database..."
until php artisan migrate:status > /dev/null 2>&1; do
    sleep 2
done

# Jalankan migration
php artisan migrate --force

echo "=== Memulai Redis Subscriber di background ==="
php artisan redis:subscribe &

echo "=== Memulai Laravel Server ==="
php artisan serve --host=0.0.0.0 --port=8000
