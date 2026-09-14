# Aturan Kerja Agen (SIRANI Project)

## Perintah "push"
Ketika pengguna memberikan perintah **"push"**:
1. Lakukan commit semua perubahan yang belum tersimpan dengan pesan deskriptif.
2. Jalankan `./push.sh` atau dorong serentak ke branch aktif dan branch `main`:
   ```bash
   ./push.sh "<pesan commit>"
   ```
3. Pastikan kode terdorong ke repositori **GitHub** (`origin/feature/website-smk` dan `origin/main`).
4. Pastikan webhook auto-deploy ke **Server Ubuntu** (`https://smkn1airnaningan.sch.id/api/deploy-webhook`) terpanggil dan server berhasil terupdate.
