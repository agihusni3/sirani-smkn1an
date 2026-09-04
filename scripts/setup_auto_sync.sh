#!/usr/bin/env bash

# ==============================================================================
# SIRANI - SETUP AUTO-SYNC UBUNTU SERVER (AUTO PULL & CACHE CLEAR)
# SMK NEGERI 1 AIR NANINGAN
# ==============================================================================
# Jalankan skrip ini SEKALI di Ubuntu Server dengan: sudo bash scripts/setup_auto_sync.sh
# Setelah ini, server Ubuntu akan otomatis mendeteksi setiap kali ada "git push"
# dari laptop dan langsung mengupdate dirinya sendiri dalam 1 menit!
# ==============================================================================

set -e

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

if [ "$EUID" -ne 0 ]; then
  echo -e "${YELLOW}Skrip ini sebaiknya dijalankan dengan sudo agar bisa mengatur cron sistem.${NC}"
fi

APP_DIR="/var/www/sirani"
if [ ! -d "$APP_DIR" ]; then
    APP_DIR="$(pwd)"
fi

echo -e "${BLUE}==============================================================${NC}"
echo -e "${GREEN}  MEMASANG AUTO-SYNC OTOMATIS SIRANI DI UBUNTU SERVER${NC}"
echo -e "${BLUE}==============================================================${NC}"

# Buat skrip sync di /usr/local/bin/sirani-sync.sh
SYNC_SCRIPT="/usr/local/bin/sirani-sync.sh"

cat > "${SYNC_SCRIPT}" << 'EOF'
#!/usr/bin/env bash
APP_DIR="/var/www/sirani"
[ ! -d "$APP_DIR" ] && exit 0

cd "$APP_DIR"

# Cek apakah ada commit baru di remote origin/main
git fetch origin main >/dev/null 2>&1 || exit 0
LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse origin/main)

if [ "$LOCAL" != "$REMOTE" ]; then
    echo "[$(date)] Deteksi commit baru: $LOCAL -> $REMOTE. Menjalankan update_ubuntu.sh..." >> /var/log/sirani-autoupdate.log
    bash "$APP_DIR/scripts/update_ubuntu.sh" >> /var/log/sirani-autoupdate.log 2>&1
    echo "[$(date)] Auto-update SIRANI selesai!" >> /var/log/sirani-autoupdate.log
fi
EOF

chmod +x "${SYNC_SCRIPT}"

# Pasang ke crontab root agar berjalan setiap menit
CRON_ENTRY="* * * * * ${SYNC_SCRIPT} >/dev/null 2>&1"
(crontab -l 2>/dev/null | grep -Fv "sirani-sync.sh"; echo "$CRON_ENTRY") | crontab -

echo -e "\n${GREEN}✔ AUTO-SYNC BERHASIL DIPASANG!${NC}"
echo -e "${BLUE}Server Ubuntu sekarang otomatis memantau GitHub setiap 1 menit.${NC}"
echo -e "${YELLOW}Setiap kali Anda push dari laptop, Ubuntu akan otomatis mengupdate kodenya!${NC}"
echo -e "${GREEN}==============================================================${NC}"
