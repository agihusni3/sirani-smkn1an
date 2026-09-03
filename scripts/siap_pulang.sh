#!/usr/bin/env bash

# ==============================================================================
# SKRIP FINAL SEBELUM PULANG (SMKN 1 AIR NANINGAN)
# 1. Matikan Sleep/Suspend (Server Melek 24 Jam)
# 2. Pasang & Aktifkan AnyDesk Auto-Allow (Remote dari Rumah)
# 3. Optimasi Kecepatan Server (Respon Kilat)
# ==============================================================================

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}============================================================${NC}"
echo -e "${GREEN}    PERSIAPAN SERVER SIRANI AMAN UNTUK DITINGGAL PULANG    ${NC}"
echo -e "${BLUE}============================================================${NC}"

# 1. Matikan Sleep / Suspend / Hibernate
echo -e "\n${YELLOW}[1/3] Mengamankan Server agar TIDAK SLEEP / TIDUR...${NC}"
systemctl mask sleep.target suspend.target hibernate.target hybrid-sleep.target 2>/dev/null || true
echo -e "${GREEN}✔ Server aman, akan tetap melek dan aktif 24 jam non-stop!${NC}"

# 2. Pasang & Siapkan AnyDesk
echo -e "\n${YELLOW}[2/3] Menyiapkan AnyDesk Auto-Allow...${NC}"
bash "$(dirname "$0")/install_anydesk.sh"

# 3. Jalankan Optimasi Kecepatan
echo -e "\n${YELLOW}[3/3] Menjalankan Optimasi Kecepatan Respon...${NC}"
bash "$(dirname "$0")/optimasi_kecepatan.sh"

echo -e "\n${GREEN}============================================================${NC}"
echo -e "${GREEN}  SEMUA SELESAI 100%! SERVER SUDAH RESMI SIAP DITINGGAL!    ${NC}"
echo -e "${GREEN}============================================================${NC}"
echo -e ""
echo -e "Silakan foto ID AnyDesk yang muncul di layar desktop atau terminal."
echo -e "Monitor boleh dimatikan, CPU biarkan tetap menyala. Hati-hati di jalan! 🚗"
