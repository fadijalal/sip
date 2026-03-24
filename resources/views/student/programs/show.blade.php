@extends('student.layouts.app')

@section('title', 'Program Details')
@section('page_title', 'Program Details')
@section('page_subtitle', 'View program requirements and apply')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                <div>
                    <div class="text-muted small mb-2">{{ ucfirst($program->type ?? '-') }}</div>
                    <h3 class="fw-bold mb-2">{{ $program->title }}</h3>
                    <div class="text-muted">
                        {{ $program->companyUser->company_name ?? $program->companyUser->name ?? '-' }}
                    </div>
                </div>

                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                    {{ ucfirst($program->status) }}
                </span>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold">Description</h6>
                <p class="text-muted mb-0">{{ $program->description ?? '-' }}</p>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold">Requirements</h6>
                <p class="text-muted mb-0">{{ $program->requirements ?? '-' }}</p>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="small text-muted">Field</div>
                    <div class="fw-semibold">{{ $program->field ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <div class="small text-muted">City</div>
                    <div class="fw-semibold">{{ $program->city ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <div class="small text-muted">Work Type</div>
                    <div class="fw-semibold">{{ ucfirst($program->work_type ?? '-') }}</div>
                </div>

                <div class="col-md-4">
                    <div class="small text-muted">Duration</div>
                    <div class="fw-semibold">{{ $program->duration ?? 0 }} month(s)</div>
                </div>

                <div class="col-md-4">
                    <div class="small text-muted">Education Level</div>
                    <div class="fw-semibold">{{ $program->education_level ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <div class="small text-muted">Deadline</div>
                    <div class="fw-semibold">{{ $program->deadline ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="content-card">
            <h5 class="fw-bold mb-3">Apply to this Program</h5>

            @if($existingApplication)
            <div class="alert alert-info rounded-4 mb-0">
                You already applied to this program.<br>
                Final status:
                <strong>{{ ucfirst($existingApplication->final_status) }}</strong>
            </div>
            @else
            <form method="POST" action="{{ route('student.applications.apply') }}" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="opportunity_id" value="{{ $program->id }}">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Skills</label>
                    <textarea name="skills" class="form-control rounded-4" rows="3" placeholder="Write your skills...">{{ old('skills') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Motivation</label>
                    <textarea name="motivation" class="form-control rounded-4" rows="4" placeholder="Why do you want this training?">{{ old('motivation') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Upload CV (PDF)</label>
                    <input type="file" name="cv" class="form-control rounded-4" accept=".pdf" required>
                </div>

                <button type="submit" class="btn btn-primary rounded-pill w-100">
                    Apply Now
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
