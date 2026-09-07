@extends('layouts.app')

@section('title', 'Kelola Bank Soal CBT PPDB')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 24px 20px;">
  
  {{-- BREADCRUMB & HEADER --}}
  <div style="margin-bottom: 24px;">
    <div style="font-size: 13px; color: var(--text-3, #64748b); margin-bottom: 8px;">
      <a href="{{ route('admin.ppdb.seleksi', ['tab' => 'pengaturan']) }}" style="color: #2563eb; text-decoration: none;">PPDB Seleksi</a>
      <span style="margin: 0 6px;">/</span>
      <span style="color: var(--text-1, #1e293b); font-weight: 600;">Bank Soal CBT</span>
    </div>
    
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
      <div>
        <h1 style="font-size: 22px; font-weight: 800; color: var(--text-1, #0f172a); margin: 0 0 6px;">
          Bank Soal CBT PPDB
        </h1>
        <div style="font-size: 13px; color: var(--text-2, #475569);">
          {{ $setting->judul_ujian }} &bull; Durasi: {{ $setting->durasi_menit }} Menit &bull; Total Soal: {{ $soalPg->count() + $soalEsai->count() }} Butir
        </div>
      </div>
      
      <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.ppdb.seleksi', ['tab' => 'pengaturan']) }}" class="btn" style="background: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-weight: 700; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px;">
          &larr; Kembali ke Seleksi
        </a>
        <a href="{{ route('admin.ppdb.soal.create', $setting->id) }}" class="btn" style="background: #2563eb; color: #ffffff; font-weight: 700; padding: 8px 18px; border-radius: 8px; text-decoration: none; font-size: 13px;">
          + Tambah Soal
        </a>
      </div>
    </div>
  </div>

  {{-- ALERTS --}}
  @if(session('success'))
    <div style="padding: 12px 16px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; color: #065f46; font-size: 13px; font-weight: 600; margin-bottom: 20px;">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div style="padding: 12px 16px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; color: #991b1b; font-size: 13px; font-weight: 600; margin-bottom: 20px;">
      {{ session('error') }}
    </div>
  @endif

  {{-- STATISTIK SOAL --}}
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
      <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Pilihan Ganda (PG)</div>
      <div style="font-size: 24px; font-weight: 800; color: #2563eb; margin-top: 4px;">{{ $soalPg->count() }} <span style="font-size: 13px; font-weight: 500; color: #64748b;">butir</span></div>
      <div style="font-size: 12px; color: #64748b; margin-top: 2px;">Bobot: {{ $setting->bobot_pg }}% &bull; Koreksi Otomatis</div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
      <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Soal Esai</div>
      <div style="font-size: 24px; font-weight: 800; color: #059669; margin-top: 4px;">{{ $soalEsai->count() }} <span style="font-size: 13px; font-weight: 500; color: #64748b;">butir</span></div>
      <div style="font-size: 12px; color: #64748b; margin-top: 2px;">Bobot: {{ $setting->bobot_esai }}% &bull; Koreksi Manual</div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
      <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Pengacakan Soal</div>
      <div style="font-size: 15px; font-weight: 700; color: #0f172a; margin-top: 8px;">Aktif Otomatis</div>
      <div style="font-size: 12px; color: #64748b; margin-top: 2px;">Urutan soal diacak per siswa saat ujian</div>
    </div>
  </div>

  {{-- TABEL SOAL PILIHAN GANDA --}}
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 28px; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
      <div>
        <h2 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0;">Bagian I: Soal Pilihan Ganda (PG)</h2>
        <div style="font-size: 12px; color: #64748b;">Soal dikoreksi secara otomatis oleh sistem berdasarkan kunci jawaban</div>
      </div>
      <a href="{{ route('admin.ppdb.soal.create', ['settingId' => $setting->id, 'type' => 'pg']) }}" style="font-size: 12px; font-weight: 700; color: #2563eb; text-decoration: none;">
        + Tambah Soal PG
      </a>
    </div>

    @if($soalPg->isEmpty())
      <div style="padding: 40px 20px; text-align: center; color: #64748b;">
        <div style="font-size: 14px; font-weight: 600; margin-bottom: 6px; color: #334155;">Belum ada soal Pilihan Ganda</div>
        <p style="font-size: 13px; margin: 0 0 16px;">Klik tombol di bawah untuk mulai menginput soal pertama.</p>
        <a href="{{ route('admin.ppdb.soal.create', ['settingId' => $setting->id, 'type' => 'pg']) }}" style="display: inline-block; background: #2563eb; color: #ffffff; font-weight: 700; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px;">
          + Tambah Soal PG Sekarang
        </a>
      </div>
    @else
      <div style="display: flex; flex-direction: column;">
        @foreach($soalPg as $soal)
          <div style="padding: 18px 20px; border-bottom: 1px solid #f1f5f9; display: flex; gap: 16px; align-items: flex-start;">
            <div style="width: 36px; height: 36px; background: #eff6ff; color: #2563eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; flex-shrink: 0;">
              {{ $soal->nomor_urut }}
            </div>

            <div style="flex: 1;">
              <div style="font-size: 14px; color: #1e293b; line-height: 1.5; font-weight: 600; margin-bottom: 10px;">
                {!! nl2br(e($soal->pertanyaan)) !!}
              </div>

              {{-- Opsi Jawaban --}}
              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 6px; font-size: 12.5px;">
                @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                  @php $val = $soal->{'opsi_' . strtolower($opt)}; @endphp
                  @if(!empty($val))
                    <div style="padding: 6px 10px; border-radius: 6px; {{ $soal->kunci_jawaban === $opt ? 'background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; font-weight: 700;' : 'background: #f8fafc; border: 1px solid #e2e8f0; color: #475569;' }}">
                      <span style="font-weight: 800; margin-right: 4px;">{{ $opt }}.</span> {{ $val }}
                      @if($soal->kunci_jawaban === $opt)
                        <span style="font-size: 10px; background: #059669; color: #fff; padding: 1px 6px; border-radius: 4px; margin-left: 4px;">Kunci</span>
                      @endif
                    </div>
                  @endif
                @endforeach
              </div>
            </div>

            {{-- Actions --}}
            <div style="display: flex; gap: 8px; flex-shrink: 0; align-items: center;">
              <span style="font-size: 11px; background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 6px; font-weight: 700;">
                Kunci: {{ $soal->kunci_jawaban ?: '-' }}
              </span>
              <a href="{{ route('admin.ppdb.soal.edit', $soal->id) }}" style="padding: 6px 12px; background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none;">
                Edit
              </a>
              <form action="{{ route('admin.ppdb.soal.destroy', $soal->id) }}" method="POST" onsubmit="return confirm('Hapus soal nomor {{ $soal->nomor_urut }}?');" style="margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: 6px 12px; background: #fff; border: 1px solid #fecaca; color: #dc2626; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">
                  Hapus
                </button>
              </form>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- TABEL SOAL ESAI --}}
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 24px; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
      <div>
        <h2 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0;">Bagian II: Soal Esai / Uraian</h2>
        <div style="font-size: 12px; color: #64748b;">Soal dikoreksi manual oleh panitia/guru penguji di tab Koreksi Esai</div>
      </div>
      <a href="{{ route('admin.ppdb.soal.create', ['settingId' => $setting->id, 'type' => 'esai']) }}" style="font-size: 12px; font-weight: 700; color: #059669; text-decoration: none;">
        + Tambah Soal Esai
      </a>
    </div>

    @if($soalEsai->isEmpty())
      <div style="padding: 40px 20px; text-align: center; color: #64748b;">
        <div style="font-size: 14px; font-weight: 600; margin-bottom: 6px; color: #334155;">Belum ada soal Esai</div>
        <p style="font-size: 13px; margin: 0 0 16px;">Klik tombol di bawah untuk menambah soal esai/uraian.</p>
        <a href="{{ route('admin.ppdb.soal.create', ['settingId' => $setting->id, 'type' => 'esai']) }}" style="display: inline-block; background: #059669; color: #ffffff; font-weight: 700; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px;">
          + Tambah Soal Esai Sekarang
        </a>
      </div>
    @else
      <div style="display: flex; flex-direction: column;">
        @foreach($soalEsai as $soal)
          <div style="padding: 18px 20px; border-bottom: 1px solid #f1f5f9; display: flex; gap: 16px; align-items: flex-start;">
            <div style="width: 36px; height: 36px; background: #ecfdf5; color: #059669; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; flex-shrink: 0;">
              {{ $soal->nomor_urut }}
            </div>

            <div style="flex: 1;">
              <div style="font-size: 14px; color: #1e293b; line-height: 1.5; font-weight: 600;">
                {!! nl2br(e($soal->pertanyaan)) !!}
              </div>
            </div>

            {{-- Actions --}}
            <div style="display: flex; gap: 8px; flex-shrink: 0; align-items: center;">
              <a href="{{ route('admin.ppdb.soal.edit', $soal->id) }}" style="padding: 6px 12px; background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none;">
                Edit
              </a>
              <form action="{{ route('admin.ppdb.soal.destroy', $soal->id) }}" method="POST" onsubmit="return confirm('Hapus soal nomor {{ $soal->nomor_urut }}?');" style="margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: 6px 12px; background: #fff; border: 1px solid #fecaca; color: #dc2626; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">
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
@endsection
