@extends('student.layouts.app')

@section('title', 'Student Dashboard')
@section('page_title', 'Student Dashboard')
@section('page_subtitle', 'Overview of your applications and active training')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="text-muted small mb-2">Total Applications</div>
            <div class="fw-bold fs-2">{{ $totalApplications }}</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="text-muted small mb-2">Pending</div>
            <div class="fw-bold fs-2 text-warning">{{ $pendingApplications }}</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="text-muted small mb-2">Approved</div>
            <div class="fw-bold fs-2 text-success">{{ $approvedApplications }}</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="text-muted small mb-2">Rejected</div>
            <div class="fw-bold fs-2 text-danger">{{ $rejectedApplications }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Applications Overview</h5>
                    <p class="text-muted mb-0 small">Your recent applications and their final decisions</p>
                </div>

                <a href="{{ route('student.applications.index') }}" class="btn btn-outline-primary rounded-pill">
                    View All
                </a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Company</th>
                            <th>Final Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications->take(5) as $application)
                        <tr>
                            <td>{{ $application->opportunity->title ?? '-' }}</td>
                            <td>{{ $application->opportunity->companyUser->company_name ?? $application->opportunity->companyUser->name ?? '-' }}</td>
                            <td>
                                @if($application->final_status === 'approved')
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">Approved</span>
                                @elseif($application->final_status === 'rejected')
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2">Rejected</span>
                                @else
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No applications yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="content-card h-100">
            <h5 class="fw-bold mb-3">Active Training</h5>

            @if($activeTraining)
            <div class="mb-3">
                <div class="text-muted small mb-1">Program</div>
                <div class="fw-semibold">{{ $activeTraining->opportunity->title ?? '-' }}</div>
            </div>

            <div class="mb-3">
                <div class="text-muted small mb-1">Company</div>
                <div class="fw-semibold">
                    {{ $activeTraining->opportunity->companyUser->company_name ?? $activeTraining->opportunity->companyUser->name ?? '-' }}
                </div>
            </div>

            <div class="mb-4">
                <div class="text-muted small mb-1">Status</div>
                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">Approved</span>
            </div>

            <a href="{{ route('student.workspace.index') }}" class="btn btn-primary rounded-pill w-100">
                Go to Workspace
            </a>
            @else
            <div class="alert alert-warning rounded-4 mb-0">
                You do not have an approved training yet.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection