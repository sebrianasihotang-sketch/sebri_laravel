@extends('layouts.app')

@section('title', 'Hapus Catatan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header py-3">Konfirmasi Hapus Catatan</div>
            <div class="card-body p-4 p-md-5 text-center">
                <p class="text-danger fw-semibold text-uppercase small">Tindakan ini tidak dapat dibatalkan</p>
                <h1 class="h3 fw-bold">Hapus catatan ini?</h1>
                <p class="text-secondary mb-4">Catatan <strong>{{ $catatan->judul }}</strong> akan dihapus permanen.</p>
                <form action="/catatan/{{ $catatan->id }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, hapus catatan</button>
                </form>
                <a href="/catatan/{{ $catatan->id }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</div>
@endsection
