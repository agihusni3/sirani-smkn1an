#!/usr/bin/env bash

# ==============================================================================
# PEMASANG ANYDESK OTOMATIS UNTUK UBUNTU 22.04 LTS (SMKN 1 AIR NANINGAN)
# Mengatasi dependensi libpangox secara otomatis
# ==============================================================================

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}============================================================${NC}"
echo -e "${GREEN}      MEMASANG ANYDESK DI PC SERVER SMKN 1 AIR NANINGAN    ${NC}"
echo -e "${BLUE}============================================================${NC}"

# 0. Perbaiki paket sistem yang rusak (seperti konflik versi Wine lama)
echo -e "\n${YELLOW}[1/3] Memperbaiki dependensi paket sistem...${NC}"
apt --fix-broken install -y || true
apt remove --purge -y winehq-stable wine-stable wine-stable-amd64 wine-stable-i386 2>/dev/null || true
apt autoremove -y 2>/dev/null || true

# 1. Pasang dependensi libpangox
echo -e "\n${YELLOW}[2/3] Mengunduh & memasang pustaka libpangox...${NC}"
curl -fsSL --retry 3 -o /tmp/libpangox.deb \
  http://ftp.us.debian.org/debian/pool/main/p/pangox-compat/libpangox-1.0-0_0.0.2-5.1_amd64.deb
apt install -y /tmp/libpangox.deb
rm -f /tmp/libpangox.deb
echo -e "${GREEN}✔ libpangox berhasil dipasang${NC}"

# 2. Pasang AnyDesk
echo -e "\n${YELLOW}[3/3] Memasang AnyDesk...${NC}"

# Cek apakah file anydesk sudah diunduh user di Downloads
DOWNLOADED_ANYDESK=$(ls /home/*/Downloads/anydesk*amd64.deb 2>/dev/null | head -1 || true)

if [ -n "$DOWNLOADED_ANYDESK" ] && [ -f "$DOWNLOADED_ANYDESK" ]; then
  echo -e "Menggunakan file AnyDesk yang sudah ada di Downloads: $DOWNLOADED_ANYDESK"
  apt install -y "$DOWNLOADED_ANYDESK"
else
  echo -e "Mengunduh paket resmi AnyDesk..."
  curl -fsSL --retry 3 -o /tmp/anydesk.deb \
    https://download.anydesk.com/linux/anydesk_6.3.2-1_amd64.deb
  apt install -y /tmp/anydesk.deb
  rm -f /tmp/anydesk.deb
fi

# 3. Konfigurasi AnyDesk Auto-Allow (Unattended Access)
echo -e "\n${YELLOW}[4/4] Mengaktifkan Auto-Allow (Akses Otomatis Tanpa Konfirmasi)...${NC}"
systemctl enable anydesk 2>/dev/null || true
systemctl restart anydesk 2>/dev/null || true
sleep 2

# Atur password unattended access: sirani123
PASSWORD_ANYDESK="sirani123"
echo "${PASSWORD_ANYDESK}" | anydesk --set-password 2>/dev/null || true

# Ambil ID AnyDesk
ANYDESK_ID=$(anydesk --get-id 2>/dev/null || true)

# Tulis data remote ke Desktop agar user tinggal baca
for USER_DESKTOP in /home/*/Desktop; do
  if [ -d "$USER_DESKTOP" ]; then
    cat > "${USER_DESKTOP}/KODE_REMOTE_ANYDESK.txt" << EOF
====================================================
        DATA REMOTE ANYDESK PC SERVER SIRANI        
====================================================
ID AnyDesk Server : ${ANYDESK_ID}
Password Remote   : ${PASSWORD_ANYDESK}
Status            : AUTO-ALLOW AKTIF (Langsung connect)
====================================================
Dari rumah, cukup buka AnyDesk di HP/Laptop:
1. Ketik ID: ${ANYDESK_ID}
2. Ketik Password: ${PASSWORD_ANYDESK}
Layar server langsung terbuka tanpa perlu orang klik Accept di sekolah!
EOF
  fi
done

echo -e "\n${GREEN}============================================================${NC}"
echo -e "${GREEN}  ANYDESK AUTO-ALLOW BERHASIL DIAKTIFKAN! 🎉                 ${NC}"
echo -e "${GREEN}============================================================${NC}"
echo -e ""
echo -e "ID AnyDesk Server : ${YELLOW}${ANYDESK_ID}${NC}"
echo -e "Password Remote   : ${YELLOW}${PASSWORD_ANYDESK}${NC}"
echo -e ""
echo -e "Catatan ini juga sudah disimpan di Layar Desktop Anda: KODE_REMOTE_ANYDESK.txt"

