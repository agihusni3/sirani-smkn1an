<!-- Cropper.js CSS & JS CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<style>
  .crop-modal-overlay {
    position: fixed !important;
    top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;
    background: rgba(0, 0, 0, 0.85) !important;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 99999 !important;
    display: none !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 16px !important;
  }
  .crop-modal-overlay.active {
    display: flex !important;
  }
  .crop-modal-card {
    background: var(--bg-2, #1e293b);
    border: 1.5px solid var(--border-2, #334155);
    border-radius: var(--r-lg, 14px);
    box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.85);
    width: 100%;
    max-width: 620px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    animation: cropModalPop .2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }
  @keyframes cropModalPop {
    0% { transform: scale(0.94); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
  }
  .crop-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    background: var(--bg-3, #0f172a);
    border-bottom: 1px solid var(--border, #334155);
  }
  .crop-title {
    font-size: 15px;
    font-weight: 800;
    color: var(--text, #f8fafc);
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .crop-container {
    width: 100%;
    height: 340px;
    background: #000;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .crop-container img {
    max-width: 100%;
    max-height: 100%;
    display: block;
  }
  .crop-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px;
    background: var(--bg-3, #0f172a);
    border-top: 1px solid var(--border, #334155);
    border-bottom: 1px solid var(--border, #334155);
    flex-wrap: wrap;
    gap: 10px;
  }
  .crop-ratio-tabs {
    display: flex;
    gap: 5px;
    background: var(--bg, #0b0f19);
    padding: 3px;
    border-radius: var(--r-sm, 8px);
    border: 1px solid var(--border, #334155);
  }
  .crop-ratio-btn {
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 6px;
    background: transparent;
    border: none;
    color: var(--text-2, #94a3b8);
    cursor: pointer;
    transition: all .15s;
  }
  .crop-ratio-btn.active {
    background: var(--gold, #ca8a04);
    color: #fff;
  }
  .crop-btn-group {
    display: flex;
    gap: 5px;
  }
  .crop-tool-btn {
    width: 34px;
    height: 34px;
    border-radius: var(--r-sm, 6px);
    background: var(--surface, #1e293b);
    border: 1px solid var(--border-2, #334155);
    color: var(--text, #f8fafc);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 14px;
    transition: all .15s;
  }
  .crop-tool-btn:hover {
    background: var(--gold-dim, rgba(202,138,4,0.15));
    border-color: var(--gold, #ca8a04);
    color: var(--gold, #ca8a04);
  }
  .crop-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    background: var(--bg-2, #1e293b);
  }
  .crop-preview-thumb {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    overflow: hidden;
    border: 1.5px solid var(--gold, #ca8a04);
    background: #000;
    flex-shrink: 0;
  }
</style>

<!-- Modal Dialog Universal Image Cropper -->
<div id="cropModalOverlay" class="crop-modal-overlay">
  <div class="crop-modal-card">
    <div class="crop-head">
      <div class="crop-title">
        <i class="bi bi-crop" style="color:var(--gold, #ca8a04);"></i>
        <span id="cropModalHeading">Sesuaikan &amp; Potong Foto (Crop)</span>
      </div>
      <button type="button" class="btn btn-sm btn-outline" style="width:30px; height:30px; padding:0; display:flex; align-items:center; justify-content:center;" onclick="closeCropModal()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <!-- Area Kanvas Pemotong -->
    <div class="crop-container">
      <img id="cropImageElement" src="" alt="Crop Source" />
    </div>

    <!-- Toolbar Alat Crop -->
    <div class="crop-controls">
      <!-- Pilihan Rasio Aspek -->
      <div class="crop-ratio-tabs">
        <button type="button" class="crop-ratio-btn active" id="btnRatio1_1" onclick="setCropperRatio(1/1, this)">1:1 (Persegi)</button>
        <button type="button" class="crop-ratio-btn" id="btnRatio3_4" onclick="setCropperRatio(3/4, this)">3:4 (Pasfoto)</button>
        <button type="button" class="crop-ratio-btn" id="btnRatio4_3" onclick="setCropperRatio(4/3, this)">4:3 (Lanskap)</button>
        <button type="button" class="crop-ratio-btn" id="btnRatioFree" onclick="setCropperRatio(NaN, this)">Bebas</button>
      </div>

      <!-- Tombol Alat Zoom & Rotasi -->
      <div class="crop-btn-group">
        <button type="button" class="crop-tool-btn" onclick="cropperZoom(0.1)" title="Perbesar (+)">
          <i class="bi bi-zoom-in"></i>
        </button>
        <button type="button" class="crop-tool-btn" onclick="cropperZoom(-0.1)" title="Perkecil (-)">
          <i class="bi bi-zoom-out"></i>
        </button>
        <button type="button" class="crop-tool-btn" onclick="cropperRotate(-90)" title="Putar Kiri 90°">
          <i class="bi bi-arrow-counterclockwise"></i>
        </button>
        <button type="button" class="crop-tool-btn" onclick="cropperRotate(90)" title="Putar Kanan 90°">
          <i class="bi bi-arrow-clockwise"></i>
        </button>
        <button type="button" class="crop-tool-btn" onclick="cropperReset()" title="Reset Posisi Awal">
          <i class="bi bi-arrow-repeat"></i>
        </button>
      </div>
    </div>

    <!-- Footer Modal -->
    <div class="crop-footer">
      <div style="display:flex; align-items:center; gap:10px;">
        <div class="crop-preview-thumb" id="cropCirclePreview"></div>
        <div style="font-size:11.5px; color:var(--text-3, #94a3b8); line-height:1.3;">
          <strong style="color:var(--text, #f8fafc); display:block;">Live Preview</strong>
          Geser &amp; sesuaikan area fokus
        </div>
      </div>

      <div style="display:flex; gap:8px;">
        <button type="button" class="btn btn-outline" style="padding:7px 16px; font-size:12.5px;" onclick="closeCropModal()">
          Batal
        </button>
        <button type="button" class="btn btn-gold" id="btnApplyCroppedImage" style="padding:7px 20px; font-size:12.5px; font-weight:800; display:inline-flex; align-items:center; gap:6px;" onclick="applyCroppedImage()">
          <i class="bi bi-check2-circle"></i> Gunakan Hasil Crop
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  let activeCropper = null;
  let targetFileInputElement = null;
  let targetPreviewElement = null;
  let originalFileObj = null;
  let currentAspectRatio = 1 / 1;

  /**
   * Inisialisasi crop saat user memilih file gambar
   */
  function initPhotoCrop(fileInput, previewTarget = null, initialRatio = '1:1', customTitle = null) {
    if (!fileInput || !fileInput.files || !fileInput.files[0]) return;

    // Cegah re-trigger looping jika file baru saja selesai dicrop
    if (fileInput._isCroppedNow === true) return;

    const file = fileInput.files[0];

    // Jika nama file sudah berakhiran _cropped atau file bukan gambar (misal PDF), jangan buka modal crop lagi
    if (file.name && file.name.includes('_cropped')) return;

    if (!file.type.startsWith('image/')) {
      if (previewTarget) {
        const previewEl = typeof previewTarget === 'string' ? document.getElementById(previewTarget) : previewTarget;
        if (previewEl && previewEl.tagName === 'IMG') {
          previewEl.src = '/img/pdf-icon.png';
        }
      }
      return;
    }

    originalFileObj = file;
    targetFileInputElement = fileInput;
    targetPreviewElement = previewTarget ? (typeof previewTarget === 'string' ? document.getElementById(previewTarget) : previewTarget) : null;

    if (customTitle) {
      document.getElementById('cropModalHeading').innerText = customTitle;
    } else {
      document.getElementById('cropModalHeading').innerText = 'Sesuaikan & Potong Foto (Crop)';
    }

    // Tentukan rasio awal
    currentAspectRatio = 1 / 1;
    if (initialRatio === '3:4' || initialRatio === 3/4) currentAspectRatio = 3 / 4;
    else if (initialRatio === '4:3' || initialRatio === 4/3) currentAspectRatio = 4 / 3;
    else if (initialRatio === 'free' || initialRatio === 'NaN' || isNaN(initialRatio)) currentAspectRatio = NaN;

    updateRatioTabUI(currentAspectRatio);

    const reader = new FileReader();
    reader.onload = function (e) {
      const cropImg = document.getElementById('cropImageElement');
      cropImg.src = e.target.result;

      const overlay = document.getElementById('cropModalOverlay');
      overlay.style.display = 'flex';
      overlay.classList.add('active');

      if (activeCropper) {
        try { activeCropper.destroy(); } catch(err) {}
      }

      activeCropper = new Cropper(cropImg, {
        aspectRatio: currentAspectRatio,
        viewMode: 1,
        dragMode: 'move',
        autoCropArea: 0.92,
        restore: false,
        guides: true,
        center: true,
        highlight: false,
        cropBoxMovable: true,
        cropBoxResizable: true,
        toggleDragModeOnDblclick: false,
        preview: '#cropCirclePreview',
      });
    };
    reader.readAsDataURL(file);
  }

  function setCropperRatio(ratio, btnEl) {
    currentAspectRatio = ratio;
    if (activeCropper) {
      activeCropper.setAspectRatio(ratio);
    }
    document.querySelectorAll('.crop-ratio-btn').forEach(b => b.classList.remove('active'));
    if (btnEl) btnEl.classList.add('active');
  }

  function updateRatioTabUI(ratio) {
    document.querySelectorAll('.crop-ratio-btn').forEach(b => b.classList.remove('active'));
    if (isNaN(ratio)) {
      document.getElementById('btnRatioFree')?.classList.add('active');
    } else if (Math.abs(ratio - (3/4)) < 0.05) {
      document.getElementById('btnRatio3_4')?.classList.add('active');
    } else if (Math.abs(ratio - (4/3)) < 0.05) {
      document.getElementById('btnRatio4_3')?.classList.add('active');
    } else {
      document.getElementById('btnRatio1_1')?.classList.add('active');
    }
  }

  function cropperZoom(val) {
    if (activeCropper) activeCropper.zoom(val);
  }

  function cropperRotate(deg) {
    if (activeCropper) activeCropper.rotate(deg);
  }

  function cropperReset() {
    if (activeCropper) activeCropper.reset();
  }

  function closeCropModal() {
    const overlay = document.getElementById('cropModalOverlay');
    if (overlay) {
      overlay.classList.remove('active');
      overlay.style.display = 'none';
    }
    if (activeCropper) {
      try {
        activeCropper.destroy();
      } catch(e) {}
      activeCropper = null;
    }
  }

  function applyCroppedImage() {
    if (!activeCropper || !targetFileInputElement) {
      closeCropModal();
      return;
    }

    const fileInput = targetFileInputElement;
    const previewEl = targetPreviewElement;
    const origFile = originalFileObj;
    const ratio = currentAspectRatio;

    let canvas = null;
    try {
      let outWidth = 600;
      let outHeight = 600;
      if (!isNaN(ratio) && ratio > 0) {
        outHeight = Math.round(outWidth / ratio);
      } else {
        const data = activeCropper.getData();
        outWidth = Math.min(1200, Math.round(data.width || 600));
        outHeight = Math.min(1200, Math.round(data.height || 600));
      }

      canvas = activeCropper.getCroppedCanvas({
        width: outWidth,
        height: outHeight,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
      });
    } catch (err) {
      console.error("Canvas error:", err);
    }

    // 1. TUTUP MODAL LANGSUNG SEKETIKA (Tanpa jeda)
    closeCropModal();

    if (!canvas) return;

    try {
      const mimeType = (origFile && origFile.type === 'image/png') ? 'image/png' : 'image/jpeg';
      const ext = mimeType === 'image/png' ? '.png' : '.jpg';
      const fileName = (origFile ? origFile.name : 'foto_upload').replace(/\.[^/.]+$/, "") + "_cropped" + ext;

      // 2. Update thumbnail preview seketika dari DataURL
      const dataUrl = canvas.toDataURL(mimeType, 0.92);
      if (previewEl) {
        if (previewEl.tagName === 'IMG') {
          previewEl.src = dataUrl;
          previewEl.style.display = 'block';
        } else {
          previewEl.innerHTML = `<img src="${dataUrl}" style="width:100%; height:100%; object-fit:cover; border-radius:inherit;" />`;
        }
      }

      // 3. Masukkan file blob hasil crop ke form input
      canvas.toBlob(function (blob) {
        if (!blob) return;

        try {
          const croppedFile = new File([blob], fileName, { type: mimeType });

          // Tandai flag agar onchange berikutnya tidak membuka modal lagi
          fileInput._isCroppedNow = true;

          const dataTransfer = new DataTransfer();
          dataTransfer.items.add(croppedFile);
          fileInput.files = dataTransfer.files;

          setTimeout(() => {
            if (fileInput) {
              fileInput._isCroppedNow = false;
            }
          }, 800);
        } catch (err) {
          console.warn("DataTransfer update fallback:", err);
        }
      }, mimeType, 0.92);

    } catch (err) {
      console.error("Error cropping image:", err);
    }
  }

  // Tutup dengan tombol Escape
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      const overlay = document.getElementById('cropModalOverlay');
      if (overlay && (overlay.classList.contains('active') || overlay.style.display === 'flex')) {
        closeCropModal();
      }
    }
  });

  // Global Auto-Binder untuk semua input bertipe file gambar
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[type="file"][data-crop="true"]').forEach(input => {
      input.addEventListener('change', function () {
        if (this._isCroppedNow === true) return;
        const previewId = this.getAttribute('data-crop-preview');
        const ratio = this.getAttribute('data-crop-ratio') || '1:1';
        const title = this.getAttribute('data-crop-title') || null;
        initPhotoCrop(this, previewId, ratio, title);
      });
    });
  });
</script>
