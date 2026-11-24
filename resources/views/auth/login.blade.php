@extends('layouts.app')

@section('title', 'Masuk')
@section('body_class', 'space-body')

@push('styles')
<style>
    .login-shell {
        display: grid;
        min-height: min(720px, calc(100vh - 180px));
        place-items: center;
        padding-block: 3rem;
    }

    .login-card {
        width: min(100%, 470px);
        border: 1px solid rgb(255 255 255 / 12%);
        border-radius: 1.5rem;
        background: rgb(15 23 42 / 78%);
        box-shadow: 0 24px 80px rgb(0 0 0 / 35%);
        color: #f8fafc;
        backdrop-filter: blur(18px);
    }

    .login-card .form-label {
        color: #e2e8f0;
    }

    .login-card .form-control {
        min-height: 48px;
        border-color: rgb(255 255 255 / 16%);
        background: rgb(15 23 42 / 72%);
        color: #f8fafc;
    }

    .login-card .form-control::placeholder {
        color: #94a3b8;
    }

    .login-card .form-control:focus {
        border-color: #a5b4fc;
        box-shadow: 0 0 0 .25rem rgb(129 140 248 / 20%);
    }

    .login-card .form-check-input {
        background-color: #0f172a;
        border-color: #64748b;
    }
</style>
@endpush

@section('content')
<main class="login-shell">
    <section class="login-card p-4 p-md-5">
        <p class="space-eyebrow mb-2">Selamat datang kembali</p>
        <h1 class="h2 fw-bold mb-2">Masuk ke akun</h1>
        <p class="text-white-50 mb-4">Lanjutkan perjalanan belajar Anda di ruang catatan.</p>

        <form action="/login" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="nama@email.com"
                    autocomplete="email"
                    required
                    autofocus
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
                    placeholder="Masukkan kata sandi"
                    autocomplete="current-password"
                    required
                >
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" @checked(old('remember'))>
                <label class="form-check-label text-white-50" for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 space-button-primary">Masuk</button>
        </form>

        <p class="text-center text-white-50 mt-4 mb-0">
            Belum punya akun?
            <a href="{{ route('register') }}" class="link-light fw-semibold">Daftar</a>
        </p>
    </section>
</main>
@endsection
