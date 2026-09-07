#!/usr/bin/env bash

# ==============================================================================
# SIRANI - SCRIPT UPDATE CEPAT UBUNTU SERVER (AUTO-SYNC DARI GITHUB)
# SMK NEGERI 1 AIR NANINGAN
# ==============================================================================

# Warna Output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}==============================================================${NC}"
echo -e "${GREEN}  MEMULAI UPDATE SIRANI KE VERSI TERBARU DARI GITHUB${NC}"
echo -e "${BLUE}==============================================================${NC}"

# Tentukan direktori aplikasi
if [ -d "/var/www/sirani" ]; then
    APP_DIR="/var/www/sirani"
else
    APP_DIR="$(pwd)"
fi

cd "$APP_DIR"
echo -e "${YELLOW}Direktori:${NC} $APP_DIR"

# 1. Konfigurasi Git Safe Directory & Tarik Pembaruan
echo -e "\n${YELLOW}[1/4] Menarik pembaruan terbaru dari GitHub (origin/main)...${NC}"
git config --global --add safe.directory "$APP_DIR" 2>/dev/null || true

# Fetch dan reset hard ke origin/main agar bebas konflik dan selalu bersih
git fetch origin main
git checkout -B main origin/main 2>/dev/null || git checkout main 2>/dev/null || true
git reset --hard origin/main

# 2. Jalankan migrasi database & sinkronisasi data
echo -e "\n${YELLOW}[2/4] Memeriksa migrasi database & sinkronisasi...${NC}"
php artisan migrate --force || true
php artisan sirani:sync-siswa 2>/dev/null || true
php artisan sirani:bersihkan-notif 2>/dev/null || true

# 3. Bersihkan & Segarkan Cache Laravel
echo -e "\n${YELLOW}[3/4] Mengosongkan cache lama & mengoptimasi cache produksi...${NC}"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Perbaiki hak akses file untuk webserver www-data
echo -e "\n${YELLOW}[4/4] Memastikan izin akses file webserver...${NC}"
LOGIN_USER="${SUDO_USER:-$USER}"
if [ "$EUID" -eq 0 ]; then
    chown -R "${LOGIN_USER}:www-data" "$APP_DIR" 2>/dev/null || true
    chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true
    chmod -R u+rw "$APP_DIR/.git" 2>/dev/null || true
    if [ -f "$APP_DIR/database/database.sqlite" ]; then
        chmod 664 "$APP_DIR/database/database.sqlite"
        chmod 775 "$APP_DIR/database"
    fi
    systemctl reload nginx 2>/dev/null || true
    systemctl reload php8.3-fpm 2>/dev/null || true
    echo -e "${GREEN}✔ Layanan webserver direload.${NC}"
else
    sudo chown -R "${LOGIN_USER}:www-data" "$APP_DIR" 2>/dev/null || true
    sudo chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true
    sudo chmod -R u+rw "$APP_DIR/.git" 2>/dev/null || true
    if [ -f "$APP_DIR/database/database.sqlite" ]; then
        sudo chmod 664 "$APP_DIR/database/database.sqlite" 2>/dev/null || true
        sudo chmod 775 "$APP_DIR/database" 2>/dev/null || true
    fi
    sudo systemctl reload nginx 2>/dev/null || true
    sudo systemctl reload php8.3-fpm 2>/dev/null || true
    echo -e "${GREEN}✔ Hak akses webserver www-data diperbarui.${NC}"
fi

echo -e "\n${GREEN}==============================================================${NC}"
echo -e "${GREEN}✔ UPDATE SELESAI! SIRANI berhasil diperbarui ke commit terbaru.${NC}"
echo -e "${BLUE}Commit saat ini:${NC} $(git log -1 --oneline)"
echo -e "${GREEN}==============================================================${NC}"
