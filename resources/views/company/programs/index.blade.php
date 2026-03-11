@extends('company.layouts.app')

@section('title', 'Training Programs')

@push('styles')
<style>
    .filter-container {
        background: var(--card-bg);
        padding: 1rem;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
        margin-bottom: 2rem;
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
        border: 1px solid var(--border-color);
    }

    .search-input-group {
        position: relative;
        flex-grow: 1;
        min-width: 200px;
    }

    .search-input-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }

    .search-input-group input {
        padding-left: 45px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        color: var(--text-main);
    }

    .tab-pill {
        padding: 8px 18px;
        border-radius: 10px;
        font-size: .9rem;
        font-weight: 500;
        cursor: pointer;
        transition: .2s;
        border: none;
        background: #f1f5f9;
        color: var(--text-muted);
    }

    .tab-pill.active {
        background: var(--accent-purple);
        color: white;
    }

    .program-card {
        background: var(--card-bg);
        border-radius: 20px;
        padding: 1.5rem;
        border: 1px solid var(--border-color);
        transition: .3s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .program-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, .1);
    }

    .card-icon {
        width: 44px;
        height: 44px;
        background: var(--accent-purple);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        font-size: .85rem;
        margin-bottom: 12px;
        color: var(--text-muted);
        gap: 12px;
    }

    .detail-row b {
        color: var(--text-main);
    }

    .action-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        transition: .2s;
        cursor: pointer;
        text-decoration: none;
    }

    .action-btn:hover {
        background: #f1f5f9;
        color: var(--accent-purple);
        border-color: var(--accent-purple);
    }

    .review-alert {
        background: rgba(59, 130, 246, .1);
        color: #3b82f6;
        text-decoration: none;
        padding: 10px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: .8rem;
        font-weight: 500;
        margin-top: auto;
    }
</style>
@endpush

@section('content')
<div class="header-section d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Training Programs</h2>
        <p class="text-muted">Manage your training opportunities and track applications</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <button class="theme-toggle-btn" onclick="toggleTheme()">
            <i id="themeToggleIcon" class="bi bi-moon-stars-fill"></i>
        </button>

        <a href="{{ route('company.programs.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
            style="background:var(--accent-purple);border:none;padding:10px 20px;border-radius:12px;font-weight:500;">
            <i class="bi bi-plus-lg"></i> Create Program
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-md-4">
        <div class="stat-card">
            <h1 class="fw-bold mb-1">{{ $totalPrograms }}</h1>
            <p class="text-muted mb-0">Total Programs</p>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="stat-card">
            <h1 class="fw-bold mb-1">{{ $openPrograms }}</h1>
            <p class="text-muted mb-0">Published / Open</p>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="stat-card">
            <h1 class="fw-bold mb-1">{{ $closedPrograms }}</h1>
            <p class="text-muted mb-0">Closed</p>
        </div>
    </div>
</div>

<div class="filter-container">
    <div class="search-input-group">
        <i class="bi bi-search"></i>
        <input type="text" class="form-control shadow-none" id="programSearch" placeholder="Search programs..." />
    </div>

    <div class="d-flex gap-2 ms-lg-3 flex-wrap">
        <button class="tab-pill active filter-btn" data-filter="all">All Programs</button>
        <button class="tab-pill filter-btn" data-filter="open">Open</button>
        <button class="tab-pill filter-btn" data-filter="closed">Closed</button>
        <button class="tab-pill filter-btn" data-filter="training">Training</button>
        <button class="tab-pill filter-btn" data-filter="job">Job</button>
    </div>
</div>

<div class="row g-4" id="programsGrid">
    @forelse($programs as $program)
    <div class="col-xl-4 col-md-6 program-item"
        data-status="{{ $program->status }}"
        data-type="{{ $program->type }}"
        data-search="{{ strtolower($program->title . ' ' . $program->field . ' ' . $program->city) }}">
        <div class="program-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="card-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <span class="badge rounded-pill {{ $program->status === 'open' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                    {{ ucfirst($program->status) }}
                </span>
            </div>

            <h5 class="fw-bold mb-1">{{ $program->title }}</h5>
            <p class="text-muted small mb-4">{{ $program->field ?? 'General Field' }}</p>

            <div class="detail-row">
                <span><i class="bi bi-briefcase me-2"></i> Type</span>
                <b>{{ ucfirst($program->type) }}</b>
            </div>

            <div class="detail-row">
                <span><i class="bi bi-clock me-2"></i> Duration</span>
                <b>{{ $program->duration ? $program->duration . ' months' : '-' }}</b>
            </div>

            <div class="detail-row">
                <span><i class="bi bi-geo-alt me-2"></i> City</span>
                <b>{{ $program->city ?? '-' }}</b>
            </div>

            <div class="detail-row">
                <span><i class="bi bi-people me-2"></i> Applicants</span>
                <b>{{ $program->applications_count ?? 0 }}</b>
            </div>

            <div class="detail-row">
                <span><i class="bi bi-calendar-event me-2"></i> Deadline</span>
                <b>{{ $program->deadline ? \Carbon\Carbon::parse($program->deadline)->format('M d, Y') : '-' }}</b>
            </div>

            <div class="d-flex justify-content-center gap-2 mt-4 pt-2 border-top mb-3">
                <a href="{{ route('company.programs.edit', $program->id) }}" class="action-btn" title="Edit">
                    <i class="bi bi-pencil-square"></i>
                </a>

                <form method="POST" action="{{ route('company.programs.delete', $program->id) }}" onsubmit="return confirm('Delete this program?')">
                    @csrf
                    <button class="action-btn" title="Delete" type="submit">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>

            <a href="{{ route('company.applicants.index') }}" class="review-alert">
                <i class="bi bi-info-circle"></i> Review applicants for this company
            </a>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-light border">No programs found.</div>
    </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById("programSearch");
    const filterButtons = document.querySelectorAll(".filter-btn");
    const programItems = document.querySelectorAll(".program-item");

    function filterPrograms() {
        const search = searchInput.value.toLowerCase();
        const activeFilter = document.querySelector(".filter-btn.active").dataset.filter;

        programItems.forEach(item => {
            const matchesSearch = item.dataset.search.includes(search);
            const matchesFilter =
                activeFilter === 'all' ||
                item.dataset.status === activeFilter ||
                item.dataset.type === activeFilter;

            item.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
        });
    }

    searchInput?.addEventListener("keyup", filterPrograms);

    filterButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            filterButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            filterPrograms();
        });
    });
</script>
@endpush