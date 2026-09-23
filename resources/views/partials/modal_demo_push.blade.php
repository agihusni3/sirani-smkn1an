{{-- MODAL DEMO BROADCAST PUSH NOTIFIKASI KE SELURUH WALI MURID --}}
<div id="modalDemoPushWaliMurid" class="demo-push-modal-overlay" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(15,23,42,0.72); backdrop-filter:blur(5px); -webkit-backdrop-filter:blur(5px); align-items:center; justify-content:center; padding:16px;">
  <div class="demo-push-modal-card" style="background:var(--bg-2, #ffffff); border:1px solid var(--border-2, #cbd5e1); border-radius:18px; width:100%; max-width:540px; max-height:90vh; overflow-y:auto; box-shadow:0 25px 50px -12px rgba(0,0,0,0.35); position:relative; display:flex; flex-direction:column; animation:modalPopIn .2s cubic-bezier(0.16, 1, 0.3, 1);">
    
    {{-- Header --}}
    <div style="padding:16px 20px; border-bottom:1px solid var(--border, #e2e8f0); display:flex; justify-content:space-between; align-items:center; background:var(--surface, #f8fafc); border-radius:18px 18px 0 0;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:38px; height:38px; border-radius:10px; background:linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); color:#ffffff; display:flex; align-items:center; justify-content:center; font-size:18px; box-shadow:0 4px 10px rgba(124,58,237,0.3);">
          <i class="bi bi-broadcast"></i>
        </div>
        <div>
          <h3 style="margin:0; font-size:15px; font-weight:900; color:var(--text, #0f172a);">
            Demo Notifikasi HP Wali Murid
          </h3>
          <div style="font-size:11.5px; color:var(--text-3, #64748b); margin-top:2px;">
            Uji coba siaran notifikasi nyata ke seluruh perangkat terhubung
          </div>
        </div>
      </div>
      <button type="button" onclick="closeModalDemoPush()" style="border:none; background:transparent; font-size:20px; color:var(--text-3, #94a3b8); cursor:pointer; width:32px; height:32px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; transition:all .15s ease;" onmouseover="this.style.background='rgba(0,0,0,0.06)'" onmouseout="this.style.background='transparent'">
        &times;
      </button>
    </div>

    {{-- Body Content --}}
    <div style="padding:18px 20px; display:flex; flex-direction:column; gap:14px;">
      
      {{-- Live Subscriber Status Banner --}}
      <div style="background:linear-gradient(135deg, rgba(124,58,237,0.08) 0%, rgba(79,70,229,0.04) 100%); border:1.5px solid rgba(124,58,237,0.22); border-radius:12px; padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
        <div>
          <div style="display:flex; align-items:center; gap:6px; font-size:11px; font-weight:800; color:#7c3aed; text-transform:uppercase; letter-spacing:0.5px;">
            <span style="width:8px; height:8px; border-radius:50%; background:#22c55e; display:inline-block; box-shadow:0 0 8px #22c55e; animation:pulseRadar 1.5s infinite;"></span>
            STATUS PERANGKAT TERHUBUNG
          </div>
          <div style="font-size:20px; font-weight:900; color:var(--text, #0f172a); font-family:var(--font-mono, monospace); margin-top:3px;">
            <span id="demoSubscriberCount" class="global-subscriber-count">0</span>
            <span style="font-size:13px; font-weight:600; color:var(--text-2, #475569); font-family:inherit;">HP Wali Murid Aktif</span>
          </div>
        </div>
        <button type="button" onclick="fetchSubscriberCount(true)" class="btn-refresh-sub" style="border:1px solid rgba(124,58,237,0.3); background:#ffffff; color:#7c3aed; padding:6px 12px; border-radius:8px; font-size:11.5px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:5px; transition:all .15s ease;" title="Muat Ulang Jumlah Perangkat">
          <i class="bi bi-arrow-clockwise" id="iconRefreshSub"></i> Refresh
        </button>
      </div>

      {{-- Petunjuk Langkah Demo Singkat --}}
      <div style="background:var(--bg-3, #f8fafc); border:1px solid var(--border-2, #e2e8f0); border-radius:10px; padding:10px 14px; font-size:11.5px; color:var(--text-2, #475569); line-height:1.5;">
        <strong style="color:var(--text, #0f172a); display:flex; align-items:center; gap:5px; margin-bottom:4px;">
          <i class="bi bi-info-circle-fill" style="color:#3b82f6;"></i> Panduan Demo Bersama Wali Murid:
        </strong>
        <ol style="margin:0; padding-left:18px;">
          <li>Minta wali murid membuka portal <strong>smkn1airnaningan.sch.id/cek-presensi</strong> di HP.</li>
          <li>Tekan <strong>ikon lonceng</strong> di pojok kanan atas atau tombol <strong>Izinkan Notifikasi</strong>.</li>
          <li>Saat wali murid mengizinkan, angka di atas akan otomatis bertambah secara real-time.</li>
          <li>Tekan tombol <strong>"Kirim Notifikasi Demo Sekarang"</strong> di bawah!</li>
        </ol>
      </div>

      {{-- Form Fields --}}
      <form id="formDemoPush" onsubmit="sendDemoPushNotification(event)">
        <div style="margin-bottom:12px;">
          <label style="display:block; font-size:12px; font-weight:800; color:var(--text, #0f172a); margin-bottom:5px;">
            Judul Notifikasi
          </label>
          <input type="text" id="demoPushTitle" required value="🔔 [DEMO SIRANI] Notifikasi Siswa Terhubung!" style="width:100%; height:38px; border-radius:8px; border:1.5px solid var(--border-2, #cbd5e1); background:var(--bg-2, #ffffff); padding:0 12px; font-size:12.5px; font-weight:700; color:var(--text, #0f172a); box-sizing:border-box;" oninput="updateDemoPreview()" />
        </div>

        <div style="margin-bottom:12px;">
          <label style="display:block; font-size:12px; font-weight:800; color:var(--text, #0f172a); margin-bottom:5px;">
            Isi Pesan Notifikasi
          </label>
          <textarea id="demoPushBody" rows="3" required style="width:100%; border-radius:8px; border:1.5px solid var(--border-2, #cbd5e1); background:var(--bg-2, #ffffff); padding:8px 12px; font-size:12px; line-height:1.45; color:var(--text, #0f172a); resize:vertical; box-sizing:border-box;" oninput="updateDemoPreview()">Halo Bapak/Ibu Wali Murid! Notifikasi kehadiran &amp; kedisiplinan siswa SMKN 1 Air Naningan berhasil aktif di HP Anda. Terima kasih telah mendukung kedisiplinan ananda.</textarea>
        </div>

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:12px; font-weight:800; color:var(--text, #0f172a); margin-bottom:5px;">
            Link Tujuan Saat Notifikasi Ditekan
          </label>
          <input type="text" id="demoPushUrl" value="/cek-presensi" style="width:100%; height:36px; border-radius:8px; border:1.5px solid var(--border-2, #cbd5e1); background:var(--bg-2, #ffffff); padding:0 12px; font-size:12px; font-family:var(--font-mono, monospace); color:var(--text, #0f172a); box-sizing:border-box;" />
        </div>

        {{-- Phone Mockup Preview --}}
        <div style="margin-bottom:16px;">
          <div style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-3, #64748b); margin-bottom:6px; letter-spacing:0.5px;">
            Simulasi Tampilan di Layar Kunci HP:
          </div>
          <div style="background:#1e293b; border-radius:12px; padding:12px 14px; color:#ffffff; box-shadow:inset 0 1px 3px rgba(0,0,0,0.3);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
              <div style="display:flex; align-items:center; gap:6px;">
                <img src="/icons/icon-192.png" alt="SIRANI" style="width:16px; height:16px; border-radius:4px;" onerror="this.src='/logo.png'">
                <span style="font-size:11px; font-weight:700; color:#94a3b8; letter-spacing:0.3px;">SIRANI PORTAL</span>
              </div>
              <span style="font-size:10px; color:#64748b;">Sekarang</span>
            </div>
            <div id="previewTitle" style="font-size:12.5px; font-weight:800; color:#f8fafc; line-height:1.3; margin-bottom:3px;">
              🔔 [DEMO SIRANI] Notifikasi Siswa Terhubung!
            </div>
            <div id="previewBody" style="font-size:11.5px; color:#cbd5e1; line-height:1.4;">
              Halo Bapak/Ibu Wali Murid! Notifikasi kehadiran &amp; kedisiplinan siswa SMKN 1 Air Naningan berhasil aktif di HP Anda. Terima kasih telah mendukung kedisiplinan ananda.
            </div>
          </div>
        </div>

        {{-- Result Message Box --}}
        <div id="demoResultBox" style="display:none; padding:12px 14px; border-radius:10px; font-size:12px; font-weight:700; margin-bottom:14px; align-items:center; gap:8px;"></div>

        {{-- Action Buttons --}}
        <div style="display:flex; justify-content:flex-end; gap:8px;">
          <button type="button" onclick="closeModalDemoPush()" style="height:40px; padding:0 16px; border-radius:8px; border:1px solid var(--border-2, #cbd5e1); background:var(--bg-3, #f1f5f9); color:var(--text-2, #475569); font-size:12.5px; font-weight:700; cursor:pointer;">
            Tutup
          </button>
          <button type="submit" id="btnSubmitDemoPush" style="height:40px; padding:0 20px; border-radius:8px; border:none; background:linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); color:#ffffff; font-size:12.5px; font-weight:800; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 4px 12px rgba(124,58,237,0.3); transition:all .15s ease;">
            <i class="bi bi-broadcast"></i> Kirim Notifikasi Demo Sekarang 🚀
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  @keyframes modalPopIn {
    from { opacity: 0; transform: scale(0.94); }
    to { opacity: 1; transform: scale(1); }
  }
  @keyframes pulseRadar {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
  }
</style>

<script>
  let demoSubscriberPollInterval = null;

  function updateDemoPreview() {
    const t = document.getElementById('demoPushTitle')?.value || '';
    const b = document.getElementById('demoPushBody')?.value || '';
    const pT = document.getElementById('previewTitle');
    const pB = document.getElementById('previewBody');
    if (pT) pT.innerText = t;
    if (pB) pB.innerText = b;
  }

  function openModalDemoPush() {
    const m = document.getElementById('modalDemoPushWaliMurid');
    if (!m) return;
    m.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    updateDemoPreview();
    fetchSubscriberCount(false);

    // Auto-refresh setiap 3.5 detik saat modal terbuka agar terdeteksi live saat wali murid klik izin
    if (demoSubscriberPollInterval) clearInterval(demoSubscriberPollInterval);
    demoSubscriberPollInterval = setInterval(() => {
      fetchSubscriberCount(false);
    }, 3500);
  }

  function closeModalDemoPush() {
    const m = document.getElementById('modalDemoPushWaliMurid');
    if (!m) return;
    m.style.display = 'none';
    document.body.style.overflow = '';
    if (demoSubscriberPollInterval) {
      clearInterval(demoSubscriberPollInterval);
      demoSubscriberPollInterval = null;
    }
  }

  // Tutup jika klik di luar card
  document.addEventListener('click', function(e) {
    const m = document.getElementById('modalDemoPushWaliMurid');
    if (m && e.target === m) {
      closeModalDemoPush();
    }
  });

  async function fetchSubscriberCount(showAnimate = false) {
    const icon = document.getElementById('iconRefreshSub');
    if (showAnimate && icon) {
      icon.classList.add('spin-icon');
    }

    try {
      const res = await fetch('/notifikasi/subscribers-count', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const json = await res.json();
      const count = json.active_count ?? 0;

      // Update semua badge di halaman
      document.querySelectorAll('.global-subscriber-count').forEach(el => {
        el.innerText = count;
      });
    } catch (err) {
      console.warn('Gagal memuat jumlah subscriber:', err);
    } finally {
      if (showAnimate && icon) {
        setTimeout(() => icon.classList.remove('spin-icon'), 500);
      }
    }
  }

  async function sendDemoPushNotification(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitDemoPush');
    const resBox = document.getElementById('demoResultBox');
    const title = document.getElementById('demoPushTitle')?.value || '';
    const body = document.getElementById('demoPushBody')?.value || '';
    const url = document.getElementById('demoPushUrl')?.value || '/cek-presensi';

    if (!confirm('Apakah Anda yakin ingin menembakkan notifikasi demo ini sekarang ke SELURUH HP wali murid yang terhubung?')) {
      return;
    }

    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:14px; height:14px; border:2px solid #fff; border-right-color:transparent; border-radius:50%; display:inline-block; animation:spin .6s linear infinite; margin-right:6px;"></span> Mengirim ke Semua HP...';
    }
    if (resBox) resBox.style.display = 'none';

    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const response = await fetch('/notifikasi/demo-push-walimurid', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ title, body, url })
      });

      const res = await response.json();

      if (resBox) {
        resBox.style.display = 'flex';
        if (res.status === 'success') {
          resBox.style.background = 'rgba(34, 197, 94, 0.12)';
          resBox.style.border = '1px solid rgba(34, 197, 94, 0.3)';
          resBox.style.color = '#15803d';
          resBox.innerHTML = '<i class="bi bi-check-circle-fill" style="font-size:16px;"></i> <div>' + res.message + '</div>';
        } else if (res.status === 'warning') {
          resBox.style.background = 'rgba(234, 179, 8, 0.12)';
          resBox.style.border = '1px solid rgba(234, 179, 8, 0.3)';
          resBox.style.color = '#a16207';
          resBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill" style="font-size:16px;"></i> <div>' + res.message + '</div>';
        } else {
          resBox.style.background = 'rgba(239, 68, 68, 0.12)';
          resBox.style.border = '1px solid rgba(239, 68, 68, 0.3)';
          resBox.style.color = '#b91c1c';
          resBox.innerHTML = '<i class="bi bi-x-circle-fill" style="font-size:16px;"></i> <div>' + res.message + '</div>';
        }
      }

      // Re-fetch count
      fetchSubscriberCount(false);

    } catch (err) {
      if (resBox) {
        resBox.style.display = 'flex';
        resBox.style.background = 'rgba(239, 68, 68, 0.12)';
        resBox.style.border = '1px solid rgba(239, 68, 68, 0.3)';
        resBox.style.color = '#b91c1c';
        resBox.innerHTML = '<i class="bi bi-x-circle-fill" style="font-size:16px;"></i> Gagal menghubungi server: ' + err.message;
      }
    } finally {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-broadcast"></i> Kirim Notifikasi Demo Sekarang 🚀';
      }
    }
  }

  // Load initial count on page load
  document.addEventListener('DOMContentLoaded', function() {
    fetchSubscriberCount(false);
  });
</script>
