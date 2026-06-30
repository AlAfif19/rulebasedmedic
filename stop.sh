#!/usr/bin/env bash
set -e

stop_pid_file() {
  local file="$1"
  local label="$2"

  if [ -f "$file" ]; then
    PID=$(cat "$file")
    if ps -p "$PID" >/dev/null 2>&1; then
      kill "$PID"
      echo "$label dihentikan. PID: $PID"
    else
      echo "$label PID tersimpan tetapi proses tidak aktif."
    fi
    rm -f "$file"
  else
    echo "$label PID tidak ditemukan."
  fi
}

stop_pid_file "storage/app/laravel-server.pid" "Laravel server"
stop_pid_file "storage/app/vite-server.pid" "Vite server"

if command -v powershell.exe >/dev/null 2>&1; then
  powershell.exe -NoProfile -Command "Get-NetTCPConnection -LocalPort 8000,5173 -ErrorAction SilentlyContinue | Select-Object -ExpandProperty OwningProcess | Sort-Object -Unique | ForEach-Object { Stop-Process -Id \$_ -Force }" || true
fi
