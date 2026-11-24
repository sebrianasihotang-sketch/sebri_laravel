@extends('layouts.app')

@section('title', 'Tentang Kami')

@section('content')
<section class="card mx-auto" style="max-width: 760px;">
    <div class="card-body p-4 p-md-5">
        <p class="text-uppercase fw-semibold text-primary small mb-2">Tentang ruang ini</p>
        <h1 class="display-6 fw-bold mb-3">Belajar, bertumbuh, dan terus mencoba.</h1>
        <p class="lead text-secondary">
            Les Laravel adalah ruang belajar sederhana untuk mengumpulkan catatan dan merayakan
            setiap langkah kecil dalam perjalanan membangun aplikasi.
        </p>
        <p class="text-secondary mb-4">Dibuat untuk pembelajaran 30 hari dengan Laravel.</p>
        <div class="d-flex flex-wrap gap-2">
            <a href="/catatan" class="btn btn-primary">Jelajahi Catatan</a>
            <a href="/" class="btn btn-outline-secondary">Kembali ke Beranda</a>
        </div>
    </div>
</section>
@endsection
