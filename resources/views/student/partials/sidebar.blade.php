<div class="sidebar" id="studentSidebar">
    <a href="{{ route('student.dashboard') }}" class="logo-area">
        <div class="p-2 rounded-3 text-white" style="background: var(--accent)">
            <i class="bi bi-mortarboard-fill fs-5"></i>
        </div>
        <span class="fw-bold fs-5">SIP</span>
    </a>

    <div class="small fw-bold text-muted text-uppercase mb-3 opacity-50" style="font-size: 11px; letter-spacing: 1px;">
        Menu
    </div>

    <nav class="mb-4">
        <a href="{{ route('student.dashboard') }}"
            class="nav-link-custom {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid"></i> Dashboard
        </a>

        <a href="{{ route('student.programs.index') }}"
            class="nav-link-custom {{ request()->routeIs('student.programs.*') ? 'active' : '' }}">
            <i class="bi bi-search"></i> Browse Programs
        </a>

        <a href="{{ route('student.applications.index') }}"
            class="nav-link-custom {{ request()->routeIs('student.applications.index') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i> Application Status
        </a>

        <a href="{{ route('student.workspace.index') }}"
            class="nav-link-custom {{ request()->routeIs('student.workspace.index') ? 'active' : '' }}">
            <i class="bi bi-kanban"></i> Training Workspace
        </a>
    </nav>

    <div class="small fw-bold text-muted text-uppercase mb-3 opacity-50" style="font-size: 11px; letter-spacing: 1px;">
        Support
    </div>

    <nav>
        <a href="#" class="nav-link-custom">
            <i class="bi bi-person-circle"></i> Profile
        </a>
    </nav>

    <div class="mt-auto pt-3 border-top">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link-custom text-danger border-0 bg-transparent w-100 text-start">
                <i class="bi bi-box-arrow-left"></i> Logout
            </button>
        </form>
    </div>
</div>