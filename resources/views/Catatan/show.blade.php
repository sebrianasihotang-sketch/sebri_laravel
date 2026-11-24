@extends('layouts.app')

@section('title', $catatan->judul)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ $catatan->judul }}</span>
                <small class="text-muted">{{ $catatan->created_at->format('d F Y, H:i') }}</small>
            </div>
            <div class="card-body">
                <p class="card-text">{!! nl2br(e($catatan->isi)) !!}</p>
            </div>
            <div class="card-footer text-end">
                <a href="/catatan" class="btn btn-secondary">&larr; Kembali</a>
                <a href="/catatan/{{ $catatan->id }}/edit" class="btn btn-warning">Edit</a>
                <form action="#" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus catatan ini?')">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection