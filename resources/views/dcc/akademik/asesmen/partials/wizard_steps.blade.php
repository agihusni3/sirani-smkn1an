{{-- Indikator 3 Tahap Alur Pembuatan & Penugasan Asesmen --}}
@php
  $currentStep = $step ?? 1;
@endphp

<div class="cbt-wizard-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 20px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
  <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
    
    {{-- Step 1: Tentukan Paket Soal --}}
    <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:200px;">
      <div style="width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:14px; 
                  background:{{ $currentStep > 1 ? '#ecfdf5' : ($currentStep == 1 ? '#eff6ff' : '#f1f5f9') }}; 
                  color:{{ $currentStep > 1 ? '#059669' : ($currentStep == 1 ? '#2563eb' : '#94a3b8') }};
                  border:2px solid {{ $currentStep > 1 ? '#10b981' : ($currentStep == 1 ? '#2563eb' : '#cbd5e1') }};">
        {!! $currentStep > 1 ? '<i class="bi bi-check-lg"></i>' : '1' !!}
      </div>
      <div>
        <div style="font-size:13px; font-weight:{{ $currentStep == 1 ? '800' : '700' }}; color:{{ $currentStep == 1 ? '#1e293b' : '#64748b' }};">
          1. Nama Paket Soal
        </div>
        <div style="font-size:11px; color:#94a3b8;">Identitas, Target Soal &amp; KKM</div>
      </div>
    </div>

    <div style="width:40px; height:2px; background:{{ $currentStep > 1 ? '#10b981' : '#e2e8f0' }}; display:none;" class="d-md-block"></div>

    {{-- Step 2: Butir Soal & Validasi --}}
    <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:200px;">
      <div style="width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:14px; 
                  background:{{ $currentStep > 2 ? '#ecfdf5' : ($currentStep == 2 ? '#eff6ff' : '#f1f5f9') }}; 
                  color:{{ $currentStep > 2 ? '#059669' : ($currentStep == 2 ? '#2563eb' : '#94a3b8') }};
                  border:2px solid {{ $currentStep > 2 ? '#10b981' : ($currentStep == 2 ? '#2563eb' : '#cbd5e1') }};">
        {!! $currentStep > 2 ? '<i class="bi bi-check-lg"></i>' : '2' !!}
      </div>
      <div>
        <div style="font-size:13px; font-weight:{{ $currentStep == 2 ? '800' : '700' }}; color:{{ $currentStep == 2 ? '#1e293b' : '#64748b' }};">
          2. Butir Soal &amp; Validasi
        </div>
        <div style="font-size:11px; color:#94a3b8;">Input Soal, Kunci &amp; Audit Mutu</div>
      </div>
    </div>

    <div style="width:40px; height:2px; background:{{ $currentStep > 2 ? '#10b981' : '#e2e8f0' }}; display:none;" class="d-md-block"></div>

    {{-- Step 3: Sesi Penugasan --}}
    <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:200px;">
      <div style="width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:14px; 
                  background:{{ $currentStep == 3 ? '#eff6ff' : '#f1f5f9' }}; 
                  color:{{ $currentStep == 3 ? '#2563eb' : '#94a3b8' }};
                  border:2px solid {{ $currentStep == 3 ? '#2563eb' : '#cbd5e1' }};">
        3
      </div>
      <div>
        <div style="font-size:13px; font-weight:{{ $currentStep == 3 ? '800' : '700' }}; color:{{ $currentStep == 3 ? '#1e293b' : '#64748b' }};">
          3. Sesi Penugasan
        </div>
        <div style="font-size:11px; color:#94a3b8;">Rombel/Siswa, Jadwal &amp; Anti-Curang</div>
      </div>
    </div>

  </div>
</div>
