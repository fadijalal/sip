@extends('company.layouts.app')

@section('title', 'Applicants')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold">Student Applications</h2>
        <p class="text-muted small">Review and manage student applications for your training programs</p>
    </div>

    <button class="theme-toggle-btn" onclick="toggleTheme()">
        <i id="themeToggleIcon" class="bi bi-moon-stars-fill"></i>
    </button>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-3 g-md-4 mb-4">
    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted mb-2">Total Applications</div>
            <div class="fs-2 fw-bold">{{ $totalApplications }}</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted mb-2">Pending Review</div>
            <div class="fs-2 fw-bold text-warning">{{ $pendingApplications }}</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted mb-2">Accepted</div>
            <div class="fs-2 fw-bold text-success">{{ $approvedApplications }}</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted mb-2">Rejected</div>
            <div class="fs-2 fw-bold text-danger">{{ $rejectedApplications }}</div>
        </div>
    </div>
</div>

<div class="bg-white rounded-4 p-4 shadow-sm border mb-4">
    <div class="row g-3">
        <div class="col-md-8 position-relative">
            <i class="bi bi-search position-absolute" style="left:25px;top:50%;transform:translateY(-50%);color:#94a3b8;"></i>
            <input type="text" class="form-control" id="searchInput" placeholder="Search applicants..." style="padding-left:40px;" />
        </div>

        <div class="col-md-4">
            <select class="form-select" id="statusFilter">
                <option value="all">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="approved">Accepted</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table mb-0" id="applicantsTable">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Contact</th>
                    <th>Program</th>
                    <th>Skills</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($applications as $application)
                @php
                $student = $application->student;
                $status = $application->company_status;
                $badgeClass = $status === 'approved'
                ? 'status-accepted'
                : ($status === 'rejected' ? 'status-rejected' : 'status-pending');
                @endphp

                <tr data-status="{{ $status }}">
                    <td>
                        <a href="{{ route('company.applicants.show', $application->id) }}" class="text-decoration-none text-dark fw-bold">
                            {{ $student->name ?? 'Student' }}
                        </a>
                    </td>

                    <td>
                        <div class="small">
                            <i class="bi bi-envelope me-1"></i> {{ $student->email ?? '-' }}
                        </div>
                        <div class="small text-muted">
                            <i class="bi bi-telephone me-1"></i> {{ $student->phone_number ?? '-' }}
                        </div>
                    </td>

                    <td>
                        <div class="fw-medium">{{ $application->opportunity->title ?? '-' }}</div>
                        <div class="text-muted small">{{ ucfirst($application->opportunity->type ?? '-') }}</div>
                    </td>

                    <td>{{ $application->skills ?? '-' }}</td>

                    <td>
                        <span class="status-badge {{ $badgeClass }}">
                            {{ ucfirst($status) }}
                        </span>
                    </td>

                    <td class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('company.applicants.show', $application->id) }}" class="btn btn-light btn-sm">
                            <i class="bi bi-eye"></i>
                        </a>

                        @if($status === 'pending')
                        <form method="POST" action="{{ route('company.applications.approve', $application->id) }}">
                            @csrf
                            <button class="btn btn-success btn-sm">
                                <i class="bi bi-check"></i>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('company.applications.reject', $application->id) }}">
                            @csrf
                            <button class="btn btn-danger btn-sm">
                                <i class="bi bi-x"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">No applications found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById("searchInput");
    const statusFilter = document.getElementById("statusFilter");
    const tableRows = document.querySelectorAll("#applicantsTable tbody tr");

    function filterApplicants() {
        const search = searchInput.value.toLowerCase();
        const status = statusFilter.value;

        tableRows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const rowStatus = row.dataset.status || '';
            const matchesSearch = text.includes(search);
            const matchesStatus = status === 'all' || rowStatus === status;

            row.style.display = matchesSearch && matchesStatus ? '' : 'none';
        });
    }

    searchInput?.addEventListener('keyup', filterApplicants);
    statusFilter?.addEventListener('change', filterApplicants);
</script>
@endpush