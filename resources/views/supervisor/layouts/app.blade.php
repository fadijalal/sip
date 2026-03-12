<!doctype html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Supervisor Dashboard')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-purple: #6366f1;
            --bg-light: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #f1f5f9;
            --sidebar-bg: #ffffff;
        }

        [data-theme="dark"] {
            --bg-light: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --sidebar-bg: #1e293b;
        }

        body {
            font-family: "Inter", sans-serif;
            background-color: var(--bg-light);
            color: var(--text-main);
            margin: 0;
            display: flex;
            transition: .3s;
            overflow-x: hidden;
        }

        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1060;
            width: 45px;
            height: 45px;
            background: var(--primary-purple);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            position: fixed;
            padding: 24px;
            display: flex;
            flex-direction: column;
            transition: .3s;
            z-index: 1050;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }

        .logo-box {
            width: 40px;
            height: 40px;
            background: var(--primary-purple);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 8px;
            transition: .3s;
        }

        .nav-link-custom.active {
            background: var(--primary-purple);
            color: white;
        }

        .nav-link-custom:hover:not(.active) {
            background: var(--border-color);
            color: var(--primary-purple);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 40px;
            min-height: 100vh;
        }

        .theme-toggle-btn {
            width: 45px;
            height: 45px;
            background: #f1efff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            color: #6366f1;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        [data-theme="dark"] .theme-toggle-btn {
            background: #334155;
            color: #fbbf24;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 24px;
            border: 1px solid var(--border-color);
            transition: .3s;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .05);
        }

        .icon-rounded {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 20px;
            color: white;
        }

        .student-card {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 24px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, .04);
            transition: .3s;
            height: 100%;
        }

        .student-card:hover {
            transform: translateY(-5px);
        }

        .avatar-lg {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            color: white;
            background: var(--primary-purple);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-on-track {
            background: #f0fdf4;
            color: #22c55e;
        }

        .status-at-risk {
            background: #fffbeb;
            color: #f59e0b;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 8px;
            color: var(--text-muted);
        }

        .progress {
            height: 8px;
            border-radius: 10px;
            background: var(--bg-light);
            margin-bottom: 20px;
        }

        .card-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .btn-outline-card {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-main);
            background: var(--card-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: .3s;
            text-decoration: none;
        }

        .btn-outline-card:hover {
            background: var(--bg-light);
            border-color: var(--primary-purple);
            color: var(--primary-purple);
        }

        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
                width: 240px;
                box-shadow: 2px 0 20px rgba(0, 0, 0, 0.1);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 80px 16px 40px 16px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <button class="mobile-menu-toggle" id="mobileMenuToggle" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>

    @include('supervisor.partials.sidebar')

    <main class="main-content">
        @yield('content')
    </main>

    <script>
        const htmlElement = document.documentElement;
        const savedTheme = localStorage.getItem('theme') || 'light';
        htmlElement.setAttribute('data-theme', savedTheme);

        function toggleTheme() {
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            const icon = document.getElementById('themeIcon');
            if (icon) {
                icon.className = newTheme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const icon = document.getElementById('themeIcon');
            if (icon) {
                icon.className = savedTheme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('mobileMenuToggle');
            sidebar.classList.toggle('show');

            if (sidebar.classList.contains('show')) {
                toggleBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
            } else {
                toggleBtn.innerHTML = '<i class="bi bi-list"></i>';
            }
        }

        document.querySelectorAll('.nav-link-custom').forEach((link) => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    const sidebar = document.getElementById('sidebar');
                    const toggleBtn = document.getElementById('mobileMenuToggle');
                    sidebar.classList.remove('show');
                    toggleBtn.innerHTML = '<i class="bi bi-list"></i>';
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>