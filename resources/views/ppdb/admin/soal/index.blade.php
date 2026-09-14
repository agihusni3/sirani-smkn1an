@php
  $cssVersion = file_exists(public_path('css/admin-ppdb.css')) ? filemtime(public_path('css/admin-ppdb.css')) : time();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kelola Bank Soal CBT PPDB — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/admin-ppdb.css') }}?v={{ $cssVersion }}">
</head>
<body>
<div class="app-container">
  @include('partials.sidebar_ppdb')

  <main class="main-content">
    <div class="cbt-container-wide">
      
      {{-- BREADCRUMB & HEADER --}}
      <nav class="cbt-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.ppdb.seleksi', ['tab' => 'pengaturan']) }}">PPDB Seleksi</a>
        <span class="cbt-breadcrumb-separator">/</span>
        <span class="cbt-breadcrumb-current">Bank Soal CBT</span>
      </nav>
      
      <div class="cbt-page-header">
        <div>
          <h1 class="cbt-page-title">
            Bank Soal CBT PPDB
          </h1>
          <div class="cbt-page-subtitle">
            {{ $setting->judul_ujian }} &bull; Durasi: {{ $setting->durasi_menit }} Menit &bull; Total Soal: {{ $soalPg->count() + $soalEsai->count() }} Butir
          </div>
        </div>
        
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
          <a href="{{ route('admin.ppdb.seleksi', ['tab' => 'pengaturan']) }}" class="cbt-btn cbt-btn-secondary">
            &larr; Kembali ke Seleksi
          </a>
          <a href="{{ route('admin.ppdb.soal.create', $setting->id) }}" class="cbt-btn cbt-btn-primary">
            + Tambah Soal
          </a>
        </div>
      </div>

      {{-- ALERTS --}}
      @if(session('success'))
        <div class="cbt-alert cbt-alert-success" role="alert">
          <span>{{ session('success') }}</span>
        </div>
      @endif

      @if(session('error'))
        <div class="cbt-alert cbt-alert-danger" role="alert">
          <span>{{ session('error') }}</span>
        </div>
      @endif

      {{-- STATISTIK SOAL --}}
      <div class="ppdb-stat-grid">
        <div class="ppdb-stat-card">
          <div>
            <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Pilihan Ganda (PG)</div>
            <div style="font-size: 24px; font-weight: 800; color: #2563eb; margin-top: 4px;">
              {{ $soalPg->count() }} <span style="font-size: 13px; font-weight: 500; color: #64748b;">butir</span>
            </div>
            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
              Bobot: {{ $setting->bobot_pg }}% &bull; Koreksi Otomatis
            </div>
          </div>
        </div>

        <div class="ppdb-stat-card">
          <div>
            <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Soal Esai</div>
            <div style="font-size: 24px; font-weight: 800; color: #059669; margin-top: 4px;">
              {{ $soalEsai->count() }} <span style="font-size: 13px; font-weight: 500; color: #64748b;">butir</span>
            </div>
            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
              Bobot: {{ $setting->bobot_esai }}% &bull; Koreksi Manual
            </div>
          </div>
        </div>

        <div class="ppdb-stat-card">
          <div>
            <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Pengacakan Soal</div>
            <div style="font-size: 16px; font-weight: 800; color: #0f172a; margin-top: 6px;">
              Aktif Otomatis
            </div>
            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
              Urutan soal diacak per siswa saat ujian
            </div>
          </div>
        </div>
      </div>

      {{-- DAFTAR SOAL PILIHAN GANDA --}}
      <div class="cbt-card" style="padding: 0; overflow: hidden;">
        <div class="cbt-card-header">
          <div>
            <h2 class="cbt-card-title">Bagian I: Soal Pilihan Ganda (PG)</h2>
            <div class="cbt-card-desc">Soal dikoreksi secara otomatis oleh sistem berdasarkan kunci jawaban</div>
          </div>
          <a href="{{ route('admin.ppdb.soal.create', ['settingId' => $setting->id, 'type' => 'pg']) }}" class="cbt-btn cbt-btn-secondary cbt-btn-sm" style="color: #2563eb; font-weight: 700;">
            + Tambah Soal PG
          </a>
        </div>

        @if($soalPg->isEmpty())
          <div class="cbt-empty-state">
            <div class="cbt-empty-state-title">Belum ada soal Pilihan Ganda</div>
            <p class="cbt-empty-state-desc">Klik tombol di bawah untuk mulai menginput soal pertama.</p>
            <a href="{{ route('admin.ppdb.soal.create', ['settingId' => $setting->id, 'type' => 'pg']) }}" class="cbt-btn cbt-btn-primary">
              + Tambah Soal PG Sekarang
            </a>
          </div>
        @else
          <div>
            @foreach($soalPg as $soal)
              <div class="cbt-soal-item">
                <div class="cbt-soal-num-badge cbt-num-pg" title="Nomor Urut {{ $soal->nomor_urut }}">
                  {{ $soal->nomor_urut }}
                </div>

                <div class="cbt-soal-content">
                  <div class="cbt-soal-text">
                    {!! nl2br(e($soal->pertanyaan)) !!}
                  </div>

                  {{-- Opsi Jawaban --}}
                  <div class="cbt-opsi-display-grid">
                    @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                      @php 
                        $val = $soal->{'opsi_' . strtolower($opt)}; 
                        $isKunci = ($soal->kunci_jawaban === $opt);
                      @endphp
                      @if(!empty($val))
                        <div class="cbt-opsi-pill {{ $isKunci ? 'is-kunci' : '' }}">
                          <strong style="margin-right: 4px;">{{ $opt }}.</strong>
                          <span>{{ $val }}</span>
                          @if($isKunci)
                            <span class="cbt-kunci-tag">Kunci</span>
                          @endif
                        </div>
                      @endif
                    @endforeach
                  </div>
                </div>

                {{-- Actions --}}
                <div class="cbt-actions-wrap">
                  <span class="badge-status" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">
                    Kunci: {{ $soal->kunci_jawaban ?: '-' }}
                  </span>
                  <a href="{{ route('admin.ppdb.soal.edit', $soal->id) }}" class="cbt-btn cbt-btn-secondary cbt-btn-sm">
                    Edit
                  </a>
                  <form action="{{ route('admin.ppdb.soal.destroy', $soal->id) }}" method="POST" onsubmit="return confirm('Hapus soal nomor {{ $soal->nomor_urut }}?');" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="cbt-btn cbt-btn-danger-outline cbt-btn-sm">
                      Hapus
                    </button>
                  </form>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      {{-- DAFTAR SOAL ESAI --}}
      <div class="cbt-card" style="padding: 0; overflow: hidden;">
        <div class="cbt-card-header">
          <div>
            <h2 class="cbt-card-title">Bagian II: Soal Esai / Uraian</h2>
            <div class="cbt-card-desc">Soal dikoreksi manual oleh panitia/guru penguji pada tab Koreksi Esai</div>
          </div>
          <a href="{{ route('admin.ppdb.soal.create', ['settingId' => $setting->id, 'type' => 'esai']) }}" class="cbt-btn cbt-btn-secondary cbt-btn-sm" style="color: #059669; font-weight: 700;">
            + Tambah Soal Esai
          </a>
        </div>

        @if($soalEsai->isEmpty())
          <div class="cbt-empty-state">
            <div class="cbt-empty-state-title">Belum ada soal Esai</div>
            <p class="cbt-empty-state-desc">Klik tombol di bawah untuk menambah soal esai/uraian.</p>
            <a href="{{ route('admin.ppdb.soal.create', ['settingId' => $setting->id, 'type' => 'esai']) }}" class="cbt-btn cbt-btn-success">
              + Tambah Soal Esai Sekarang
            </a>
          </div>
        @else
          <div>
            @foreach($soalEsai as $soal)
              <div class="cbt-soal-item">
                <div class="cbt-soal-num-badge cbt-num-esai" title="Nomor Urut {{ $soal->nomor_urut }}">
                  {{ $soal->nomor_urut }}
                </div>

                <div class="cbt-soal-content">
                  <div class="cbt-soal-text">
                    {!! nl2br(e($soal->pertanyaan)) !!}
                  </div>
                </div>

                {{-- Actions --}}
                <div class="cbt-actions-wrap">
                  <a href="{{ route('admin.ppdb.soal.edit', $soal->id) }}" class="cbt-btn cbt-btn-secondary cbt-btn-sm">
                    Edit
                  </a>
                  <form action="{{ route('admin.ppdb.soal.destroy', $soal->id) }}" method="POST" onsubmit="return confirm('Hapus soal nomor {{ $soal->nomor_urut }}?');" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="cbt-btn cbt-btn-danger-outline cbt-btn-sm">
                      Hapus
                    </button>
                  </form>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

    </div>
  </main>
</div>
</body>
</html>
