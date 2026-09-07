#!/usr/bin/env bash

# ==============================================================================
# SIRANI - 1-CLICK PUSH GITHUB & AUTO-UPDATE SERVER
# SMK NEGERI 1 AIR NANINGAN
# ==============================================================================
# Cara Penggunaan:
#   ./push.sh                    (akan memakai pesan commit otomatis)
#   ./push.sh "pesan commit Anda"
# ==============================================================================

set -e

# Warna Terminal
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
RED='\033[0;31m'
BOLD='\033[1m'
NC='\033[0m'

echo -e "${BLUE}╔══════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║${BOLD}${GREEN}   SIRANI - AUTO PUSH GITHUB & SINKRONISASI SERVER OTOMATIS  ${NC}${BLUE}║${NC}"
echo -e "${BLUE}╚══════════════════════════════════════════════════════════════╝${NC}"

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"

# 1. Cek Branch Saat Ini
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
echo -e "\n${CYAN}[1/4] Branch saat ini:${NC} ${BOLD}${CURRENT_BRANCH}${NC}"

# 2. Cek & Commit Perubahan Lokal Jika Ada
COMMIT_MSG="$1"
if [ -z "$COMMIT_MSG" ]; then
    COMMIT_MSG="update: sinkronisasi sistem SIRANI ($(date '+%Y-%m-%d %H:%M'))"
fi

STATUS_OUTPUT=$(git status --porcelain)
if [ -n "$STATUS_OUTPUT" ]; then
    echo -e "${YELLOW}[2/4] Menyimpan perubahan lokal...${NC}"
    git add -A
    git commit -m "$COMMIT_MSG"
    echo -e "${GREEN}✔ Perubahan berhasil di-commit: \"${COMMIT_MSG}\"${NC}"
else
    echo -e "${GREEN}[2/4] Tidak ada perubahan lokal yang belum di-commit.${NC}"
fi

# 3. Push ke GitHub (Sinkronkan branch saat ini & main)
echo -e "\n${YELLOW}[3/4] Mendorong pembaruan ke GitHub (origin)...${NC}"

if [ "$CURRENT_BRANCH" != "main" ]; then
    # Push branch aktif terlebih dahulu
    echo -e "Mendorong ke origin/${CURRENT_BRANCH}..."
    git push origin "$CURRENT_BRANCH"

    # Sinkronkan dan push ke branch main agar server produksi selalu dapat commit terbaru
    echo -e "Menyelaraskan dengan branch main..."
    git checkout main
    git merge "$CURRENT_BRANCH" -m "merge: selaraskan $CURRENT_BRANCH ke main" 2>/dev/null || git merge "$CURRENT_BRANCH" --no-edit
    git push origin main
    
    # Kembali ke branch awal
    git checkout "$CURRENT_BRANCH"
else
    # Jika saat ini di branch main
    echo -e "Mendorong ke origin/main..."
    git push origin main

    # Selaraskan juga ke feature/website-smk jika ada
    if git show-ref --verify --quiet refs/heads/feature/website-smk; then
        echo -e "Menyelaraskan ke branch feature/website-smk..."
        git checkout feature/website-smk
        git merge main -m "merge: selaraskan main ke feature/website-smk" 2>/dev/null || git merge main --no-edit
        git push origin feature/website-smk 2>/dev/null || true
        git checkout main
    fi
fi

echo -e "${GREEN}✔ GitHub Repositori BERHASIL DIUPDATE!${NC}"

# 4. Trigger Update Server
echo -e "\n${YELLOW}[4/4] Memperbarui Server Produksi SIRANI...${NC}"

DEPLOY_TOKEN="sirani_smkn1an_secret_deploy_key_2026"
SERVER_TRIGGERED=false

# Cari URL server dari .env atau file tunnel
SERVER_URL=""
if [ -f "$PROJECT_DIR/.env" ]; then
    SERVER_URL=$(grep -E "^SERVER_URL=" "$PROJECT_DIR/.env" | cut -d '=' -f2- | tr -d '"' | tr -d "'")
fi

if [ -z "$SERVER_URL" ] && [ -f "$PROJECT_DIR/public/tunnel-url.txt" ]; then
    SERVER_URL=$(cat "$PROJECT_DIR/public/tunnel-url.txt" 2>/dev/null | head -n 1)
fi

# Jika server URL ditemukan, panggil endpoint webhook secara instan
if [ -n "$SERVER_URL" ]; then
    echo -e "Menghubungi endpoint Webhook Server di: ${CYAN}${SERVER_URL}${NC}..."
    WEBHOOK_URL="${SERVER_URL%/}/api/deploy-webhook?token=${DEPLOY_TOKEN}"
    
    RESPONSE=$(curl -s -m 12 -X POST "$WEBHOOK_URL" 2>/dev/null || curl -s -m 12 "$WEBHOOK_URL" 2>/dev/null || true)
    
    if echo "$RESPONSE" | grep -q "success"; then
        echo -e "${GREEN}✔ SERVER BERHASIL DIUPDATE SECARA INSTAN VIA WEBHOOK!${NC}"
        COMMIT_NAME=$(echo "$RESPONSE" | grep -o '"latest_commit":"[^"]*' | cut -d '"' -f4)
        [ -n "$COMMIT_NAME" ] && echo -e "  Versi Server: ${CYAN}${COMMIT_NAME}${NC}"
        SERVER_TRIGGERED=true
    fi
fi

if [ "$SERVER_TRIGGERED" = false ]; then
    echo -e "${GREEN}✔ Server Ubuntu dipantau otomatis oleh daemon Auto-Sync (/etc/cron.d/sirani-sync).${NC}"
    echo -e "${CYAN}  Pembaruan akan ditarik otomatis oleh server dalam < 60 detik.${NC}"
fi

echo -e "\n${BLUE}══════════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✔ SEMUA SELESAI DENGAN SUKSES!${NC}"
echo -e "  - GitHub: ${BOLD}TERUPDATE${NC} (origin/main & origin/${CURRENT_BRANCH})"
echo -e "  - Server: ${BOLD}TERUPDATE / AUTO-SYNC AKTIF${NC}"
echo -e "${BLUE}══════════════════════════════════════════════════════════════${NC}\n"
