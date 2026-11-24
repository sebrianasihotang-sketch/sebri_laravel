@extends('layouts.app')

@section('title', 'Impor Catatan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card">
            <div class="card-header py-3">Impor Catatan dari CSV</div>
            <div class="card-body p-4">
                <h1 class="h3 fw-bold">Tambahkan catatan dari file</h1>
                <p class="text-secondary">
                    File harus memiliki kolom <strong>Judul</strong> dan <strong>Isi</strong>.
                    Maksimal 2 MB dan 5.000 catatan per impor.
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('catatan.import.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="form-label">Pilih file CSV</label>
                        <input
                            type="file"
                            name="file"
                            id="file"
                            class="form-control @error('file') is-invalid @enderror"
                            accept=".csv,.txt"
                            required
                        >
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">Impor sekarang</button>
                        <a href="{{ route('catatan.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>

                <hr>
                <p class="small text-secondary mb-2">Contoh format CSV:</p>
                <pre class="small bg-light rounded p-3 mb-0"><code>Judul,Isi,Tanggal Dibuat
"Ide proyek","Belajar Laravel",2026-02-10 10:20:00</code></pre>
            </div>
        </div>
    </div>
</div>
@endsection
