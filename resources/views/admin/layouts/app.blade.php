<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --bg: #f5f6f8;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #e5e7eb;
            --primary: #3b5bfd;
            --primary2: #4338f2;
            --green: #16a34a;
            --green-soft: #dcfce7;
            --orange: #f97316;
            --orange-soft: #ffedd5;
            --blue-soft: #dbeafe;
            --shadow: 0 10px 25px rgba(0, 0, 0, .07);
            --radius: 24px;
        }

        * {
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 300px;
            background: #fff;
            border-right: 1px solid var(--border);
            padding: 32px 26px;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 40px;
        }

        .brand-icon {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--primary), var(--primary2));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 22px;
        }

        .brand-title {
            font-size: 20px;
            font-weight: 600;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .menu a {
            text-decoration: none;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            border-radius: 18px;
            font-size: 16px;
            font-weight: 500;
            transition: .25s ease;
        }

        .menu a i {
            width: 20px;
            font-size: 18px;
        }

        .menu a:hover {
            background: #f3f4f6;
        }

        .menu a.active {
            color: #fff;
            background: linear-gradient(90deg, var(--primary), var(--primary2));
            box-shadow: 0 8px 20px rgba(59, 91, 253, .25);
        }

        .main-content {
            flex: 1;
            padding: 34px 34px 28px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 28px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .page-subtitle {
            color: var(--muted);
            font-size: 16px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .date-pill {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 160px;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .03);
        }

        .avatar-pill {
            width: 60px;
            height: 60px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--primary), var(--primary2));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 8px 20px rgba(59, 91, 253, .2);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 26px;
            box-shadow: var(--shadow);
            min-height: 220px;
            position: relative;
        }

        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 34px;
        }

        .stat-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 24px;
        }

        .icon-blue {
            background: linear-gradient(135deg, #4162ff, #3e43f0);
        }

        .icon-green {
            background: #10b157;
        }

        .icon-orange {
            background: #f97316;
        }

        .trend {
            color: #16a34a;
            font-size: 18px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 22px;
        }

        .stat-label {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .stat-note {
            color: #16a34a;
            font-size: 14px;
            font-weight: 500;
        }

        .section-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 26px;
            box-shadow: var(--shadow);
            padding: 26px;
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .section-title {
            font-size: 22px;
            font-weight: 600;
        }

        .view-all-btn {
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 16px;
            padding: 11px 20px;
            font-weight: 500;
            color: #374151;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .activity-item {
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .activity-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .activity-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .soft-blue {
            background: var(--blue-soft);
            color: #2563eb;
        }

        .soft-green {
            background: #dff7e7;
            color: #16a34a;
        }

        .soft-orange {
            background: #ffedd5;
            color: #f97316;
        }

        .activity-title {
            font-size: 16px;
            font-weight: 500;
            line-height: 1.45;
        }

        .activity-sub {
            color: #667085;
            font-size: 14px;
            margin-top: 2px;
        }

        .activity-date {
            color: #7c8798;
            font-size: 14px;
            margin-top: 4px;
        }

        .activity-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .status-pill {
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 500;
        }

        .status-completed {
            background: #dff7e7;
            color: #16a34a;
        }

        .status-pending {
            background: #ffedd5;
            color: #f97316;
        }

        .icon-btn {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #111827;
        }

        .icon-btn.primary {
            background: linear-gradient(135deg, var(--primary), var(--primary2));
            color: #fff;
            border: none;
        }

        .table-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 26px;
            box-shadow: var(--shadow);
            padding: 26px;
        }

        .filters-bar {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .filter-btn {
            border: none;
            background: #eef2ff;
            color: #3b5bfd;
            padding: 10px 16px;
            border-radius: 14px;
            font-weight: 600;
        }

        .filter-btn.active {
            background: linear-gradient(90deg, var(--primary), var(--primary2));
            color: #fff;
        }

        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 14px;
        }

        .custom-table thead th {
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
            padding: 0 14px 6px;
        }

        .custom-table tbody tr {
            background: #fff;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .04);
        }

        .custom-table tbody td {
            padding: 18px 14px;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .custom-table tbody td:first-child {
            border-left: 1px solid var(--border);
            border-top-left-radius: 18px;
            border-bottom-left-radius: 18px;
        }

        .custom-table tbody td:last-child {
            border-right: 1px solid var(--border);
            border-top-right-radius: 18px;
            border-bottom-right-radius: 18px;
        }

        .role-badge {
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
        }

        .role-student {
            background: #ede9fe;
            color: #7c3aed;
        }

        .role-supervisor {
            background: #fff7ed;
            color: #ea580c;
        }

        .user-name {
            font-weight: 600;
        }

        .user-email {
            color: #6b7280;
            font-size: 14px;
            margin-top: 3px;
        }

        @media(max-width:1200px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width:992px) {
            .sidebar {
                width: 100px;
                padding: 24px 16px;
            }

            .brand-title,
            .menu a span {
                display: none;
            }

            .menu a {
                justify-content: center;
                padding: 16px;
            }

            .main-content {
                padding: 20px;
            }
        }

        @media(max-width:768px) {
            .topbar {
                flex-direction: column;
                align-items: stretch;
            }

            .topbar-right {
                justify-content: space-between;
            }

            .activity-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .custom-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        @include('admin.partials.sidebar')

        <main class="main-content">
            @yield('content')
        </main>
    </div>
</body>

</html>