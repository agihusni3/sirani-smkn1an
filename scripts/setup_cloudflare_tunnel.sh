#!/usr/bin/env bash

# ==============================================================================
# SETUP CLOUDFLARE TUNNEL OTOMATIS UNTUK SIRANI
# Jalankan SEKALI, setelah itu tunnel aktif otomatis setiap PC dinyalakan
# ==============================================================================

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}============================================================${NC}"
echo -e "${GREEN}  MENYIAPKAN CLOUDFLARE TUNNEL OTOMATIS UNTUK SIRANI${NC}"
echo -e "${BLUE}============================================================${NC}"

# 1. Download & Install cloudflared
echo -e "\n${YELLOW}[1/3] Mengunduh dan memasang Cloudflare Tunnel...${NC}"
if ! command -v cloudflared &> /dev/null; then
  curl -L --output /tmp/cloudflared.deb \
    https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb
  dpkg -i /tmp/cloudflared.deb
  rm -f /tmp/cloudflared.deb
  echo -e "${GREEN}✔ cloudflared berhasil dipasang${NC}"
else
  echo -e "${GREEN}✔ cloudflared sudah terpasang sebelumnya${NC}"
fi

# 2. Buat skrip wrapper yang menjalankan tunnel dan menyimpan URL
echo -e "\n${YELLOW}[2/3] Membuat layanan otomatis tunnel SIRANI...${NC}"

# Skrip untuk menjalankan tunnel dan menyimpan link publik ke file
cat > /usr/local/bin/sirani-tunnel.sh << 'EOF'
#!/bin/bash
LOG_FILE="/var/log/sirani-tunnel.log"
URL_FILE="/var/www/sirani/public/tunnel-url.txt"

echo "Memulai Cloudflare Tunnel SIRANI..." > ${LOG_FILE}
echo "Waktu mulai: $(date)" >> ${LOG_FILE}

# Jalankan cloudflared dan tangkap URL yang dihasilkan
cloudflared tunnel --url http://localhost --logfile ${LOG_FILE} 2>&1 &
CF_PID=$!

# Tunggu hingga URL muncul di log (maks 30 detik)
for i in {1..30}; do
  sleep 1
  URL=$(grep -o 'https://[a-zA-Z0-9\-]*\.trycloudflare\.com' ${LOG_FILE} | head -1)
  if [ -n "$URL" ]; then
    echo "${URL}" > ${URL_FILE}
    echo "============================================" >> ${LOG_FILE}
    echo "SIRANI PUBLIK AKTIF DI: ${URL}" >> ${LOG_FILE}
    echo "============================================" >> ${LOG_FILE}
    echo "Link SIRANI: ${URL}" > /etc/motd
    echo "" >> /etc/motd
    echo "Buka ${URL} dari HP/laptop mana saja!" >> /etc/motd
    
    # Tulis juga ke Desktop agar user tinggal buka file di Desktop
    for USER_DESKTOP in /home/*/Desktop; do
      if [ -d "$USER_DESKTOP" ]; then
        echo "=== SIRANI SMKN 1 AIR NANINGAN ONLINE ===" > "${USER_DESKTOP}/LINK_SIRANI_ONLINE.txt"
        echo "" >> "${USER_DESKTOP}/LINK_SIRANI_ONLINE.txt"
        echo "Link Akses Guru & Orang Tua:" >> "${USER_DESKTOP}/LINK_SIRANI_ONLINE.txt"
        echo "${URL}" >> "${USER_DESKTOP}/LINK_SIRANI_ONLINE.txt"
        echo "" >> "${USER_DESKTOP}/LINK_SIRANI_ONLINE.txt"
        echo "Status: AKTIF (Otomatis menyala setiap PC hidup)" >> "${USER_DESKTOP}/LINK_SIRANI_ONLINE.txt"
      fi
    done
    break
  fi
done


wait ${CF_PID}
EOF

chmod +x /usr/local/bin/sirani-tunnel.sh

# 3. Daftarkan sebagai layanan sistem (otomatis aktif saat PC dinyalakan)
echo -e "\n${YELLOW}[3/3] Mendaftarkan layanan otomatis sirani-tunnel...${NC}"

cat > /etc/systemd/system/sirani-tunnel.service << 'EOF'
[Unit]
Description=Cloudflare Tunnel SIRANI SMKN 1 Air Naningan
After=network-online.target nginx.service
Wants=network-online.target

[Service]
Type=simple
User=root
ExecStart=/usr/local/bin/sirani-tunnel.sh
Restart=always
RestartSec=15
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
EOF

systemctl daemon-reload
systemctl enable sirani-tunnel.service
systemctl restart sirani-tunnel.service

echo -e "\n${GREEN}============================================================${NC}"
echo -e "${GREEN}  CLOUDFLARE TUNNEL SUDAH AKTIF DAN SIAP!${NC}"
echo -e "${GREEN}============================================================${NC}"
echo -e ""
echo -e "Menunggu link publik tersedia (tunggu 15 detik)..."
sleep 15

# Tampilkan URL yang tersimpan
URL_FILE="/var/www/sirani/public/tunnel-url.txt"
if [ -f "${URL_FILE}" ]; then
  PUBLIK_URL=$(cat ${URL_FILE})
  echo -e ""
  echo -e "${GREEN}╔══════════════════════════════════════════════════════════╗${NC}"
  echo -e "${GREEN}║  LINK PUBLIK SIRANI:                                     ║${NC}"
  echo -e "${GREEN}║  ${PUBLIK_URL}${NC}"
  echo -e "${GREEN}╚══════════════════════════════════════════════════════════╝${NC}"
  echo -e ""
  echo -e "Link tersebut bisa langsung dibagikan ke guru dan orang tua!"
  echo -e "Tunnel akan otomatis aktif setiap PC Server dinyalakan."
else
  echo -e "${YELLOW}Link sedang dalam proses... Tunggu 30 detik lalu cek dengan:${NC}"
  echo -e "  cat /var/www/sirani/public/tunnel-url.txt"
fi

echo -e ""
echo -e "Untuk melihat link kapan saja, ketik:"
echo -e "  cat /var/www/sirani/public/tunnel-url.txt"
