@extends('student.layouts.app')

@section('title', 'Browse Programs')
@section('page_title', 'Browse Programs')
@section('page_subtitle', 'Explore available training opportunities and jobs')

@section('content')
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Available Programs</h5>
            <p class="text-muted mb-0 small">{{ $programs->count() }} open opportunities available</p>
        </div>
    </div>

    <div class="row g-4">
        @forelse($programs as $program)
        <div class="col-lg-6">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="text-muted small mb-2">{{ ucfirst($program->type ?? '-') }}</div>
                        <h5 class="fw-bold mb-1">{{ $program->title }}</h5>
                        <div class="text-muted small">
                            {{ $program->companyUser->company_name ?? $program->companyUser->name ?? '-' }}
                        </div>
                    </div>

                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                        {{ ucfirst($program->status) }}
                    </span>
                </div>

                <p class="text-muted small mb-3">
                    {{ \Illuminate\Support\Str::limit($program->description, 140) }}
                </p>

                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <div class="small text-muted">Field</div>
                        <div class="fw-semibold small">{{ $program->field ?? '-' }}</div>
                    </div>

                    <div class="col-6">
                        <div class="small text-muted">City</div>
                        <div class="fw-semibold small">{{ $program->city ?? '-' }}</div>
                    </div>

                    <div class="col-6">
                        <div class="small text-muted">Work Type</div>
                        <div class="fw-semibold small">{{ ucfirst($program->work_type ?? '-') }}</div>
                    </div>

                    <div class="col-6">
                        <div class="small text-muted">Duration</div>
                        <div class="fw-semibold small">{{ $program->duration ?? 0 }} month(s)</div>
                    </div>
                </div>

                <a href="{{ route('student.programs.show', $program->id) }}" class="btn btn-primary rounded-pill">
                    View Details
                </a>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-light border rounded-4 text-center">
                No open programs found.
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection