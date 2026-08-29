@if ($paginator->hasPages())
  <nav class="custom-pagination" role="navigation" aria-label="Navigasi Halaman">
    <div class="pagination-wrapper">
      
      {{-- Tombol Sebelumnya (Prev) --}}
      @if ($paginator->onFirstPage())
        <span class="page-btn disabled" aria-disabled="true">
          <i class="bi bi-chevron-left"></i> Prev
        </span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="page-btn">
          <i class="bi bi-chevron-left"></i> Prev
        </a>
      @endif

      {{-- Nomor-Nomor Halaman (1 2 3 4 5 ...) --}}
      @foreach ($elements as $element)
        {{-- Tanda Titik-Titik (...) --}}
        @if (is_string($element))
          <span class="page-btn dots" aria-disabled="true">{{ $element }}</span>
        @endif

        {{-- Tombol Angka Halaman --}}
        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span class="page-btn active" aria-current="page">{{ $page }}</span>
            @else
              <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
            @endif
          @endforeach
        @endif
      @endforeach

      {{-- Tombol Berikutnya (Next) --}}
      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="page-btn">
          Next <i class="bi bi-chevron-right"></i>
        </a>
      @else
        <span class="page-btn disabled" aria-disabled="true">
          Next <i class="bi bi-chevron-right"></i>
        </span>
      @endif

    </div>
  </nav>
@endif
