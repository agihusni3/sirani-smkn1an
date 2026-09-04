#!/usr/bin/env bash

# ==============================================================================
# SIRANI - SETUP AUTO-SYNC UBUNTU SERVER (AUTO PULL & CACHE CLEAR)
# SMK NEGERI 1 AIR NANINGAN
# ==============================================================================
# Jalankan skrip ini CUKUP SEKALI di Ubuntu Server dengan:
#   sudo bash scripts/setup_auto_sync.sh
#
# Setelah ini, Anda TIDAK PERLU lagi membuka terminal Ubuntu!
# Setiap kali Anda mengetik 'push' di laptop, server Ubuntu akan otomatis
# menarik pembaruan GitHub dalam waktu < 1 menit secara hening di latar belakang.
# ==============================================================================

set -e

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

APP_DIR="/var/www/sirani"
[ ! -d "$APP_DIR" ] && APP_DIR="$(pwd)"

REAL_USER="${SUDO_USER:-$USER}"

echo -e "${BLUE}==============================================================${NC}"
echo -e "${GREEN}  MEMASANG AUTO-SYNC OTOMATIS SIRANI DI UBUNTU SERVER${NC}"
echo -e "${BLUE}==============================================================${NC}"
echo -e "User Pemilik: ${YELLOW}${REAL_USER}${NC}"
echo -e "Direktori   : ${YELLOW}${APP_DIR}${NC}"

# 1. Pastikan safe.directory di Git agar tidak ditolak karena beda user
echo -e "\n${YELLOW}[1/4] Mengonfigurasi izin Git safe.directory...${NC}"
git config --global --add safe.directory "${APP_DIR}" 2>/dev/null || true
if [ -n "$SUDO_USER" ]; then
    sudo -u "$SUDO_USER" git config --global --add safe.directory "${APP_DIR}" 2>/dev/null || true
fi

# 2. Perbaiki kepemilikan file & folder .git
echo -e "${YELLOW}[2/4] Memperbaiki kepemilikan file & hak akses folder...${NC}"
chown -R "${REAL_USER}:www-data" "${APP_DIR}" 2>/dev/null || true
chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache" 2>/dev/null || true
chmod -R u+rw "${APP_DIR}/.git" 2>/dev/null || true
if [ -f "${APP_DIR}/database/database.sqlite" ]; then
    chmod 664 "${APP_DIR}/database/database.sqlite" 2>/dev/null || true
    chmod 775 "${APP_DIR}/database" 2>/dev/null || true
fi

# 3. Buat file script sync otomatis di /usr/local/bin/sirani-sync.sh
echo -e "${YELLOW}[3/4] Membuat daemon pemantau auto-sync (/usr/local/bin/sirani-sync.sh)...${NC}"
SYNC_SCRIPT="/usr/local/bin/sirani-sync.sh"

cat > "${SYNC_SCRIPT}" << 'EOF'
#!/usr/bin/env bash
APP_DIR="/var/www/sirani"
[ ! -d "$APP_DIR" ] && exit 0

cd "$APP_DIR" || exit 0
export HOME="/root"

# Pastikan git safe directory
git config --global --add safe.directory "$APP_DIR" >/dev/null 2>&1 || true

# Cek pembaruan dari remote origin/main secara diam-diam
git fetch origin main >/dev/null 2>&1 || exit 0

LOCAL=$(git rev-parse HEAD 2>/dev/null)
REMOTE=$(git rev-parse origin/main 2>/dev/null)

if [ -n "$LOCAL" ] && [ -n "$REMOTE" ] && [ "$LOCAL" != "$REMOTE" ]; then
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Perubahan terdeteksi ($LOCAL -> $REMOTE). Menjalankan update otomatis..." >> /var/log/sirani-autoupdate.log
    bash "$APP_DIR/scripts/update_ubuntu.sh" >> /var/log/sirani-autoupdate.log 2>&1
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Auto-update SIRANI sukses diselesaikan!" >> /var/log/sirani-autoupdate.log
fi
EOF

chmod +x "${SYNC_SCRIPT}"

# 4. Pasang ke /etc/cron.d/sirani-sync (berjalan setiap 1 menit tanpa campur tangan user)
echo -e "${YELLOW}[4/4] Memasang jadwal cron sistem (/etc/cron.d/sirani-sync)...${NC}"
echo "* * * * * root /usr/local/bin/sirani-sync.sh >/dev/null 2>&1" > /etc/cron.d/sirani-sync
chmod 644 /etc/cron.d/sirani-sync

# Jalankan 1x sekarang untuk menarik pembaruan yang tertunda
echo -e "\n${YELLOW}Menjalankan sinkronisasi pertama kali...${NC}"
/usr/local/bin/sirani-sync.sh || true

echo -e "\n${GREEN}==============================================================${NC}"
echo -e "${GREEN}✔ AUTO-SYNC BERHASIL DIAKTIFKAN SECARA PERMANEN!${NC}"
echo -e "${BLUE}Server Ubuntu sekarang otomatis memantau GitHub setiap 60 detik.${NC}"
echo -e "${YELLOW}Mulai sekarang Anda TIDAK PERLU membuka terminal Ubuntu lagi.${NC}"
echo -e "Cukup ketik 'push' di laptop, server otomatis update sendiri!"
echo -e "${GREEN}==============================================================${NC}"
