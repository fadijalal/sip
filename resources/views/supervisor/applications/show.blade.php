@extends('supervisor.layouts.app')

@section('title', 'Application Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('supervisor.applications.index') }}" class="btn btn-light border rounded-pill">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="bg-white rounded-4 border shadow-sm p-4 h-100">
            <h5 class="fw-bold mb-4">Student Info</h5>

            <div class="mb-3">
                <div class="text-muted small">Name</div>
                <div class="fw-semibold">{{ $application->student->name ?? '-' }}</div>
            </div>

            <div class="mb-3">
                <div class="text-muted small">Email</div>
                <div class="fw-semibold">{{ $application->student->email ?? '-' }}</div>
            </div>

            <div class="mb-3">
                <div class="text-muted small">Phone</div>
                <div class="fw-semibold">{{ $application->student->phone_number ?? '-' }}</div>
            </div>

            <div class="mb-3">
                <div class="text-muted small">University ID</div>
                <div class="fw-semibold">{{ $application->student->university_id ?? '-' }}</div>
            </div>

            <div class="mb-0">
                <div class="text-muted small">Supervisor Code</div>
                <div class="fw-semibold">{{ $application->student->supervisor_code ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="bg-white rounded-4 border shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-4">Application Info</h5>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted small">Program / Job</div>
                    <div class="fw-semibold">{{ $application->opportunity->title ?? '-' }}</div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Company</div>
                    <div class="fw-semibold">
                        {{ $application->opportunity->companyUser->company_name ?? $application->opportunity->companyUser->name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Type</div>
                    <div class="fw-semibold">{{ ucfirst($application->opportunity->type ?? '-') }}</div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Field</div>
                    <div class="fw-semibold">{{ $application->opportunity->field ?? '-' }}</div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Company Status</div>
                    <div class="fw-semibold">{{ ucfirst($application->company_status ?? 'pending') }}</div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Supervisor Status</div>
                    <div class="fw-semibold">{{ ucfirst($application->supervisor_status ?? 'pending') }}</div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Final Status</div>
                    <div class="fw-semibold">{{ ucfirst($application->final_status ?? 'pending') }}</div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Applied At</div>
                    <div class="fw-semibold">{{ optional($application->created_at)->format('Y-m-d h:i A') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-4 border shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-3">Student Motivation</h5>
            <p class="text-muted mb-0">{{ $application->motivation ?? 'No motivation provided.' }}</p>
        </div>

        <div class="bg-white rounded-4 border shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-3">Student Skills</h5>
            <p class="text-muted mb-0">{{ $application->skills ?? 'No skills provided.' }}</p>
        </div>

        <div class="bg-white rounded-4 border shadow-sm p-4">
            <h5 class="fw-bold mb-3">CV</h5>
            @if($application->cv)
            <a href="{{ asset('storage/' . $application->cv) }}" target="_blank" class="btn btn-primary rounded-pill">
                <i class="bi bi-file-earmark-arrow-down"></i> Open CV
            </a>
            @else
            <p class="text-muted mb-0">No CV uploaded.</p>
            @endif
        </div>
    </div>
</div>
@endsection