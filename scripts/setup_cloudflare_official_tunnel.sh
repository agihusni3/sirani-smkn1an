#!/usr/bin/env bash

# ==============================================================================
# SETUP RESMI CLOUDFLARE TUNNEL (TANPA KARTU KREDIT / ZERO TRUST BILLING)
# Menghubungkan smkn1airnaningan.sch.id langsung ke Server Ubuntu Sekolah
# ==============================================================================

set -e

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
RED='\033[0;31m'
BOLD='\033[1m'
NC='\033[0m'

DOMAIN="smkn1airnaningan.sch.id"
TUNNEL_NAME="sirani-smk"

echo -e "${BLUE}╔══════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║${BOLD}${GREEN}  SETUP RESMI CLOUDFLARE TUNNEL smkn1airnaningan.sch.id       ${NC}${BLUE}║${NC}"
echo -e "${BLUE}╚══════════════════════════════════════════════════════════════╝${NC}"

# 1. Pasang cloudflared jika belum ada
echo -e "\n${YELLOW}[1/4] Memeriksa instalasi cloudflared...${NC}"
if ! command -v cloudflared &> /dev/null; then
    curl -fsSL https://pkg.cloudflare.com/cloudflare-main.gpg | tee /etc/apt/keyrings/cloudflare-main.gpg >/dev/null || true
    echo 'deb [signed-by=/etc/apt/keyrings/cloudflare-main.gpg] https://pkg.cloudflare.com/cloudflared jammy main' | tee /etc/apt/sources.list.d/cloudflared.list
    apt update && apt install -y cloudflared
    echo -e "${GREEN}✔ cloudflared berhasil dipasang.${NC}"
else
    echo -e "${GREEN}✔ cloudflared sudah terpasang.${NC}"
fi

# 2. Periksa Sertifikat Otorisasi (Login)
CERT_PATH="$HOME/.cloudflared/cert.pem"
[ ! -f "$CERT_PATH" ] && CERT_PATH="/root/.cloudflared/cert.pem"

if [ ! -f "$CERT_PATH" ]; then
    echo -e "\n${YELLOW}[2/4] Menghubungkan Server ke Akun Cloudflare Anda...${NC}"
    echo -e "${CYAN}Silakan Buka Link berikut di Browser Anda, lalu klik domain ${BOLD}${DOMAIN}${NC}:"
    cloudflared tunnel login
fi

# Pastikan sertifikat login sudah ada
if [ ! -f "$HOME/.cloudflared/cert.pem" ] && [ ! -f "/root/.cloudflared/cert.pem" ]; then
    echo -e "${RED}Gagal: Sertifikat login Cloudflare belum ditemukan.${NC}"
    exit 1
fi

CERT_FILE="$HOME/.cloudflared/cert.pem"
[ ! -f "$CERT_FILE" ] && CERT_FILE="/root/.cloudflared/cert.pem"

# 3. Buat Tunnel Resmi
echo -e "\n${YELLOW}[3/4] Membuat Tunnel resmi: ${TUNNEL_NAME}...${NC}"
EXISTING_TUNNEL=$(cloudflared tunnel list | grep "${TUNNEL_NAME}" | awk '{print $1}' || echo "")

if [ -z "$EXISTING_TUNNEL" ]; then
    cloudflared tunnel create "${TUNNEL_NAME}"
    TUNNEL_ID=$(cloudflared tunnel list | grep "${TUNNEL_NAME}" | awk '{print $1}')
else
    TUNNEL_ID="$EXISTING_TUNNEL"
    echo -e "Tunnel ${TUNNEL_NAME} (${TUNNEL_ID}) sudah ada."
fi

echo -e "Tunnel ID: ${BOLD}${TUNNEL_ID}${NC}"

# Arahkan DNS Domain ke Tunnel
echo -e "\nMenghubungkan domain ${DOMAIN} ke Tunnel..."
cloudflared tunnel route dns -f "${TUNNEL_NAME}" "${DOMAIN}" || true
cloudflared tunnel route dns -f "${TUNNEL_NAME}" "www.${DOMAIN}" || true

# 4. Siapkan Konfigurasi Layanan Sistem
echo -e "\n${YELLOW}[4/4] Memasang Layanan Tunnel Otomatis (Systemd Service)...${NC}"
mkdir -p /etc/cloudflared

# Salin credentials file
CRED_ORIGIN="$HOME/.cloudflared/${TUNNEL_ID}.json"
[ ! -f "$CRED_ORIGIN" ] && CRED_ORIGIN="/root/.cloudflared/${TUNNEL_ID}.json"
cp -f "$CRED_ORIGIN" "/etc/cloudflared/${TUNNEL_ID}.json" 2>/dev/null || true
cp -f "$CERT_FILE" "/etc/cloudflared/cert.pem" 2>/dev/null || true

cat > /etc/cloudflared/config.yml << EOF
tunnel: ${TUNNEL_ID}
credentials-file: /etc/cloudflared/${TUNNEL_ID}.json

ingress:
  - hostname: ${DOMAIN}
    service: http://localhost:80
  - hostname: www.${DOMAIN}
    service: http://localhost:80
  - service: http_status:404
EOF

# Install & Jalankan sebagai system service
cloudflared service uninstall 2>/dev/null || true
cloudflared service install
systemctl daemon-reload
systemctl enable cloudflared
systemctl restart cloudflared

echo -e "\n${BLUE}══════════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✔ CLOUDFLARE TUNNEL RESMI BERHASIL AKTIF & BERJALAN!${NC}"
echo -e "Domain Utama : ${BOLD}https://${DOMAIN}${NC}"
echo -e "Domain WWW   : ${BOLD}https://www.${DOMAIN}${NC}"
echo -e "Status       : ${BOLD}AKTIF PERMANEN & OTOMATIS BERJALAN SETIAP SERVER HIDUP${NC}"
echo -e "${BLUE}══════════════════════════════════════════════════════════════${NC}\n"
