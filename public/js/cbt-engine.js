/**
 * DCC CBT Exam Engine (Zero Data Loss & Anti-Cheat Monitor)
 * Standar SMKN 1 Air Naningan
 */

class CbtExamEngine {
  constructor(config) {
    this.pesertaId = config.pesertaId;
    this.jadwalId = config.jadwalId;
    this.sisaDetik = config.sisaDetik;
    this.totalSoal = config.totalSoal;
    this.autosaveUrl = config.autosaveUrl;
    this.logViolationUrl = config.logViolationUrl;
    this.finishUrl = config.finishUrl;
    this.csrfToken = config.csrfToken;
    
    this.timerElement = document.getElementById('cbt-timer');
    this.currentSoalIndex = 0;
    this.answers = {};
    this.violations = 0;
    this.db = null;

    this.initIndexedDB();
    this.initTimer();
    this.initAntiCheat();
    this.initOptionCards();
  }

  // 1. IndexedDB Local Storage
  initIndexedDB() {
    const request = indexedDB.open(`cbt_session_${this.pesertaId}`, 1);
    request.onupgradeneeded = (e) => {
      const db = e.target.result;
      if (!db.objectStoreNames.contains('answers')) {
        db.createObjectStore('answers', { keyPath: 'soal_id' });
      }
    };
    request.onsuccess = (e) => {
      this.db = e.target.result;
      this.loadLocalAnswers();
    };
  }

  saveLocal(soalId, jawaban, isRagu) {
    if (!this.db) return;
    const tx = this.db.transaction('answers', 'readwrite');
    const store = tx.objectStore('answers');
    store.put({
      soal_id: soalId,
      jawaban: jawaban,
      is_ragu: isRagu,
      updated_at: new Date().toISOString()
    });
  }

  loadLocalAnswers() {
    if (!this.db) return;
    const tx = this.db.transaction('answers', 'readonly');
    const store = tx.objectStore('answers');
    const req = store.getAll();
    req.onsuccess = () => {
      req.result.forEach(item => {
        this.answers[item.soal_id] = {
          jawaban: item.jawaban,
          isRagu: item.is_ragu
        };
        this.updatePaletteButton(item.soal_id, item.jawaban, item.is_ragu);
      });
    };
  }

  // 2. Real-time Countdown Timer
  initTimer() {
    if (!this.timerElement) return;

    this.timerInterval = setInterval(() => {
      if (this.sisaDetik <= 0) {
        clearInterval(this.timerInterval);
        this.timerElement.textContent = '00:00:00';
        alert('Waktu ujian telah habis! Sistem akan mengumpulkan lembar jawaban Anda secara otomatis.');
        this.submitExam(true);
        return;
      }

      this.sisaDetik--;
      const hours = Math.floor(this.sisaDetik / 3600);
      const minutes = Math.floor((this.sisaDetik % 3600) / 60);
      const seconds = this.sisaDetik % 60;

      const formatted = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
      this.timerElement.textContent = formatted;

      // Warning when under 5 minutes (300 seconds)
      if (this.sisaDetik <= 300) {
        this.timerElement.parentElement.classList.add('cbt-timer-danger');
      }
    }, 1000);
  }

  // 3. Tab Switch & Anti-Cheat Violation Logger
  initAntiCheat() {
    let hidden, visibilityChange;
    if (typeof document.hidden !== "undefined") {
      hidden = "hidden";
      visibilityChange = "visibilitychange";
    }

    document.addEventListener(visibilityChange, () => {
      if (document[hidden]) {
        this.violations++;
        this.logViolation('tab_switch', `Siswa beralih tab atau membuka aplikasi lain. Total: ${this.violations}x`);
      } else {
        alert(`PERINGATAN INTEGRITAS!\nAnda terdeteksi meninggalkan layar ujian (${this.violations}x).\nAktivitas ini dicatat di log pengawas ujian.`);
      }
    });

    window.addEventListener('blur', () => {
      // Blur window
    });
  }

  logViolation(type, detail) {
    fetch(this.logViolationUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': this.csrfToken
      },
      body: JSON.stringify({
        tipe_event: type,
        detail: detail
      })
    }).catch(() => {});
  }

  // 4. Interactive Answer Cards
  initOptionCards() {
    document.querySelectorAll('.cbt-option-card').forEach(card => {
      card.addEventListener('click', (e) => {
        const soalId = card.getAttribute('data-soal-id');
        const huruf = card.getAttribute('data-huruf');

        // Remove active class from sibling cards
        document.querySelectorAll(`.cbt-option-card[data-soal-id="${soalId}"]`).forEach(c => {
          c.classList.remove('selected');
        });

        // Add selected to current card
        card.classList.add('selected');

        const isRagu = document.getElementById(`ragu-${soalId}`) ? document.getElementById(`ragu-${soalId}`).checked : false;

        this.recordAnswer(soalId, huruf, isRagu);
      });
    });
  }

  recordAnswer(soalId, jawaban, isRagu) {
    this.answers[soalId] = { jawaban, isRagu };
    this.saveLocal(soalId, jawaban, isRagu);
    this.updatePaletteButton(soalId, jawaban, isRagu);

    // Background Autosave to Server
    fetch(this.autosaveUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': this.csrfToken
      },
      body: JSON.stringify({
        soal_id: soalId,
        jawaban: jawaban,
        is_ragu: isRagu
      })
    }).then(res => res.json())
      .then(data => {
        const syncBadge = document.getElementById('cbt-sync-status');
        if (syncBadge) {
          syncBadge.textContent = 'Tersimpan';
          syncBadge.style.color = '#10b981';
        }
      }).catch(err => {
        const syncBadge = document.getElementById('cbt-sync-status');
        if (syncBadge) {
          syncBadge.textContent = 'Menyimpan di memori lokal...';
          syncBadge.style.color = '#f59e0b';
        }
      });
  }

  toggleRagu(soalId) {
    const chk = document.getElementById(`ragu-${soalId}`);
    const isRagu = chk ? chk.checked : false;
    const current = this.answers[soalId] ? this.answers[soalId].jawaban : '';

    this.recordAnswer(soalId, current, isRagu);
  }

  updatePaletteButton(soalId, jawaban, isRagu) {
    const btn = document.getElementById(`nav-btn-${soalId}`);
    if (!btn) return;

    btn.classList.remove('answered', 'doubtful');
    if (isRagu) {
      btn.classList.add('doubtful');
    } else if (jawaban && jawaban.trim() !== '') {
      btn.classList.add('answered');
    }
  }

  // 5. Jump to Question
  showSoal(index) {
    document.querySelectorAll('.cbt-soal-box').forEach((box, i) => {
      box.style.display = (i === index) ? 'block' : 'none';
    });

    document.querySelectorAll('.cbt-num-btn').forEach((btn, i) => {
      if (i === index) {
        btn.classList.add('active');
      } else {
        btn.classList.remove('active');
      }
    });

    this.currentSoalIndex = index;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  nextSoal() {
    if (this.currentSoalIndex < this.totalSoal - 1) {
      this.showSoal(this.currentSoalIndex + 1);
    }
  }

  prevSoal() {
    if (this.currentSoalIndex > 0) {
      this.showSoal(this.currentSoalIndex - 1);
    }
  }

  submitExam(force = false) {
    if (!force) {
      // Check if all answered
      const answeredCount = Object.values(this.answers).filter(a => a.jawaban && a.jawaban.trim() !== '').length;
      let msg = `Anda telah menjawab ${answeredCount} dari ${this.totalSoal} butir soal.\nApakah Anda yakin ingin menyelesaikan ujian ini?`;
      if (answeredCount < this.totalSoal) {
        msg = `PERHATIAN: Masih ada ${this.totalSoal - answeredCount} soal yang belum Anda jawab!\nApakah Anda benar-benar yakin ingin mengakhiri ujian sekarang?`;
      }
      if (!confirm(msg)) {
        return;
      }
    }

    const form = document.getElementById('form-finish-exam');
    if (form) {
      form.submit();
    } else {
      window.location.href = this.finishUrl;
    }
  }
}

window.CbtExamEngine = CbtExamEngine;
