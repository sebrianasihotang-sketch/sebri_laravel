@extends('layouts.app')

@section('title', $catatan->judul)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-start gap-2 py-3">
                <div>
                    <h1 class="h4 mb-1">{{ $catatan->judul }}</h1>
                    <small class="text-muted">{{ $catatan->created_at->locale('id')->isoFormat('dddd, D MMMM Y [pukul] HH:mm') }}</small>
                    @if ($catatan->user)
                        <small class="d-block text-success">{{ $catatan->user->name }}</small>
                    @endif
                </div>
                @if ($catatan->lampiran)
                    <span class="badge text-bg-info">Ada lampiran</span>
                @endif
            </div>
            <div class="card-body p-4 p-md-5">
                <p class="card-text text-secondary lh-lg mb-0">{!! nl2br(e($catatan->isi)) !!}</p>
                @if ($catatan->lampiran && $attachmentUrl)
                    <hr class="my-4">
                    <h2 class="h6 fw-bold">Lampiran</h2>
                    <a href="{{ $attachmentUrl }}" class="btn btn-outline-secondary btn-sm mt-2">
                        <i class="fa-solid fa-download me-1" aria-hidden="true"></i>Unduh lampiran
                    </a>
                @endif
            </div>
            <div class="card-footer d-flex flex-wrap justify-content-end gap-2 p-3">
                <a href="{{ $catatan->is_archived ? route('catatan.arsip') : route('catatan.index') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i>Kembali
                </a>
                @if ($catatan->is_archived)
                    <form action="{{ route('catatan.unarchive', $catatan) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-rotate-left me-1" aria-hidden="true"></i>Pulihkan
                        </button>
                    </form>
                @else
                    <form action="{{ route('catatan.archive', $catatan) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-box-archive me-1" aria-hidden="true"></i>Arsipkan
                        </button>
                    </form>
                @endif
                <a href="{{ route('catatan.edit', $catatan) }}" class="btn btn-primary">
                    <i class="fa-solid fa-pen-to-square me-1" aria-hidden="true"></i>Edit
                </a>
                <button
                    type="button"
                    class="btn btn-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteNoteModal"
                    data-delete-url="{{ route('catatan.destroy', $catatan) }}"
                    data-note-title="{{ $catatan->judul }}"
                >
                    <i class="fa-solid fa-trash me-1" aria-hidden="true"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection