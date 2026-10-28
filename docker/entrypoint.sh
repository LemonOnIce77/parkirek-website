#!/bin/sh
# =====================================================================
#  Entrypoint container aplikasi Parkirek
#  - menyiapkan permission direktori Laravel
#  - menunggu database siap
#  - menyiapkan aplikasi (package discover, storage link, cache config)
#  - menjalankan migrasi database
#  - menjalankan Nginx + PHP-FPM lewat Supervisor
# =====================================================================
set -e

APP_HOME="${APP_HOME:-/var/www/html}"
cd "$APP_HOME"

echo "=============================================="
echo " Parkirek - Container Startup"
echo "=============================================="

if [ -z "${APP_KEY:-}" ]; then
    echo "[entrypoint] PERINGATAN: APP_KEY kosong. Pastikan file .env berisi APP_KEY yang valid."
fi

echo "[entrypoint] Menyiapkan direktori & permission Laravel..."
mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    storage/logs \
    storage/app/public \
    bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "[entrypoint] Menunggu database siap..."
php -r '
$host = getenv("DB_HOST") ?: "db";
$port = (int) (getenv("DB_PORT") ?: 3306);
$deadline = time() + 60;
while (time() < $deadline) {
    $fp = @fsockopen($host, $port, $errno, $errstr, 2);
    if ($fp) {
        fclose($fp);
        echo "database siap\n";
        exit(0);
    }
    sleep(2);
}
fwrite(STDERR, "database belum siap\n");
exit(1);
' || echo "[entrypoint] database belum siap, migrasi akan dicoba ulang."

echo "[entrypoint] Menjalankan package discovery..."
php artisan package:discover --ansi

echo "[entrypoint] Menautkan storage publik (symlink storage:link)..."
php artisan storage:link --force --ansi || echo "[entrypoint] storage:link dilewati."

echo "[entrypoint] Membersihkan & menyimpan cache konfigurasi..."
php artisan config:clear --ansi
php artisan config:cache --ansi
php artisan route:cache --ansi || echo "[entrypoint] route cache dilewati."
php artisan view:cache --ansi || true

echo "[entrypoint] Menjalankan migrasi database..."
attempt=1
until php artisan migrate --force --ansi; do
    if [ "$attempt" -ge 5 ]; then
        echo "[entrypoint] Migrasi database gagal setelah 5 percobaan."
        exit 1
    fi
    attempt=$((attempt + 1))
    echo "[entrypoint] Migrasi gagal, mencoba lagi (${attempt}/5)..."
    sleep 5
done

echo "[entrypoint] Menjalankan Nginx + PHP-FPM..."
exec "$@"
