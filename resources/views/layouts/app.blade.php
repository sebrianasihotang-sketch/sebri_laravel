<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Aplikasi Laravel')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            background: linear-gradient(145deg, #f8fafc, #edf2fb);
            color: #172033;
        }

        body > .container.my-4 {
            flex: 1;
        }

        .navbar {
            position: relative;
            z-index: 10;
            box-shadow: 0 8px 26px rgb(15 23 42 / 14%);
        }

        .navbar-brand,
        .navbar-nav .nav-link {
            border-radius: .75rem;
            transition: transform .2s ease, box-shadow .2s ease, color .2s ease, background-color .2s ease;
        }

        .navbar-brand:hover {
            transform: translateY(-2px);
            text-shadow: 0 5px 16px rgb(165 180 252 / 60%);
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus-visible {
            transform: translateY(-3px) scale(1.025);
            background: rgb(255 255 255 / 12%);
            box-shadow: 0 7px 18px rgb(0 0 0 / 22%);
            color: #fff;
        }

        .card {
            overflow: hidden;
            border: 1px solid #e6eaf2;
            border-radius: 1.15rem;
            box-shadow: 0 10px 28px rgb(15 23 42 / 8%);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 42px rgb(15 23 42 / 15%);
        }

        .card-header {
            border-bottom-color: #e8edf5;
            background: linear-gradient(135deg, #f8fafc, #eef2ff);
            font-weight: 600;
        }

        .card-footer {
            border-top-color: #e8edf5;
            background: #fff;
        }

        .btn {
            border-radius: .75rem;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .btn:not(:disabled):hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgb(15 23 42 / 16%);
        }

        .form-control {
            border-color: #dce3ee;
            border-radius: .75rem;
        }

        .form-control:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 .22rem rgb(129 140 248 / 18%);
        }

        .site-footer {
            background: linear-gradient(110deg, #0b1120, #18213a) !important;
        }

        body.space-body {
            position: relative;
            isolation: isolate;
            min-height: 100vh;
            overflow-x: hidden;
            color: #f8fafc;
            background: #080b18;
        }

        body.space-body::before,
        body.space-body::after {
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            content: "";
        }

        body.space-body::before {
            background:
                radial-gradient(ellipse at 18% 28%, rgb(56 189 248 / 16%), transparent 36%),
                radial-gradient(ellipse at 80% 58%, rgb(129 140 248 / 19%), transparent 42%),
                linear-gradient(145deg, #080b18 0%, #11132b 55%, #080b18 100%);
        }

        body.space-body::after {
            inset: -35%;
            opacity: .58;
            background-image:
                radial-gradient(1px 1px at 24px 36px, #fff 98%, transparent),
                radial-gradient(1.5px 1.5px at 82px 116px, rgb(191 219 254 / 90%) 98%, transparent),
                radial-gradient(1px 1px at 154px 68px, rgb(255 255 255 / 80%) 98%, transparent),
                radial-gradient(2px 2px at 222px 188px, rgb(224 231 255 / 80%) 98%, transparent);
            background-size: 260px 230px, 310px 280px, 190px 210px, 370px 330px;
            animation: star-drift 100s linear infinite, star-twinkle 5s ease-in-out infinite alternate;
        }

        body.space-body.space-wiggle::after {
            animation: star-sway .65s ease-in-out;
        }

        .space-navbar {
            border-bottom: 1px solid rgb(255 255 255 / 9%);
            background: rgb(8 11 24 / 78%) !important;
            backdrop-filter: blur(16px);
        }

        .space-body .container {
            position: relative;
            z-index: 1;
        }

        .space-eyebrow {
            color: #a5b4fc;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .2em;
            text-transform: uppercase;
        }

        .space-button-primary {
            border: 0;
            background: linear-gradient(110deg, #6366f1, #8b5cf6);
            box-shadow: 0 12px 36px rgb(99 102 241 / 30%);
        }

        @keyframes star-drift {
            to { transform: translate3d(8%, 12%, 0); }
        }

        @keyframes star-twinkle {
            from { opacity: .3; }
            to { opacity: .8; }
        }

        @keyframes star-sway {
            0%, 100% { transform: translate3d(0, 0, 0) rotate(0); }
            25% { transform: translate3d(-1.5%, .5%, 0) rotate(-.6deg); }
            50% { transform: translate3d(1.2%, -1%, 0) rotate(.5deg); }
            75% { transform: translate3d(-.7%, .8%, 0) rotate(-.3deg); }
        }

        @media (prefers-reduced-motion: reduce) {
            body.space-body::after,
            body.space-body.space-wiggle::after {
                animation: none;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="@yield('body_class', 'space-body')">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark space-navbar">
        <div class="container">
            <a class="navbar-brand" href="/">Les Laravel</a>
            <div class="navbar-nav flex-row flex-wrap align-items-center gap-2 gap-lg-3">
                <a class="nav-link" href="/">Beranda</a>
                <a class="nav-link" href="/halo">Halo</a>
                <a class="nav-link" href="/about">Tentang</a>
                <a class="nav-link" href="/catatan">Catatan</a>
                <a class="nav-link" href="{{ route('kontak') }}">Kontak</a>
                @guest
                    <a class="nav-link" href="{{ route('login') }}">Masuk</a>
                    <a class="nav-link" href="{{ route('register') }}">Daftar</a>
                @else
                    <span class="nav-link">Halo, {{ auth()->user()->name }}!</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link">Keluar</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    <div class="container my-4">
        @if (session('sukses'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('sukses') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="site-footer bg-dark text-light text-center py-3 mt-5">
        <small>&copy; {{ date('Y') }} - Belajar Laravel 30 Hari</small>
    </footer>

    <div class="modal fade" id="deleteNoteModal" tabindex="-1" aria-labelledby="deleteNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="deleteNoteModalLabel">Konfirmasi hapus</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">Yakin ingin menghapus <strong id="deleteNoteTitle"></strong>?</p>
                    <p class="text-danger small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteNoteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus catatan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const deleteNoteModal = document.getElementById('deleteNoteModal');

        deleteNoteModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;

            if (!button) {
                return;
            }

            document.getElementById('deleteNoteTitle').textContent = button.dataset.noteTitle || 'catatan ini';
            document.getElementById('deleteNoteForm').action = button.dataset.deleteUrl;
        });
    </script>

    @stack('scripts')
</body>
</html>