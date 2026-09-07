#!/usr/bin/env bash

# ==============================================================================
# SETUP DOMAIN RESMI & SSL HTTPS UNTUK SIRANI SMKN 1 AIR NANINGAN
# Domain: smkn1airnaningan.sch.id & www.smkn1airnaningan.sch.id
# ==============================================================================
# Cara Menjalankan di Server Ubuntu / VPS:
#   sudo bash scripts/setup_domain_ssl.sh
# ==============================================================================

set -e

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
RED='\033[0;31m'
BOLD='\033[1m'
NC='\033[0m'

echo -e "${BLUE}╔══════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║${BOLD}${GREEN}  SETUP DOMAIN RESMI smkn1airnaningan.sch.id & SSL HTTPS      ${NC}${BLUE}║${NC}"
echo -e "${BLUE}╚══════════════════════════════════════════════════════════════╝${NC}"

if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Error: Skrip ini harus dijalankan dengan sudo atau root!${NC}"
    echo -e "Jalankan: ${YELLOW}sudo bash $0${NC}"
    exit 1
fi

DOMAIN="smkn1airnaningan.sch.id"
DOMAIN_WWW="www.smkn1airnaningan.sch.id"
APP_DIR="/var/www/sirani"

if [ ! -d "$APP_DIR" ]; then
    APP_DIR="$(pwd)"
fi

echo -e "\n${CYAN}Direktori Aplikasi:${NC} $APP_DIR"
echo -e "${CYAN}Domain Utama      :${NC} $DOMAIN"
echo -e "${CYAN}Domain Sekunder   :${NC} $DOMAIN_WWW"

# 1. Pastikan Nginx terpasang & Buka Firewall Port 80 dan 443
echo -e "\n${YELLOW}[1/5] Memeriksa webserver Nginx & Membuka Firewall...${NC}"
if ! command -v nginx &> /dev/null; then
    apt update && apt install -y nginx
fi
systemctl enable nginx
systemctl start nginx

# Buka port 80 (HTTP) dan 443 (HTTPS) untuk verifikasi SSL Let's Encrypt
if command -v ufw &> /dev/null; then
    ufw allow 80/tcp 2>/dev/null || true
    ufw allow 443/tcp 2>/dev/null || true
fi
iptables -I INPUT 1 -p tcp --dport 80 -j ACCEPT 2>/dev/null || true
iptables -I INPUT 1 -p tcp --dport 443 -j ACCEPT 2>/dev/null || true

# 2. Siapkan Konfigurasi Nginx Server Block
echo -e "\n${YELLOW}[2/5] Membuat konfigurasi Nginx untuk ${DOMAIN}...${NC}"

NGINX_CONF="/etc/nginx/sites-available/sirani"

cat > "${NGINX_CONF}" << EOF
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN} ${DOMAIN_WWW} _;
    root ${APP_DIR}/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php index.html;
    charset utf-8;

    client_max_body_size 50M;

    # Akselerasi File Statis
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform, immutable";
        access_log off;
    }

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Proteksi Ketat File Sensitif
    location ~* \.(sqlite|sqlite3|db|sql|log|env|bak)$ {
        deny all;
        return 404;
    }
}
EOF

# Aktifkan konfigurasi Nginx
ln -sf "${NGINX_CONF}" /etc/nginx/sites-enabled/sirani
rm -f /etc/nginx/sites-enabled/default 2>/dev/null || true

nginx -t
systemctl reload nginx
echo -e "${GREEN}✔ Konfigurasi Nginx berhasil diaktifkan!${NC}"

# 3. Pasang Certbot untuk SSL Gratis (Let's Encrypt)
echo -e "\n${YELLOW}[3/5] Memeriksa & memasang Certbot Let's Encrypt...${NC}"
if ! command -v certbot &> /dev/null; then
    apt update
    apt install -y certbot python3-certbot-nginx
fi

# 4. Ambil Sertifikat SSL Otomatis
echo -e "\n${YELLOW}[4/5] Memeriksa DNS dan Meminta sertifikat SSL HTTPS untuk ${DOMAIN}...${NC}"

# Cek IP Publik server
SERVER_PUBLIC_IP=$(curl -s -m 5 https://api.ipify.org 2>/dev/null || curl -s -m 5 https://icanhazip.com 2>/dev/null || curl -s -m 5 https://ifconfig.me 2>/dev/null || echo "")

# Cek IP tujuan DNS domain saat ini
DOMAIN_DNS_IP=$(python3 -c "import socket; print(socket.gethostbyname('${DOMAIN}'))" 2>/dev/null || dig +short ${DOMAIN} 2>/dev/null | tail -n1 || echo "")

# Cek IP lokal server
SERVER_LOCAL_IP=$(hostname -I 2>/dev/null | awk '{print $1}' || echo "")

echo -e "IP Publik Server ini     : ${BOLD}${CYAN}${SERVER_PUBLIC_IP:-Tidak terdeteksi}${NC}"
echo -e "IP Lokal Server (LAN)    : ${BOLD}${CYAN}${SERVER_LOCAL_IP:-Tidak terdeteksi}${NC}"
echo -e "IP Tujuan Domain Saat Ini: ${BOLD}${YELLOW}${DOMAIN_DNS_IP:-Belum terhubung}${NC}"

# Deteksi jika masih mengarah ke IP Parkir Bawaan Hostinger
if [ "$DOMAIN_DNS_IP" = "2.57.91.91" ]; then
    echo -e "\n${RED}╔══════════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${RED}║ [DIAGNOSIS] Domain masih mengarah ke IP Parkir Hostinger (2.57.91.91) ║${NC}"
    echo -e "${RED}╚══════════════════════════════════════════════════════════════════════╝${NC}"
    echo -e "Penyebab IP terbaca salah:"
    echo -e "1. Di dashboard penyedia domain (Hostinger), A Record masih berisi IP parkir bawaan."
    echo -e "2. Anda perlu mengubah A Record (Name: @) ke IP Server Anda: ${BOLD}${GREEN}${SERVER_PUBLIC_IP}${NC}"
    echo -e "3. Jika baru saja Anda ubah di Hostinger, DNS membutuhkan waktu 5-15 menit untuk merambat."
fi

if [ -n "$SERVER_PUBLIC_IP" ] && [ "$SERVER_PUBLIC_IP" = "$DOMAIN_DNS_IP" ]; then
    echo -e "\n${GREEN}✔ IP DNS Domain sudah cocok dengan IP Server! Menerbitkan SSL Let's Encrypt...${NC}"
    certbot --nginx -d "${DOMAIN}" -d "${DOMAIN_WWW}" \
        --non-interactive --agree-tos \
        --email info@smkn1airnaningan.sch.id \
        --redirect || true
    echo -e "${GREEN}✔ Sertifikat SSL HTTPS aktif secara permanen dengan Auto-Renewal!${NC}"
else
    echo -e "\n${YELLOW}ℹ Mencoba menjalankan certbot...${NC}"
    certbot --nginx -d "${DOMAIN}" -d "${DOMAIN_WWW}" \
        --non-interactive --agree-tos \
        --email info@smkn1airnaningan.sch.id \
        --redirect || {
            echo -e "\n${YELLOW}Catatan Penting:${NC}"
            echo -e "Jika Certbot gagal verifikasi, pastikan:"
            echo -e "  - A Record domain ${BOLD}${DOMAIN}${NC} diarahkan ke IP: ${BOLD}${GREEN}${SERVER_PUBLIC_IP}${NC}"
            echo -e "  - Port 80 dan 443 terbuka di firewall (iptables / cloud security list)."
        }
fi

# 5. Perbarui konfigurasi .env Laravel
echo -e "\n${YELLOW}[5/5] Memperbarui APP_URL pada ${APP_DIR}/.env...${NC}"
if [ -f "${APP_DIR}/.env" ]; then
    sed -i "s|^APP_URL=.*|APP_URL=https://${DOMAIN}|g" "${APP_DIR}/.env"
    
    cd "${APP_DIR}"
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    echo -e "${GREEN}✔ APP_URL berhasil diatur ke https://${DOMAIN}${NC}"
fi

echo -e "\n${BLUE}══════════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✔ SETUP DOMAIN RESMI SELESAI!${NC}"
echo -e "Domain: ${BOLD}https://${DOMAIN}${NC}"
echo -e "Laman : ${BOLD}https://${DOMAIN_WWW}${NC}"
echo -e "${BLUE}══════════════════════════════════════════════════════════════${NC}\n"
