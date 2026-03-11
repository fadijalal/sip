@extends('company.layouts.app')

@section('title', 'Company Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Company Dashboard</h2>
        <p class="text-muted">Manage your training programs and applicants</p>
    </div>

    <div class="d-flex align-items-center">
        <button class="theme-toggle-btn me-3" onclick="toggleTheme()">
            <i id="themeToggleIcon" class="bi bi-moon-stars-fill"></i>
        </button>

        <div class="bg-white border rounded-pill px-3 py-2 me-3 shadow-sm d-flex align-items-center">
            <i class="bi bi-calendar3 me-2 text-muted"></i>
            <small class="fw-medium">{{ now()->format('M d, Y') }}</small>
        </div>

        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
            {{ strtoupper(substr(auth()->user()->name ?? 'CO', 0, 2)) }}
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-4 mb-5">
    <div class="col-sm-6 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-clipboard-check fs-4"></i>
                </div>
                <i class="bi bi-graph-up-arrow text-success"></i>
            </div>
            <h1 class="fw-bold mb-1">{{ $totalPrograms }}</h1>
            <p class="text-muted mb-3">Total Programs</p>
            <small class="text-success fw-bold">All created opportunities</small>
        </div>
    </div>

    <div class="col-sm-6 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-unlock fs-4"></i>
                </div>
                <i class="bi bi-graph-up-arrow text-success"></i>
            </div>
            <h1 class="fw-bold mb-1">{{ $openPrograms }}</h1>
            <p class="text-muted mb-3">Open Programs</p>
            <small class="text-success fw-bold">Currently accepting students</small>
        </div>
    </div>

    <div class="col-sm-6 col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-file-earmark-text fs-4"></i>
                </div>
                <i class="bi bi-graph-up-arrow text-success"></i>
            </div>
            <h1 class="fw-bold mb-1">{{ $pendingApplications }}</h1>
            <p class="text-muted mb-3">Pending Applications</p>
            <small class="text-success fw-bold">Waiting for your review</small>
        </div>
    </div>
</div>

<div class="bg-white rounded-4 p-4 shadow-sm border">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">Recent Applicants</h5>
        <a href="{{ route('company.applicants.index') }}" class="btn btn-light btn-sm px-3 rounded-pill">View All</a>
    </div>

    @forelse($recentApplicants as $application)
    @php
    $student = $application->student;
    $name = $student->name ?? 'Student';
    $initials = strtoupper(substr($name, 0, 1)) . strtoupper(substr(explode(' ', trim($name))[1] ?? '', 0, 1));
    $statusClass = $application->company_status === 'approved'
    ? 'status-accepted'
    : ($application->company_status === 'rejected' ? 'status-rejected' : 'status-pending');
    @endphp

    <div class="applicant-item d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="avatar bg-primary me-3">{{ $initials ?: 'ST' }}</div>
            <div>
                <h6 class="mb-0 fw-bold">{{ $name }}</h6>
                <small class="text-muted">
                    {{ $application->opportunity->title ?? '-' }} • Applied: {{ optional($application->created_at)->format('m/d/Y') }}
                </small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="status-badge {{ $statusClass }}">
                {{ ucfirst($application->company_status) }}
            </span>

            <a href="{{ route('company.applicants.show', $application->id) }}" class="btn-action">
                <i class="bi bi-eye"></i>
            </a>

            @if($application->company_status === 'pending')
            <form method="POST" action="{{ route('company.applications.approve', $application->id) }}">
                @csrf
                <button class="btn-action bg-primary text-white border-0">
                    <i class="bi bi-check-lg"></i>
                </button>
            </form>

            <form method="POST" action="{{ route('company.applications.reject', $application->id) }}">
                @csrf
                <button class="btn-action bg-danger text-white border-0">
                    <i class="bi bi-x-lg"></i>
                </button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="alert alert-light border">No recent applicants yet.</div>
    @endforelse
</div>
@endsection