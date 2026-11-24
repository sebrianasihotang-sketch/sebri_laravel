@extends('layouts.app')

@section('title', 'Tambah Catatan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header py-3">Catatan baru</div>
            <div class="card-body p-4">
                <h1 class="h3 fw-bold mb-1">Tulis ide Anda</h1>
                <p class="text-secondary mb-4">Simpan ide agar mudah ditemukan kembali.</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('catatan.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul</label>
                        <input
                            type="text"
                            name="judul"
                            id="judul"
                            class="form-control @error('judul') is-invalid @enderror"
                            value="{{ old('judul') }}"
                            maxlength="255"
                            required
                        >
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="isi" class="form-label">Isi Catatan</label>
                        <textarea
                            name="isi"
                            id="isi"
                            class="form-control @error('isi') is-invalid @enderror"
                            rows="6"
                            required
                        >{{ old('isi') }}</textarea>
                        @error('isi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="lampiran" class="form-label">Lampiran (opsional)</label>
                        <input
                            type="file"
                            name="lampiran"
                            id="lampiran"
                            class="form-control @error('lampiran') is-invalid @enderror"
                            accept=".jpg,.jpeg,.png,.pdf,.txt"
                        >
                        <div class="form-text">JPG, PNG, PDF, atau TXT; ukuran maksimal 2 MB.</div>
                        @error('lampiran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Simpan catatan</button>
                    </div>
                    <a href="/catatan" class="mt-3 d-block text-center text-decoration-none">&larr; Kembali ke daftar</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection