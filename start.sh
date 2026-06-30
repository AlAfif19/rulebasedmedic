#!/usr/bin/env bash
set -e

APP_PORT="${APP_PORT:-8000}"
VITE_HOST="${VITE_HOST:-127.0.0.1}"
VITE_PORT="${VITE_PORT:-5173}"
DB_NAME="${DB_DATABASE:-rulebasedmedic}"

echo "Menyiapkan RuleBasedMedic pada http://127.0.0.1:${APP_PORT}"

if [ ! -f .env ]; then
  cp .env.example .env
fi

MYSQL_BIN=""
if command -v mysql >/dev/null 2>&1; then
  MYSQL_BIN="mysql"
elif [ -x "/c/xampp/mysql/bin/mysql.exe" ]; then
  MYSQL_BIN="/c/xampp/mysql/bin/mysql.exe"
elif [ -x "/c/laragon/bin/mysql/mysql-8.0/bin/mysql.exe" ]; then
  MYSQL_BIN="/c/laragon/bin/mysql/mysql-8.0/bin/mysql.exe"
fi

if [ -n "$MYSQL_BIN" ]; then
  echo "Membuat database MySQL jika belum ada"
  "$MYSQL_BIN" -uroot -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" || true
else
  echo "MySQL client tidak ditemukan. Pastikan MySQL aktif dan database ${DB_NAME} tersedia."
fi

if [ ! -d vendor ]; then
  if ! command -v composer >/dev/null 2>&1; then
    echo "Composer belum terinstal. Instal Composer terlebih dahulu, lalu jalankan start.sh lagi."
    exit 1
  fi
  composer install
fi

if ! grep -q "APP_KEY=base64" .env; then
  php artisan key:generate --force
fi

if [ ! -d node_modules ]; then
  if command -v npm >/dev/null 2>&1; then
    npm install
  else
    echo "NPM tidak ditemukan. Tampilan tetap memiliki CDN Tailwind fallback, tetapi build asset lokal dilewati."
  fi
fi

if command -v npm >/dev/null 2>&1; then
  npm run build || true
fi

php artisan migrate --force
php artisan db:seed --force
mkdir -p storage/app storage/logs bootstrap/cache
php artisan optimize:clear

if [ -f storage/app/laravel-server.pid ]; then
  OLD_PID=$(cat storage/app/laravel-server.pid)
  if ps -p "$OLD_PID" >/dev/null 2>&1; then
    echo "Server lama masih berjalan dengan PID $OLD_PID. Jalankan stop.sh terlebih dahulu."
    exit 1
  fi
fi

php artisan serve --host=127.0.0.1 --port="${APP_PORT}" > storage/logs/server.log 2>&1 &
echo $! > storage/app/laravel-server.pid

if command -v npm >/dev/null 2>&1; then
  npm run dev -- --host "${VITE_HOST}" --port "${VITE_PORT}" > storage/logs/vite.log 2>&1 &
  echo $! > storage/app/vite-server.pid
fi

echo "Laravel development server started: http://127.0.0.1:${APP_PORT}"
echo "Vite development server started: http://${VITE_HOST}:${VITE_PORT}"
echo "Application URL: http://127.0.0.1:${APP_PORT}"
echo "Gunakan akun lokal yang dibuat dari seeder atau data development pribadi."
echo "Log Laravel: storage/logs/server.log"
echo "Log Vite: storage/logs/vite.log"
