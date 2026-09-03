#!/usr/bin/env bash

# ==============================================================================
# SIRANI - ORACLE CLOUD ALWAYS FREE 1-CLICK DEPLOYMENT SCRIPT
# SMK NEGERI 1 AIR NANINGAN
# ==============================================================================

set -e

# Warna Output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${BLUE}==============================================================${NC}"
echo -e "${GREEN}  MEMULAI INSTALASI SIRANI DI ORACLE CLOUD ALWAYS FREE${NC}"
echo -e "${BLUE}==============================================================${NC}"

# 1. Pastikan dijalankan sebagai root / sudo
if [ "$EUID" -ne 0 ]; then
  echo -e "${RED}Error: Skrip ini harus dijalankan dengan hak akses root / sudo.${NC}"
  exit 1
fi

# 2. Update Sistem & Buka Firewall iptables
echo -e "\n${YELLOW}[1/7] Memperbarui indeks paket dan membuka port 80 & 443 di firewall...${NC}"
apt update || true
apt --fix-broken install -y || true
apt install -y software-properties-common curl git unzip

# Buka port 80 dan 443 di iptables (jika ada)
iptables -I INPUT 6 -m state --state NEW -p tcp --dport 80 -j ACCEPT 2>/dev/null || true
iptables -I INPUT 6 -m state --state NEW -p tcp --dport 443 -j ACCEPT 2>/dev/null || true

# 3. Tambahkan Repositori PHP 8.3 & Pasang LEMP Stack
echo -e "\n${YELLOW}[2/7] Menginstal Nginx, PHP 8.3, dan Ekstensi yang Dibutuhkan...${NC}"
add-apt-repository ppa:ondrej/php -y || true
apt update || true

DEBIAN_FRONTEND=noninteractive apt install -y nginx supervisor \
  php8.3-fpm php8.3-cli php8.3-common php8.3-sqlite3 \
  php8.3-curl php8.3-mbstring php8.3-xml php8.3-zip \
  php8.3-bcmath php8.3-intl php8.3-gd


systemctl enable nginx
systemctl enable php8.3-fpm
systemctl start nginx
systemctl start php8.3-fpm

# 4. Pasang Composer
echo -e "\n${YELLOW}[3/7] Menginstal Composer...${NC}"
if ! command -v composer &> /dev/null; then
  curl -sS https://getcomposer.org/installer | php
  mv composer.phar /usr/local/bin/composer
  chmod +x /usr/local/bin/composer
fi

# 5. Siapkan Direktori Web SIRANI
APP_DIR="/var/www/sirani"
echo -e "\n${YELLOW}[4/7] Menyiapkan direktori aplikasi di ${APP_DIR}...${NC}"

mkdir -p ${APP_DIR}

if [ ! -f "${APP_DIR}/artisan" ]; then
  echo -e "Menyalin file project ke ${APP_DIR}..."
  # Jika dieksekusi dari dalam folder project lokal
  if [ -f "./artisan" ]; then
    cp -r ./* ${APP_DIR}/
    cp -r ./.[!.]* ${APP_DIR}/ 2>/dev/null || true
  fi
fi

cd ${APP_DIR}

# 6. Install Dependensi & Konfigurasi Lingkungan
echo -e "\n${YELLOW}[5/7] Menginstal pustaka Composer dan migrasi database...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction

if [ ! -f "${APP_DIR}/.env" ]; then
  cp ${APP_DIR}/.env.example ${APP_DIR}/.env
  php artisan key:generate --force
fi

# Pastikan SQLite database siap
mkdir -p ${APP_DIR}/database
if [ -f "${APP_DIR}/database/sirani_master.sqlite" ] && [ ! -s "${APP_DIR}/database/database.sqlite" ]; then
  echo "Memulihkan database master SIRANI (151 siswa & 24 guru)..."
  cp ${APP_DIR}/database/sirani_master.sqlite ${APP_DIR}/database/database.sqlite
else
  touch ${APP_DIR}/database/database.sqlite
fi

php artisan migrate --force
php artisan storage:link || true

# Optimasi Cache Produksi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Atur Izin Berkas www-data
chown -R www-data:www-data ${APP_DIR}
chmod -R 775 ${APP_DIR}/storage
chmod -R 775 ${APP_DIR}/bootstrap/cache
chmod 664 ${APP_DIR}/database/database.sqlite
chmod 775 ${APP_DIR}/database

# 7. Konfigurasi Nginx Server Block
echo -e "\n${YELLOW}[6/7] Menyiapkan Nginx Server Block...${NC}"

NGINX_CONF="/etc/nginx/sites-available/sirani"
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
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -sf ${NGINX_CONF} /etc/nginx/sites-enabled/sirani
rm -f /etc/nginx/sites-enabled/default

nginx -t
systemctl reload nginx

# 8. Konfigurasi Cron Scheduler
echo -e "\n${YELLOW}[7/7] Mengonfigurasi Crontab Scheduler Laravel...${NC}"
CRON_JOB="* * * * * cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1"
(crontab -u www-data -l 2>/dev/null | grep -Fv "schedule:run"; echo "$CRON_JOB") | crontab -u www-data -

echo -e "\n${GREEN}==============================================================${NC}"
echo -e "${GREEN}  INSTALASI SELESAI DENGAN SUKSES!${NC}"
echo -e "${GREEN}==============================================================${NC}"
echo -e "Server SIRANI kini aktif dan dapat diakses melalui IP Publik VPS Anda."
echo -e "Untuk memasang domain dan SSL gratis (HTTPS), jalankan:"
echo -e "  sudo apt install -y certbot python3-certbot-nginx"
echo -e "  sudo certbot --nginx -d domainsekolahanda.sch.id\n"
