@extends('layouts.app')

@section('title', $judul)
@section('body_class', 'space-body')

@push('styles')
<style>
    .space-hero {
        display: flex;
        min-height: min(690px, calc(100vh - 180px));
        align-items: center;
        padding-block: 4rem;
    }

    .space-title {
        max-width: 760px;
        margin-block: 1.25rem;
        font-size: clamp(3rem, 7vw, 6.4rem);
        font-weight: 700;
        letter-spacing: -.065em;
        line-height: .98;
    }

    .space-title span {
        color: #a5b4fc;
    }

    .space-copy {
        max-width: 560px;
        color: #cbd5e1;
        font-size: clamp(1rem, 2vw, 1.2rem);
        line-height: 1.8;
    }

    .space-visual {
        position: relative;
        display: grid;
        min-height: 390px;
        place-items: center;
    }

    .space-planet {
        position: relative;
        z-index: 2;
        width: clamp(155px, 22vw, 245px);
        aspect-ratio: 1;
        border-radius: 50%;
        background:
            radial-gradient(circle at 30% 25%, rgb(255 255 255 / 90%), transparent 4%),
            radial-gradient(circle at 35% 30%, #c4b5fd 0%, #818cf8 22%, #4f46e5 55%, #1e1b4b 78%);
        box-shadow: inset -24px -24px 50px rgb(8 11 24 / 65%), 0 0 80px rgb(99 102 241 / 45%);
        animation: planet-float 6s ease-in-out infinite;
    }

    .space-orbit {
        position: absolute;
        width: min(100%, 410px);
        height: 145px;
        border: 1px solid rgb(199 210 254 / 38%);
        border-radius: 50%;
        transform: rotate(-28deg);
    }

    .space-orbit--outer {
        width: min(112%, 475px);
        height: 215px;
        border-color: rgb(125 211 252 / 24%);
        transform: rotate(38deg);
    }

    .space-moon {
        position: absolute;
        top: 21%;
        right: 14%;
        width: 27px;
        aspect-ratio: 1;
        border-radius: 50%;
        background: #e0e7ff;
        box-shadow: 0 0 28px rgb(224 231 255 / 80%);
    }

    .space-caption {
        position: absolute;
        right: 0;
        bottom: 1rem;
        color: #94a3b8;
        font-size: .7rem;
        letter-spacing: .24em;
    }

    @keyframes planet-float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-12px); }
    }

    @media (prefers-reduced-motion: reduce) {
        .space-planet {
            animation: none;
        }
    }

    @media (max-width: 767.98px) {
        .space-hero {
            min-height: auto;
            padding-block: 3rem 1rem;
        }

        .space-visual {
            min-height: 300px;
            overflow: hidden;
        }

        .space-caption {
            right: 1rem;
            bottom: 0;
        }
    }
</style>
@endpush

@section('content')
<main class="space-hero">
    <div class="row w-100 align-items-center g-5">
        <div class="col-lg-7">
            <p class="space-eyebrow mb-0">{{ $hari }} &middot; Les Laravel</p>
            <h1 class="space-title">Ruang untuk ide <span>tanpa batas.</span></h1>
            <p class="space-copy">
                Simpan catatan, kumpulkan inspirasi, dan jelajahi perjalanan belajar Laravel Anda
                di bawah langit penuh bintang.
            </p>
            <div class="d-flex flex-wrap gap-3 mt-4">
                <a href="/catatan" class="btn btn-lg btn-primary space-button-primary px-4">Jelajahi Catatan</a>
                @guest
                    <a href="{{ route('login') }}" class="btn btn-lg btn-outline-light px-4">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-lg btn-outline-light px-4">Buat akun</a>
                @else
                    <a href="/catatan/tambah" class="btn btn-lg btn-outline-light px-4">Tulis Catatan</a>
                @endguest
            </div>
            <p class="text-white-50 mt-4 mb-0">Dibuat untuk {{ $nama }}</p>
        </div>
        <div class="col-lg-5">
            <div class="space-visual" aria-hidden="true">
                <div class="space-orbit"></div>
                <div class="space-orbit space-orbit--outer"></div>
                <div class="space-planet"></div>
                <div class="space-moon"></div>
                <span class="space-caption">CATATAN DARI SEMESTA</span>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    const spaceBody = document.body;
    let swayTimeout;

    spaceBody.addEventListener('pointerdown', () => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        spaceBody.classList.remove('space-wiggle');
        void spaceBody.offsetWidth;
        spaceBody.classList.add('space-wiggle');

        window.clearTimeout(swayTimeout);
        swayTimeout = window.setTimeout(() => spaceBody.classList.remove('space-wiggle'), 650);
    }, { passive: true });
</script>
@endpush
