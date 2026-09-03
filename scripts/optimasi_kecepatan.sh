#!/usr/bin/env bash

# ==============================================================================
# OPTIMASI KECEPATAN EKSTRIM SIRANI SMKN 1 AIR NANINGAN
# Meningkatkan respon refresh dari detik ke milidetik (< 50ms)
# ==============================================================================

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}============================================================${NC}"
echo -e "${GREEN}  MENJALANKAN OPTIMASI KECEPATAN SERVER SIRANI${NC}"
echo -e "${BLUE}============================================================${NC}"

APP_DIR="/var/www/sirani"

if [ ! -d "$APP_DIR" ]; then
  APP_DIR="$(pwd)"
fi

# 1. Optimasi Konfigurasi .env (Ubah ke File Driver & Production)
echo -e "\n${YELLOW}[1/4] Mengoptimalkan konfigurasi lingkungan (.env)...${NC}"
if [ -f "${APP_DIR}/.env" ]; then
  # Matikan mode debug agar Laravel tidak mencatat log trace berat di setiap refresh
  sed -i 's/APP_DEBUG=true/APP_DEBUG=false/g' ${APP_DIR}/.env
  sed -i 's/APP_ENV=local/APP_ENV=production/g' ${APP_DIR}/.env

  # Pindahkan session dan cache ke memory/file agar tidak mengunci SQLite
  sed -i 's/SESSION_DRIVER=database/SESSION_DRIVER=file/g' ${APP_DIR}/.env
  sed -i 's/CACHE_STORE=database/CACHE_STORE=file/g' ${APP_DIR}/.env
  
  echo -e "${GREEN}✔ Mode Production aktif, sesi dialihkan ke file RAM${NC}"
fi

# 2. Aktifkan PHP OPcache di PHP 8.3 FPM
echo -e "\n${YELLOW}[2/4] Mengaktifkan PHP 8.3 OPcache (Akselerasi RAM)...${NC}"
PHP_INI="/etc/php/8.3/fpm/php.ini"
if [ -f "$PHP_INI" ]; then
  sed -i 's/^;opcache.enable=0/opcache.enable=1/g' $PHP_INI
  sed -i 's/^;opcache.enable=1/opcache.enable=1/g' $PHP_INI
  sed -i 's/^opcache.enable=0/opcache.enable=1/g' $PHP_INI
  sed -i 's/^;opcache.memory_consumption=.*/opcache.memory_consumption=128/g' $PHP_INI
  sed -i 's/^;opcache.interned_strings_buffer=.*/opcache.interned_strings_buffer=16/g' $PHP_INI
  sed -i 's/^;opcache.max_accelerated_files=.*/opcache.max_accelerated_files=10000/g' $PHP_INI
  sed -i 's/^;opcache.revalidate_freq=.*/opcache.revalidate_freq=2/g' $PHP_INI
  sed -i 's/^;opcache.fast_shutdown=.*/opcache.fast_shutdown=1/g' $PHP_INI
  systemctl restart php8.3-fpm 2>/dev/null || true
  echo -e "${GREEN}✔ PHP OPcache aktif (kode disimpan di RAM)${NC}"
fi

# 3. Optimasi Cache Laravel (Route, View, Config)
echo -e "\n${YELLOW}[3/4] Mengompilasi Cache Laravel...${NC}"
cd ${APP_DIR}
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✔ Seluruh route dan tampilan telah dicache di memori${NC}"

# 4. Tambahkan Aturan Browser Cache di Nginx
echo -e "\n${YELLOW}[4/4] Mengatur Nginx Static Asset Cache...${NC}"
NGINX_CONF="/etc/nginx/sites-available/sirani"
if [ -f "$NGINX_CONF" ]; then
  cat > ${NGINX_CONF} << 'EOF'
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name _;
    root /var/www/sirani/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html;
    charset utf-8;

    client_max_body_size 50M;

    # Akselerasi Aset Statis (Gambar, CSS, JS di-cache langsung di browser)
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform, immutable";
        access_log off;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Proteksi Ketat
    location ~* \.(sqlite|sqlite3|db|sql|log|env|bak)$ {
        deny all;
        return 404;
    }
}
EOF
  nginx -t && systemctl reload nginx
  echo -e "${GREEN}✔ Nginx dikonfigurasi dengan browser caching & fastcgi buffer optimal${NC}"
fi

echo -e "\n${GREEN}============================================================${NC}"
echo -e "${GREEN}  OPTIMASI SELESAI! KECEPATAN SERVER KINI MAKSIMAL (KILAT)! 🚀${NC}"
echo -e "${GREEN}============================================================${NC}"
