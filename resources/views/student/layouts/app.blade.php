<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Panel')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #ffffff;
            --main-bg: #f9fafb;
            --card-bg: #ffffff;
            --accent: #7c3aed;
            --accent-soft: #f5f3ff;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --border-color: #f3f4f6;
            --sidebar-width: 280px;
        }

        [data-bs-theme="dark"] {
            --sidebar-bg: #111827;
            --main-bg: #030712;
            --card-bg: #1f2937;
            --text-dark: #f9fafb;
            --text-muted: #9ca3af;
            --border-color: #374151;
            --accent-soft: rgba(124, 58, 237, 0.2);
        }

        body {
            font-family: "Inter", sans-serif;
            background: var(--main-bg);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 30px 24px;
            display: flex;
            flex-direction: column;
            z-index: 1050;
            transition: .3s;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
            text-decoration: none;
            color: var(--text-dark);
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 14px;
            margin-bottom: 8px;
            font-weight: 500;
            transition: .3s;
        }

        .nav-link-custom:hover {
            background: var(--accent-soft);
            color: var(--accent);
        }

        .nav-link-custom.active {
            background: var(--accent);
            color: #fff !important;
            box-shadow: 0 10px 15px -3px rgba(124, 58, 237, 0.3);
        }

        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .header-nav {
            background: var(--sidebar-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 16px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .content-wrap {
            padding: 24px;
        }

        .content-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .03);
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 24px;
            height: 100%;
        }

        .theme-btn {
            width: 42px;
            height: 42px;
            border: none;
            border-radius: 12px;
            background: var(--accent-soft);
            color: var(--accent);
        }

        .mobile-toggle {
            display: none;
            width: 42px;
            height: 42px;
            border: none;
            border-radius: 12px;
            background: var(--accent-soft);
            color: var(--accent);
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }

            .mobile-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    @include('student.partials.sidebar')

    <div class="main-wrapper">
        <header class="header-nav">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <button class="mobile-toggle" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>

                    <div>
                        <h6 class="fw-bold mb-0">@yield('page_title', 'Student Panel')</h6>
                        <small class="text-muted">@yield('page_subtitle', 'Manage your applications and training')</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button id="themeToggle" class="theme-btn" type="button">
                        <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                    </button>

                    <div class="px-3 py-2 rounded-pill border bg-white small fw-semibold">
                        {{ auth()->user()->name ?? 'Student' }}
                    </div>
                </div>
            </div>
        </header>

        <div class="content-wrap">
            @if(session('success'))
            <div class="alert alert-success rounded-4">{{ session('success') }}</div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </div>

    <script>
        const html = document.documentElement;
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const savedTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-bs-theme', savedTheme);

        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.className = 'bi bi-sun-fill';
            } else {
                themeIcon.className = 'bi bi-moon-stars-fill';
            }
        }

        updateThemeIcon(savedTheme);

        themeToggle.addEventListener('click', function() {
            const current = html.getAttribute('data-bs-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', next);
            localStorage.setItem('theme', next);
            updateThemeIcon(next);
        });

        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('studentSidebar');

        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }
    </script>

    @stack('scripts')
</body>

</html>