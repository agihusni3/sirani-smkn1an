{{-- Indikator 3 Tahap Alur Pembuatan & Penugasan Asesmen --}}
@php
  $currentStep = $step ?? 1;
@endphp

<div class="cbt-wizard-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:10px 18px; margin-bottom:18px; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
  <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
    
    {{-- Step 1: Identitas / Detail --}}
    <div style="display:flex; align-items:center; gap:9px;">
      <div style="width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:12px; flex-shrink:0;
                  background:{{ $currentStep > 1 ? '#ecfdf5' : ($currentStep == 1 ? '#eff6ff' : '#f8fafc') }}; 
                  color:{{ $currentStep > 1 ? '#059669' : ($currentStep == 1 ? '#2563eb' : '#94a3b8') }};
                  border:2px solid {{ $currentStep > 1 ? '#10b981' : ($currentStep == 1 ? '#2563eb' : '#e2e8f0') }};">
        {!! $currentStep > 1 ? '<i class="bi bi-check-lg"></i>' : '1' !!}
      </div>
      <div style="font-size:12.5px; font-weight:{{ $currentStep == 1 ? '800' : '700' }}; color:{{ $currentStep == 1 ? '#0f172a' : '#64748b' }};">
        1. Nama Paket Soal
      </div>
    </div>

    <div style="flex:1; max-width:60px; height:2px; background:{{ $currentStep > 1 ? '#10b981' : '#e2e8f0' }};" class="d-none d-md-block"></div>

    {{-- Step 2: Butir Soal & Kunci --}}
    <div style="display:flex; align-items:center; gap:9px;">
      <div style="width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:12px; flex-shrink:0;
                  background:{{ $currentStep > 2 ? '#ecfdf5' : ($currentStep == 2 ? '#eff6ff' : '#f8fafc') }}; 
                  color:{{ $currentStep > 2 ? '#059669' : ($currentStep == 2 ? '#2563eb' : '#94a3b8') }};
                  border:2px solid {{ $currentStep > 2 ? '#10b981' : ($currentStep == 2 ? '#2563eb' : '#e2e8f0') }};">
        {!! $currentStep > 2 ? '<i class="bi bi-check-lg"></i>' : '2' !!}
      </div>
      <div style="font-size:12.5px; font-weight:{{ $currentStep == 2 ? '800' : '700' }}; color:{{ $currentStep == 2 ? '#0f172a' : '#64748b' }};">
        2. Butir Soal &amp; Validasi
      </div>
    </div>

    <div style="flex:1; max-width:60px; height:2px; background:{{ $currentStep > 2 ? '#10b981' : '#e2e8f0' }};" class="d-none d-md-block"></div>

    {{-- Step 3: Sesi Penugasan --}}
    <div style="display:flex; align-items:center; gap:9px;">
      <div style="width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:12px; flex-shrink:0;
                  background:{{ $currentStep == 3 ? '#eff6ff' : '#f8fafc' }}; 
                  color:{{ $currentStep == 3 ? '#2563eb' : '#94a3b8') }};
                  border:2px solid {{ $currentStep == 3 ? '#2563eb' : '#e2e8f0') }};">
        3
      </div>
      <div style="font-size:12.5px; font-weight:{{ $currentStep == 3 ? '800' : '700' }}; color:{{ $currentStep == 3 ? '#0f172a' : '#64748b' }};">
        3. Sesi Penugasan
      </div>
    </div>

  </div>
</div>
