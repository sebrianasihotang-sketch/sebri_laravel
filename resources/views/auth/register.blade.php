@extends('layouts.app')

@section('title', 'Buat Akun')
@section('body_class', 'space-body')

@push('styles')
<style>
    .auth-shell {
        display: grid;
        min-height: min(760px, calc(100vh - 180px));
        place-items: center;
        padding-block: 3rem;
    }

    .auth-card {
        width: min(100%, 500px);
        border: 1px solid rgb(255 255 255 / 12%);
        border-radius: 1.5rem;
        background: rgb(15 23 42 / 82%);
        box-shadow: 0 24px 80px rgb(0 0 0 / 35%);
        color: #f8fafc;
        backdrop-filter: blur(18px);
    }

    .auth-card .form-label {
        color: #e2e8f0;
    }

    .auth-card .form-control {
        min-height: 48px;
        border-color: rgb(255 255 255 / 16%);
        background: rgb(15 23 42 / 72%);
        color: #f8fafc;
    }

    .auth-card .form-control::placeholder {
        color: #94a3b8;
    }

    .auth-card .form-control:focus {
        border-color: #a5b4fc;
        box-shadow: 0 0 0 .25rem rgb(129 140 248 / 20%);
    }

    .auth-card .form-check-input {
        background-color: #0f172a;
        border-color: #64748b;
    }
</style>
@endpush

@section('content')
<main class="auth-shell">
    <section class="auth-card p-4 p-md-5">
        <p class="space-eyebrow mb-2">Mulai perjalanan Anda</p>
        <h1 class="h2 fw-bold mb-2">Buat akun</h1>
        <p class="text-white-50 mb-4">Simpan catatan dan inspirasi di satu tempat.</p>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    class="form-control @error('name') is-invalid @enderror"
                    autocomplete="name"
                    required
                    autofocus
                >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    autocomplete="email"
                    required
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Kata sandi</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    autocomplete="new-password"
                    required
                >
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Konfirmasi kata sandi</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control"
                    autocomplete="new-password"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 space-button-primary">Buat akun</button>
        </form>

        <p class="text-center text-white-50 mt-4 mb-0">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="link-light fw-semibold">Masuk</a>
        </p>
    </section>
</main>
@endsection
