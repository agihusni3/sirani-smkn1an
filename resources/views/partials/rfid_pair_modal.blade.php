{{-- MODAL PAIRING KARTU RFID FISIK (SMKN 1 AIR NANINGAN) --}}
<div id="rfidPairModal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
  <div class="panel" style="max-width:440px; width:92%; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-md); padding:24px; box-shadow:0 20px 50px rgba(0,0,0,0.5); position:relative; animation:modalFadeIn .25s ease;">
    
    {{-- Header Modal --}}
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:12px; margin-bottom:16px;">
      <div style="font-weight:900; font-size:15px; color:var(--text); display:flex; align-items:center; gap:8px;">
        <i class="bi bi-person-vcard-fill" style="color:#000000; font-size:18px;"></i>
        <span id="rfidModalTitle">Kelola / Ganti Kartu RFID</span>
      </div>
      <button type="button" onclick="closeRfidPairModal()" style="background:transparent; border:none; color:var(--text-3); font-size:18px; cursor:pointer;" title="Tutup Modal">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    {{-- Info Pemilik --}}
    <div style="background:var(--bg-3); border:1px solid var(--border); border-radius:var(--r-sm); padding:12px; display:flex; align-items:center; gap:12px; margin-bottom:18px;">
      <img id="rfidModalPhoto" src="/img/user-default.png" style="width:46px; height:46px; border-radius:8px; object-fit:cover; border:1px solid var(--border-2);" />
      <div>
        <strong id="rfidModalName" style="color:var(--text); font-size:13.5px; display:block;">-</strong>
        <span id="rfidModalSub" style="color:var(--text-2); font-size:11.5px; font-family:var(--font-mono);">-</span>
      </div>
    </div>

    {{-- Form Input UID --}}
    <form id="rfidPairForm" onsubmit="submitRfidPairing(event)">
      <input type="hidden" id="rfidPemilikType" value="siswa" />
      <input type="hidden" id="rfidPemilikId" value="" />

      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:12px; font-weight:800; color:var(--text); margin-bottom:6px;">
          Kode UID Kartu RFID:
        </label>
        <div style="position:relative;">
          <input type="text" id="rfidUidInput" placeholder="Tempelkan kartu atau ketik UID..." required
            autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
            style="width:100%; height:40px; border-radius:var(--r-sm); border:1.5px solid #000000; background:var(--bg-3); color:var(--text); font-family:var(--font-mono); font-size:14px; font-weight:700; padding:0 12px; letter-spacing:.05em; outline:none; text-align:center;" autofocus />
        </div>
        <small style="color:var(--text-3); font-size:11px; margin-top:4px; display:block;">
          <i class="bi bi-info-circle"></i> Gunakan USB RFID Scanner atau ketikkan kode hex kartu.
        </small>
      </div>

      <div id="rfidPairAlert" style="display:none; padding:10px; border-radius:var(--r-sm); font-size:12px; font-weight:700; margin-bottom:14px;"></div>

      <div style="display:flex; justify-content:flex-end; gap:8px; border-top:1px solid var(--border); padding-top:14px;">
        <button type="button" id="btnUnpairBtn" onclick="submitRfidUnpairing()" class="btn btn-outline" style="color:var(--red); border-color:rgba(239,68,68,0.4); font-size:12px; padding:0 12px; height:36px; display:none;">
          <i class="bi bi-trash3"></i> Lepas Kartu
        </button>
        <button type="button" onclick="closeRfidPairModal()" class="btn btn-outline" style="font-size:12px; padding:0 14px; height:36px;">
          Batal
        </button>
        <button type="submit" id="btnSaveRfid" class="btn btn-gold" style="font-size:12px; padding:0 16px; height:36px; display:inline-flex; align-items:center; gap:6px;">
          <i class="bi bi-check-lg"></i> Simpan Kartu
        </button>
      </div>
    </form>

  </div>
</div>

<script>
  function openRfidPairModal(type, id, nama, identitas, fotoUrl, currentUid) {
    const modal = document.getElementById('rfidPairModal');
    if (!modal) return;
    document.getElementById('rfidPemilikType').value = type;
    document.getElementById('rfidPemilikId').value = id;
    document.getElementById('rfidModalName').textContent = nama;
    document.getElementById('rfidModalSub').textContent = identitas;
    document.getElementById('rfidModalPhoto').src = fotoUrl || '/img/user-default.png';
    
    const input = document.getElementById('rfidUidInput');
    input.value = currentUid || '';

    const titleEl = document.getElementById('rfidModalTitle');
    const saveBtn = document.getElementById('btnSaveRfid');
    const unpairBtn = document.getElementById('btnUnpairBtn');

    if (currentUid) {
      if (titleEl) titleEl.textContent = 'Kelola / Ganti Kartu RFID';
      if (saveBtn) saveBtn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan Perubahan';
      if (unpairBtn) unpairBtn.style.display = 'inline-flex';
    } else {
      if (titleEl) titleEl.textContent = 'Pasang Kartu RFID';
      if (saveBtn) saveBtn.innerHTML = '<i class="bi bi-check-lg"></i> Pasangkan Kartu';
      if (unpairBtn) unpairBtn.style.display = 'none';
    }

    const alertBox = document.getElementById('rfidPairAlert');
    alertBox.style.display = 'none';

    modal.classList.add('active');
    modal.style.display = 'flex';
    modal.style.opacity = '1';
    setTimeout(() => input.focus(), 150);
  }

  function closeRfidPairModal() {
    const modal = document.getElementById('rfidPairModal');
    if (modal) {
      modal.classList.remove('active');
      modal.style.display = 'none';
    }
  }

  async function submitRfidPairing(e) {
    e.preventDefault();
    const type = document.getElementById('rfidPemilikType').value;
    const id = document.getElementById('rfidPemilikId').value;
    const uid = document.getElementById('rfidUidInput').value.trim();
    const btn = document.getElementById('btnSaveRfid');
    const alertBox = document.getElementById('rfidPairAlert');

    if (!uid) return;

    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';

    try {
      const res = await fetch('/api/v1/rfid-pair', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          uid: uid,
          pemilik_type: type,
          pemilik_id: id,
        })
      });

      const json = await res.json();
      if (json.success) {
        alertBox.style.display = 'block';
        alertBox.style.background = 'var(--green-dim)';
        alertBox.style.color = 'var(--green)';
        alertBox.style.border = '1px solid rgba(16,185,129,0.3)';
        alertBox.innerHTML = `<i class="bi bi-check-circle-fill"></i> ${json.message}`;
        setTimeout(() => {
          closeRfidPairModal();
          window.location.reload();
        }, 800);
      } else {
        alertBox.style.display = 'block';
        alertBox.style.background = 'var(--red-dim)';
        alertBox.style.color = 'var(--red)';
        alertBox.style.border = '1px solid rgba(239,68,68,0.3)';
        alertBox.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i> ${json.message || 'Gagal menyimpan kartu'}`;
      }
    } catch (err) {
      console.error(err);
      alertBox.style.display = 'block';
      alertBox.style.background = 'var(--red-dim)';
      alertBox.style.color = 'var(--red)';
      alertBox.textContent = 'Terjadi kesalahan jaringan.';
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan Kartu';
    }
  }

  async function submitRfidUnpairing() {
    if (!confirm('Apakah Anda yakin ingin melepas/menonaktifkan kartu RFID ini?')) return;

    const type = document.getElementById('rfidPemilikType').value;
    const id = document.getElementById('rfidPemilikId').value;
    const alertBox = document.getElementById('rfidPairAlert');

    try {
      const res = await fetch('/api/v1/rfid-unpair', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          pemilik_type: type,
          pemilik_id: id,
        })
      });

      const json = await res.json();
      if (json.success) {
        alertBox.style.display = 'block';
        alertBox.style.background = 'var(--green-dim)';
        alertBox.style.color = 'var(--green)';
        alertBox.innerHTML = `<i class="bi bi-check-circle-fill"></i> ${json.message}`;
        setTimeout(() => {
          closeRfidPairModal();
          window.location.reload();
        }, 800);
      } else {
        alertBox.style.display = 'block';
        alertBox.style.background = 'var(--red-dim)';
        alertBox.style.color = 'var(--red)';
        alertBox.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i> ${json.message}`;
      }
    } catch (err) {
      console.error(err);
    }
  }
</script>
