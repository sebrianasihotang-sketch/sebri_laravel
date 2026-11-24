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
            --space-panel: rgb(15 23 42 / 84%);
            --space-panel-raised: rgb(24 34 62 / 88%);
            --space-border: rgb(148 163 184 / 22%);
            --space-text: #e2e8f0;
            --space-muted: #aab8d2;
            --space-accent: #a5b4fc;
            --space-accent-strong: #818cf8;
            --bs-body-color: var(--space-text);
            --bs-body-bg: #080b18;
            --bs-border-color: var(--space-border);
            position: relative;
            isolation: isolate;
            min-height: 100vh;
            overflow-x: hidden;
            color: var(--space-text);
            background: #080b18;
        }

        body.space-body .card {
            border-color: var(--space-border);
            background: var(--space-panel);
            color: var(--space-text);
            backdrop-filter: blur(16px);
        }

        body.space-body .card-header,
        body.space-body .card-footer {
            border-color: var(--space-border);
            background: var(--space-panel-raised);
        }

        body.space-body .text-primary {
            color: var(--space-accent) !important;
        }

        body.space-body .text-secondary,
        body.space-body .text-muted {
            color: var(--space-muted) !important;
        }

        body.space-body .text-success {
            color: #5eead4 !important;
        }

        body.space-body .text-info {
            color: #67e8f9 !important;
        }

        body.space-body .text-danger {
            color: #fda4af !important;
        }

        body.space-body .form-control,
        body.space-body .form-select {
            border-color: var(--space-border);
            background-color: rgb(8 11 24 / 76%);
            color: var(--space-text);
        }

        body.space-body .form-control::placeholder {
            color: var(--space-muted);
        }

        body.space-body .form-control:focus,
        body.space-body .form-select:focus {
            border-color: var(--space-accent-strong);
            background-color: rgb(8 11 24 / 90%);
            color: #fff;
            box-shadow: 0 0 0 .22rem rgb(129 140 248 / 22%);
        }

        body.space-body .form-control[type="file"]::file-selector-button {
            border-color: var(--space-border);
            background: var(--space-panel-raised);
            color: var(--space-text);
        }

        body.space-body .form-select option {
            background: #0f172a;
            color: var(--space-text);
        }

        body.space-body .form-text {
            color: var(--space-muted);
        }

        body.space-body .form-check-input {
            border-color: var(--space-border);
            background-color: rgb(8 11 24 / 76%);
        }

        body.space-body .form-check-input:checked {
            border-color: var(--space-accent-strong);
            background-color: var(--space-accent-strong);
        }

        body.space-body .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: #6366f1;
            --bs-btn-border-color: #6366f1;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #7c3aed;
            --bs-btn-hover-border-color: #8b5cf6;
            --bs-btn-active-bg: #4f46e5;
            --bs-btn-active-border-color: #6366f1;
        }

        body.space-body .btn-outline-primary {
            --bs-btn-color: #c7d2fe;
            --bs-btn-border-color: rgb(165 180 252 / 55%);
            --bs-btn-hover-color: #0f172a;
            --bs-btn-hover-bg: #a5b4fc;
            --bs-btn-hover-border-color: #a5b4fc;
            --bs-btn-active-color: #0f172a;
            --bs-btn-active-bg: #a5b4fc;
            --bs-btn-active-border-color: #a5b4fc;
        }

        body.space-body .btn-outline-secondary {
            --bs-btn-color: #cbd5e1;
            --bs-btn-border-color: rgb(148 163 184 / 45%);
            --bs-btn-hover-color: #f8fafc;
            --bs-btn-hover-bg: rgb(148 163 184 / 20%);
            --bs-btn-hover-border-color: #94a3b8;
            --bs-btn-active-color: #f8fafc;
            --bs-btn-active-bg: rgb(148 163 184 / 28%);
            --bs-btn-active-border-color: #94a3b8;
        }

        body.space-body .btn-success {
            --bs-btn-color: #ccfbf1;
            --bs-btn-bg: #0f766e;
            --bs-btn-border-color: #2dd4bf;
            --bs-btn-hover-color: #042f2e;
            --bs-btn-hover-bg: #5eead4;
            --bs-btn-hover-border-color: #5eead4;
            --bs-btn-active-color: #042f2e;
            --bs-btn-active-bg: #5eead4;
            --bs-btn-active-border-color: #5eead4;
        }

        body.space-body .btn-outline-success {
            --bs-btn-color: #99f6e4;
            --bs-btn-border-color: rgb(45 212 191 / 55%);
            --bs-btn-hover-color: #042f2e;
            --bs-btn-hover-bg: #5eead4;
            --bs-btn-hover-border-color: #5eead4;
            --bs-btn-active-color: #042f2e;
            --bs-btn-active-bg: #5eead4;
            --bs-btn-active-border-color: #5eead4;
        }

        body.space-body .btn-outline-info {
            --bs-btn-color: #a5f3fc;
            --bs-btn-border-color: rgb(103 232 249 / 55%);
            --bs-btn-hover-color: #083344;
            --bs-btn-hover-bg: #67e8f9;
            --bs-btn-hover-border-color: #67e8f9;
            --bs-btn-active-color: #083344;
            --bs-btn-active-bg: #67e8f9;
            --bs-btn-active-border-color: #67e8f9;
        }

        body.space-body .btn-danger {
            --bs-btn-bg: #be123c;
            --bs-btn-border-color: #fb7185;
            --bs-btn-hover-bg: #e11d48;
            --bs-btn-hover-border-color: #fda4af;
            --bs-btn-active-bg: #9f1239;
            --bs-btn-active-border-color: #fb7185;
        }

        body.space-body .btn-outline-danger {
            --bs-btn-color: #fda4af;
            --bs-btn-border-color: rgb(251 113 133 / 55%);
            --bs-btn-hover-color: #4c0519;
            --bs-btn-hover-bg: #fda4af;
            --bs-btn-hover-border-color: #fda4af;
            --bs-btn-active-color: #4c0519;
            --bs-btn-active-bg: #fda4af;
            --bs-btn-active-border-color: #fda4af;
        }

        body.space-body .btn-outline-light {
            --bs-btn-color: #e0e7ff;
            --bs-btn-border-color: rgb(224 231 255 / 48%);
            --bs-btn-hover-color: #0f172a;
            --bs-btn-hover-bg: #e0e7ff;
            --bs-btn-hover-border-color: #e0e7ff;
        }

        body.space-body .badge.text-bg-info {
            border: 1px solid rgb(103 232 249 / 34%);
            background: rgb(34 211 238 / 14%) !important;
            color: #a5f3fc !important;
        }

        body.space-body .alert-success,
        body.space-body .alert-info,
        body.space-body .alert-danger {
            border-color: var(--space-border);
            color: var(--space-text);
        }

        body.space-body .alert-success {
            background: rgb(13 148 136 / 18%);
        }

        body.space-body .alert-info {
            background: rgb(8 145 178 / 18%);
        }

        body.space-body .alert-danger {
            background: rgb(190 18 60 / 18%);
        }

        body.space-body .modal-content {
            border: 1px solid var(--space-border);
            background: #111827;
            color: var(--space-text);
        }

        body.space-body .modal-header,
        body.space-body .modal-footer {
            border-color: var(--space-border);
        }

        body.space-body .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        body.space-body .pagination {
            --bs-pagination-color: #c7d2fe;
            --bs-pagination-bg: rgb(15 23 42 / 84%);
            --bs-pagination-border-color: var(--space-border);
            --bs-pagination-hover-color: #fff;
            --bs-pagination-hover-bg: rgb(129 140 248 / 22%);
            --bs-pagination-hover-border-color: rgb(165 180 252 / 50%);
            --bs-pagination-focus-color: #fff;
            --bs-pagination-focus-bg: rgb(129 140 248 / 22%);
            --bs-pagination-focus-box-shadow: 0 0 0 .2rem rgb(129 140 248 / 18%);
            --bs-pagination-active-color: #0f172a;
            --bs-pagination-active-bg: #a5b4fc;
            --bs-pagination-active-border-color: #a5b4fc;
            --bs-pagination-disabled-color: #64748b;
            --bs-pagination-disabled-bg: rgb(15 23 42 / 64%);
            --bs-pagination-disabled-border-color: var(--space-border);
        }

        body.space-body pre.bg-light {
            border: 1px solid var(--space-border);
            background: rgb(8 11 24 / 76%) !important;
            color: #c7d2fe;
        }

        body.space-body hr {
            color: var(--space-border);
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