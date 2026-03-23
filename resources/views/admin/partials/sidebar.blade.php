<aside class="sidebar">
    <div class="brand">
        <div class="brand-icon">
            <i class="fa-regular fa-shield"></i>
        </div>
        <div class="brand-title">Admin Panel</div>
    </div>

    <nav class="menu">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <i class="fa-regular fa-user"></i>
            <span>Supervisiors</span>
        </a>

        <a href="{{ route('admin.companies') }}" class="{{ request()->routeIs('admin.companies') ? 'active' : '' }}">
            <i class="fa-regular fa-building"></i>
            <span>Companies</span>
        </a>

        <a href="{{ route('tasks.admin.workspace') }}" class="{{ request()->routeIs('tasks.admin.*') ? 'active' : '' }}">
            <i class="fa-solid fa-list-check"></i>
            <span>Global Tasks</span>
        </a>

        <a href="#">
            <i class="fa-regular fa-file-lines"></i>
            <span>Reports</span>
        </a>
    </nav>
</aside>
