<aside class="sidebar" id="sidebar">
    <div class="brand-section">
        <div class="brand-icon">
            <i class="bi bi-building"></i>
        </div>
        <span class="brand-name">Company Panel</span>
    </div>

    <ul class="nav-menu">
        <li class="nav-item">
            <a href="{{ route('company.dashboard') }}" class="nav-link {{ request()->routeIs('company.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('company.programs.index') }}" class="nav-link {{ request()->routeIs('company.programs.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Training Programs</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('company.programs.create') }}" class="nav-link {{ request()->routeIs('company.programs.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i>
                <span>Add Program</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('company.applicants.index') }}" class="nav-link {{ request()->routeIs('company.applicants.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Applicants</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Reports</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-bell"></i>
                <span>Notifications</span>
            </a>
        </li>

        <div style="margin-top:auto;">
            <hr class="opacity-25" />
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="nav-link text-danger border-0 bg-transparent w-100 text-start">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </ul>
</aside>