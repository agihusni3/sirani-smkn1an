{{-- Selector Bar Perangkat Pembelajaran --}}
<div class="akademik-card" style="margin-bottom:20px; background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px;">
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
    
    {{-- Form Filter & Switcher Mapel --}}
    <form method="GET" action="{{ url()->current() }}" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin:0;">
      
      {{-- Switch Semester --}}
      <div class="btn-group" role="group">
        <a href="{{ url()->current() }}?semester=1{{ request('perangkat_id') ? '&perangkat_id='.request('perangkat_id') : '' }}{{ request('mapel_id') ? '&mapel_id='.request('mapel_id') : '' }}"
           class="ak-btn {{ $semester == 1 ? 'ak-btn-primary' : 'ak-btn-secondary' }}"
           style="font-size:12px; padding:5px 12px; font-weight:700;">
          Sem 1 (Ganjil)
        </a>
        <a href="{{ url()->current() }}?semester=2{{ request('perangkat_id') ? '&perangkat_id='.request('perangkat_id') : '' }}{{ request('mapel_id') ? '&mapel_id='.request('mapel_id') : '' }}"
           class="ak-btn {{ $semester == 2 ? 'ak-btn-primary' : 'ak-btn-secondary' }}"
           style="font-size:12px; padding:5px 12px; font-weight:700;">
          Sem 2 (Genap)
        </a>
      </div>

      {{-- Dropdown Pilih Perangkat / Mapel --}}
      @if(isset($perangkatsList) && $perangkatsList->isNotEmpty())
        <div style="display:flex; align-items:center; gap:6px;">
          <label style="font-size:12px; font-weight:700; color:#475569; margin:0; white-space:nowrap;">
            <i class="bi bi-folder2-open text-primary me-1"></i> Perangkat:
          </label>
          <select name="perangkat_id" class="ak-select" onchange="this.form.submit()" style="font-size:12.5px; font-weight:700; padding:5px 10px; min-width:240px; height:32px;">
            @foreach($perangkatsList as $p)
              <option value="{{ $p->id }}" {{ ($activePerangkat?->id == $p->id) ? 'selected' : '' }}>
                {{ $p->mataPelajaran?->nama_mapel }} (Kls {{ $p->tingkat }}) · {{ $p->guru?->nama }}
              </option>
            @endforeach
          </select>
        </div>
      @endif

      @if(isset($showMapelSelector) && $showMapelSelector && isset($mapels))
        <div style="display:flex; align-items:center; gap:6px;">
          <label style="font-size:12px; font-weight:700; color:#475569; margin:0; white-space:nowrap;">
            <i class="bi bi-book-half text-primary me-1"></i> Pilih Mapel:
          </label>
          <select name="mapel_id" class="ak-select" onchange="this.form.submit()" style="font-size:12.5px; font-weight:700; padding:5px 10px; min-width:240px; height:32px;">
            @foreach($mapels as $m)
              <option value="{{ $m->id }}" {{ ($selectedMapel?->id == $m->id) ? 'selected' : '' }}>
                {{ $m->kode_mapel }} - {{ $m->nama_mapel }} ({{ $m->tingkat_label }})
              </option>
            @endforeach
          </select>
        </div>
      @endif
    </form>

    {{-- Info Status & Aksi Cepat --}}
    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
      @if($activePerangkat)
        @php
          $st = match($activePerangkat->status) {
            'disahkan' => ['label' => 'Disahkan Kepala Sekolah', 'color' => '#059669', 'bg' => '#ecfdf5', 'icon' => 'bi-patch-check-fill'],
            'diajukan' => ['label' => 'Menunggu Supervisi', 'color' => '#d97706', 'bg' => '#fef3c7', 'icon' => 'bi-hourglass-split'],
            'perlu_revisi' => ['label' => 'Perlu Revisi', 'color' => '#dc2626', 'bg' => '#fee2e2', 'icon' => 'bi-exclamation-octagon-fill'],
            default => ['label' => 'Draft Guru', 'color' => '#475569', 'bg' => '#f1f5f9', 'icon' => 'bi-file-earmark-text'],
          };
        @endphp
        <span class="badge" style="background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-weight:700; font-size:11.5px; padding:6px 12px; border-radius:8px;">
          <i class="bi {{ $st['icon'] }} me-1"></i> {{ $st['label'] }}
        </span>

        <a href="{{ route('akademik.perangkat.show', $activePerangkat->id) }}" class="ak-btn ak-btn-secondary" style="font-size:11.5px; padding:5px 10px;" title="Lihat Portofolio Lengkap">
          <i class="bi bi-box-arrow-up-right me-1"></i> Detail Portofolio
        </a>
      @endif

      @if(!empty($isGuru) && isset($myDistribusis) && $myDistribusis->isNotEmpty())
        @php
          $missingDists = $myDistribusis->unique('mata_pelajaran_id')->filter(function($dist) use ($perangkatsList) {
            return !$perangkatsList->contains(fn($p) => $p->mata_pelajaran_id == $dist->mata_pelajaran_id && $p->tingkat == ($dist->rombel?->tingkat ?? 'X'));
          });
        @endphp
        @if($missingDists->isNotEmpty())
          <div class="dropdown">
            <button class="ak-btn ak-btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" style="font-size:11.5px; padding:5px 10px; font-weight:700;">
              <i class="bi bi-plus-lg me-1"></i> Buat Perangkat Mapel Lain
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="font-size:12px; border-radius:10px; box-shadow:0 10px 25px rgba(0,0,0,0.1);">
              @foreach($missingDists as $dist)
                <li>
                  <form action="{{ route('akademik.perangkat.store') }}" method="POST" style="margin:0;">
                    @csrf
                    <input type="hidden" name="guru_id" value="{{ auth()->user()->guru_id }}">
                    <input type="hidden" name="mata_pelajaran_id" value="{{ $dist->mata_pelajaran_id }}">
                    <input type="hidden" name="distribusi_id" value="{{ $dist->id }}">
                    <input type="hidden" name="tingkat" value="{{ $dist->rombel?->tingkat ?? 'X' }}">
                    <input type="hidden" name="semester" value="{{ $semester }}">
                    <button type="submit" class="dropdown-item py-2" style="font-weight:600;">
                      <i class="bi bi-plus-circle text-primary me-2"></i>
                      {{ $dist->mataPelajaran?->nama_mapel }} (Kls {{ $dist->rombel?->tingkat }})
                    </button>
                  </form>
                </li>
              @endforeach
            </ul>
          </div>
        @endif
      @endif
    </div>

  </div>
</div>
