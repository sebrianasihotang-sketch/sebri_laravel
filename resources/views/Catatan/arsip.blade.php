@extends('layouts.app')

@section('title', 'Catatan Arsip')

@section('content')
<div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
        <p class="text-uppercase fw-semibold text-secondary small mb-1">Catatan tersimpan</p>
        <h1 class="display-6 fw-bold mb-1">Catatan Arsip</h1>
        <p class="text-secondary mb-0">Anda memiliki {{ $jumlahArsip }} catatan di arsip.</p>
    </div>
    <a href="{{ route('catatan.index') }}" class="btn btn-outline-primary">
        <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i>Aktif ({{ $jumlahAktif }})
    </a>
</div>

<form method="GET" action="{{ route('catatan.arsip') }}" class="row g-2 align-items-end mb-4">
    <div class="col-md">
        <label for="cari" class="form-label">Cari di arsip</label>
        <input
            type="search"
            name="cari"
            id="cari"
            class="form-control"
            placeholder="Cari judul atau isi..."
            value="{{ $filters['cari'] ?? '' }}"
        >
    </div>
    <div class="col-sm-6 col-md-3">
        <label for="urut" class="form-label">Urutkan</label>
        <select name="urut" id="urut" class="form-select">
            <option value="terbaru" @selected(($filters['urut'] ?? 'terbaru') === 'terbaru')>Terbaru</option>
            <option value="terlama" @selected(($filters['urut'] ?? 'terbaru') === 'terlama')>Terlama</option>
        </select>
    </div>
    <div class="col-sm-auto d-flex gap-2">
        <button type="submit" class="btn btn-outline-primary">Terapkan</button>
        @if (filled($filters['cari'] ?? null) || ($filters['urut'] ?? 'terbaru') !== 'terbaru')
            <a href="{{ route('catatan.arsip') }}" class="btn btn-outline-secondary">Reset</a>
        @endif
    </div>
</form>

@if ($catatan->count() > 0)
    <div class="row g-3">
        @foreach ($catatan as $item)
            <div class="col-md-6">
                <article class="card h-100 border-secondary">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                        <h2 class="h5 fw-semibold mb-0">{{ $item->judul }}</h2>
                        <small class="text-muted">{{ $item->created_at->locale('id')->diffForHumans() }}</small>
                    </div>
                    <div class="card-body p-4">
                        <p class="card-text text-secondary">{{ Str::limit($item->isi, 160) }}</p>
                        @if ($item->lampiran)
                            <span class="badge text-bg-info mb-3">Ada lampiran</span>
                        @endif
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('catatan.show', $item) }}" class="btn btn-sm btn-outline-secondary" title="Lihat">
                                <i class="fa-solid fa-eye me-1" aria-hidden="true"></i>Lihat
                            </a>
                            <form action="{{ route('catatan.unarchive', $item) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" title="Pulihkan">
                                    <i class="fa-solid fa-rotate-left me-1" aria-hidden="true"></i>Pulihkan
                                </button>
                            </form>
                            <button
                                type="button"
                                class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteNoteModal"
                                data-delete-url="{{ route('catatan.destroy', $item) }}"
                                data-note-title="{{ $item->judul }}"
                                title="Hapus permanen"
                            >Hapus permanen</button>
                        </div>
                    </div>
                </article>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $catatan->links('pagination::bootstrap-5') }}
    </div>
@else
    <div class="alert alert-info">
        Tidak ada catatan arsip yang cocok.
        <a href="{{ route('catatan.index') }}" class="alert-link ms-2">Kembali ke catatan aktif</a>
    </div>
@endif
@endsection
