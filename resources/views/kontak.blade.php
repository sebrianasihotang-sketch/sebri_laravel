@extends('layouts.app')

@section('title', 'Kontak')

@section('content')
<section class="card mx-auto" style="max-width: 760px;">
    <div class="card-body p-4 p-md-5">
        <p class="text-uppercase fw-semibold text-primary small mb-2">Kami siap membantu</p>
        <h1 class="display-6 fw-bold mb-3">Hubungi kami</h1>
        <p class="text-secondary mb-4">Ada pertanyaan atau masukan? Silakan hubungi melalui:</p>
        <div class="d-grid gap-3">
            <a href="mailto:sebriana@gmail.com" class="btn btn-outline-secondary text-start p-3">
                <span class="d-block small text-secondary">Email</span>
                <span class="fw-semibold">sebriana@gmail.com</span>
            </a>
            <a href="tel:+6282177772102" class="btn btn-outline-secondary text-start p-3">
                <span class="d-block small text-secondary">Telepon</span>
                <span class="fw-semibold">0821-7777-2102</span>
            </a>
        </div>
    </div>
</section>
@endsection
