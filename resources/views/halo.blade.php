@extends('layouts.app')

@section('title', 'Halo Dunia')

@section('content')
<section class="card mx-auto text-center" style="max-width: 760px;">
    <div class="card-body p-4 p-md-5">
        <p class="text-uppercase fw-semibold text-primary small mb-2">Status perjalanan belajar</p>
        <h1 class="display-5 fw-bold">{{ $pesan }}</h1>
        <p class="lead text-secondary mb-4">Dikirim dari controller pada pukul {{ $timestamp }}</p>
        <a href="/" class="btn btn-primary">Kembali ke Beranda</a>
    </div>
</section>
@endsection
