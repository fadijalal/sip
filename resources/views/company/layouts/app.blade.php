<!doctype html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Company Dashboard')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        :root {
            --sidebar-bg: #ffffff;
            --main-bg: #f8f9fb;
            --accent-purple: #4f46e5;
            --text-main: #111827;
            --text-muted: #6b7280;
            --card-radius: 16px;
            --card-bg: #ffffff;
            --border-color: #f0f0f0;
            --item-border: #f1f5f9;
        }

        [data-theme="dark"] {
            --sidebar-bg: #111827;
            --main-bg: #030712;
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --card-bg: #1f2937;
            --border-color: #374151;
            --item-border: #374151;
        }

        body {
            font-family: "Inter", sans-serif;
            background-color: var(--main-bg);
            color: var(--text-main);
            margin: 0;
            display: flex;
            transition: .3s;
            overflow-x: hidden;
            width: 100%;
        }

        .menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            background: var(--accent-purple);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            z-index: 1100;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .12);
        }

        @media (max-width: 991.98px) {
            .menu-toggle {
                display: block;
            }
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            padding: 24px 16px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            overflow-y: auto;
            transition: .3s;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-260px);
                box-shadow: 2px 0 20px rgba(0, 0, 0, .1);
            }

            .sidebar.show {
                transform: translateX(0);
            }
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 12px 32px 12px;
        }

        .brand-icon {
            background: var(--accent-purple);
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(79, 70, 229, .2);
        }

        .brand-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--text-main);
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: calc(100% - 80px);
        }

        .nav-item {
            margin-bottom: 4px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            font-size: .95rem;
            border-radius: 12px;
            transition: .2s;
        }

        .nav-link:hover {
            background-color: var(--border-color);
            color: var(--text-main);
        }

        .nav-link.active {
            background: var(--accent-purple);
            color: white !important;
            box-shadow: 0 8px 20px rgba(79, 70, 229, .3);
        }

        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 45px 50px;
            transition: .3s;
        }

        @media (max-width: 991.98px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 70px 20px 30px 20px;
            }
        }

        .theme-toggle-btn {
            background: #f3f0ff;
            color: #7c3aed;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: .3s;
            font-size: 1.1rem;
        }

        [data-theme="dark"] .theme-toggle-btn {
            background: #2d2d44;
            color: #fbbf24;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .05);
            height: 100%;
            transition: .3s;
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .applicant-item {
            background: var(--card-bg);
            border: 1px solid var(--item-border);
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 12px;
            transition: .2s;
        }

        .applicant-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, .05);
        }

        .avatar {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            flex-shrink: 0;
        }

        .status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: .85rem;
            font-weight: 500;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .status-pending {
            background: #fff7ed;
            color: #c2410c;
        }

        .status-accepted {
            background: #f0fdf4;
            color: #15803d;
        }

        .status-rejected {
            background: #fee2e2;
            color: #dc2626;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #64748b;
            text-decoration: none;
        }

        .table-card {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, .03);
            overflow: hidden;
        }

        .table thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: .75rem;
            text-transform: uppercase;
            font-weight: 600;
            padding: 15px 20px;
            border: none;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 20px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
        }

        [data-theme="dark"] .table thead th {
            background: #111827;
            color: #9ca3af;
        }

        [data-theme="dark"] .bg-white,
        [data-theme="dark"] .table-card {
            background-color: var(--card-bg) !important;
            color: var(--text-main) !important;
        }

        [data-theme="dark"] .btn-action {
            background: #374151;
            border-color: #4b5563;
            color: #d1d5db;
        }
    </style>
    @stack('styles')
</head>

<body>
    <button class="menu-toggle" onclick="toggleMenu()">
        <i class="bi bi-list"></i>
    </button>

    @include('company.partials.sidebar')

    <div class="main-content">
        @yield('content')
    </div>

    <script>
        const htmlElement = document.documentElement;

        const savedTheme = localStorage.getItem("theme") || "light";
        htmlElement.setAttribute("data-theme", savedTheme);

        function toggleTheme() {
            const currentTheme = htmlElement.getAttribute("data-theme");
            const newTheme = currentTheme === "light" ? "dark" : "light";
            htmlElement.setAttribute("data-theme", newTheme);
            localStorage.setItem("theme", newTheme);

            const icon = document.querySelector("#themeToggleIcon");
            if (icon) {
                icon.className = newTheme === "dark" ? "bi bi-sun-fill" : "bi bi-moon-stars-fill";
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const icon = document.querySelector("#themeToggleIcon");
            if (icon) {
                icon.className = savedTheme === "dark" ? "bi bi-sun-fill" : "bi bi-moon-stars-fill";
            }
        });

        function toggleMenu() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("show");
        }

        document.addEventListener("click", function(event) {
            const sidebar = document.getElementById("sidebar");
            const menuToggle = document.querySelector(".menu-toggle");

            if (window.innerWidth <= 991.98) {
                if (!sidebar.contains(event.target) && !menuToggle.contains(event.target) && sidebar.classList.contains("show")) {
                    sidebar.classList.remove("show");
                }
            }
        });
    </script>
    @stack('scripts')
</body>

</html>