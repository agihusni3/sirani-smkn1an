@php
  $currentStep = $currentStep ?? 1;
@endphp

<div class="akademik-stepper-wrap" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; padding:12px 18px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; flex-wrap:wrap; gap:8px;">
    <div style="font-size:11px; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; gap:6px;">
      <i class="bi bi-diagram-3-fill text-primary"></i> Alur Kerja Sistematis Waka Kurikulum (SOP Resmi)
    </div>
    <div style="font-size:11.5px; color:#64748b;">
      Lakukan langkah 1 s.d. 4 secara berurutan agar jadwal teralokasi akurat &amp; tanpa bentrok.
    </div>
  </div>

  <div class="akademik-stepper-grid">
    {{-- Step 1 --}}
    <a href="{{ route('akademik.matpel.index') }}" class="akademik-step-item {{ $currentStep == 1 ? 'active' : ($currentStep > 1 ? 'completed' : '') }}">
      <div class="step-num">{{ $currentStep > 1 ? '✓' : '1' }}</div>
      <div class="step-text">
        <div class="step-title">1. Mata Pelajaran &amp; CP</div>
        <div class="step-sub">Struktur Mapel, Fase, &amp; Lab</div>
      </div>
    </a>

    <div class="step-arrow"><i class="bi bi-chevron-right"></i></div>

    {{-- Step 2 --}}
    <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi']) }}" class="akademik-step-item {{ $currentStep == 2 ? 'active' : ($currentStep > 2 ? 'completed' : '') }}">
      <div class="step-num">{{ $currentStep > 2 ? '✓' : '2' }}</div>
      <div class="step-text">
        <div class="step-title">2. SK Pembagian Tugas</div>
        <div class="step-sub">Beban 24–40 JP &amp; Tugas Tambahan</div>
      </div>
    </a>

    <div class="step-arrow"><i class="bi bi-chevron-right"></i></div>

    {{-- Step 3 --}}
    <a href="{{ route('akademik.jadwal.index', ['tab' => 'jadwal']) }}" class="akademik-step-item {{ $currentStep == 3 ? 'active' : ($currentStep > 3 ? 'completed' : '') }}">
      <div class="step-num">{{ $currentStep > 3 ? '✓' : '3' }}</div>
      <div class="step-text">
        <div class="step-title">3. Jadwal Pelajaran (Roster)</div>
        <div class="step-sub">Slot Jam &amp; Proteksi Lab</div>
      </div>
    </a>

    <div class="step-arrow"><i class="bi bi-chevron-right"></i></div>

    {{-- Step 4 --}}
    <a href="{{ route('akademik.jadwal.index', ['tab' => 'piket']) }}" class="akademik-step-item {{ $currentStep == 4 ? 'active' : '' }}">
      <div class="step-num">4</div>
      <div class="step-text">
        <div class="step-title">4. Jadwal Guru Piket</div>
        <div class="step-sub">Piket Harian Senin–Jumat</div>
      </div>
    </a>
  </div>
</div>

<style>
.akademik-stepper-grid {
  display: grid;
  grid-template-columns: 1fr auto 1fr auto 1fr auto 1fr;
  align-items: center;
  gap: 8px;
}
.akademik-step-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  border-radius: 9px;
  background: #f8fafc;
  border: 1.5px solid #e2e8f0;
  text-decoration: none;
  transition: all 0.2s ease;
}
.akademik-step-item:hover {
  background: #f1f5f9;
  border-color: #cbd5e1;
}
.akademik-step-item .step-num {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #e2e8f0;
  color: #475569;
  font-weight: 800;
  font-size: 13px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.akademik-step-item .step-title {
  font-size: 12.5px;
  font-weight: 800;
  color: #334155;
  line-height: 1.2;
}
.akademik-step-item .step-sub {
  font-size: 10.5px;
  color: #64748b;
  margin-top: 2px;
}
.akademik-step-item.active {
  background: #eff6ff;
  border-color: #3b82f6;
  box-shadow: 0 0 0 1px #3b82f6;
}
.akademik-step-item.active .step-num {
  background: #2563eb;
  color: #ffffff;
}
.akademik-step-item.active .step-title {
  color: #1d4ed8;
}
.akademik-step-item.completed {
  background: #f0fdf4;
  border-color: #86efac;
}
.akademik-step-item.completed .step-num {
  background: #16a34a;
  color: #ffffff;
}
.step-arrow {
  color: #94a3b8;
  font-size: 12px;
  text-align: center;
}
@media (max-width: 991px) {
  .akademik-stepper-grid {
    grid-template-columns: 1fr;
  }
  .step-arrow {
    display: none;
  }
}
</style>
