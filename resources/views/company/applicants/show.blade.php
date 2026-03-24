@extends('company.layouts.app')

@section('title', 'Applicant Details')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <a href="{{ route('company.applicants.index') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i> Back to Applicants
        </a>
    </div>

    @php
    $isFinalApproved = $application->company_status === 'approved' && $application->supervisor_status === 'approved' && $application->final_status === 'approved';
    $trainingEndAt = null;
    if ($application->approved_at && $application->opportunity?->duration) {
        $trainingEndAt = $application->approved_at->copy()->addMonths((int) $application->opportunity->duration)->startOfDay();
    }
    $canComplete = $isFinalApproved && ! $application->training_completed_at && $trainingEndAt && now()->startOfDay()->greaterThanOrEqualTo($trainingEndAt);
    @endphp

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-4 p-4 p-md-5 mb-4 border shadow-sm">
        <div class="row align-items-start">
            <div class="col-auto mb-3 mb-md-0">
                <div class="bg-primary text-white d-flex align-items-center justify-content-center rounded-4" style="width:110px;height:110px;font-size:2rem;font-weight:700;">
                    {{ strtoupper(substr($application->student->name ?? 'ST', 0, 2)) }}
                </div>
            </div>

            <div class="col">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                    <div>
                        <h1 class="h3 fw-bold mb-1">{{ $application->student->name ?? 'Student' }}</h1>
                        <span class="badge rounded-pill {{ $application->company_status === 'approved' ? 'bg-success-subtle text-success' : ($application->company_status === 'rejected' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') }}">
                            {{ ucfirst($application->company_status) }}
                        </span>
                        @if($application->training_completed_at)
                        <span class="badge rounded-pill bg-primary-subtle text-primary ms-1">Training Completed</span>
                        @endif
                    </div>

                    <div class="mt-3 mt-md-0 d-flex gap-2">
                        @if($application->company_status === 'pending')
                        <form method="POST" action="{{ route('company.applications.approve', $application->id) }}">
                            @csrf
                            <button class="btn btn-success rounded-pill px-3">Approve</button>
                        </form>

                        <form method="POST" action="{{ route('company.applications.reject', $application->id) }}">
                            @csrf
                            <button class="btn btn-danger rounded-pill px-3">Reject</button>
                        </form>
                        @endif
                    </div>
                </div>

                @if($isFinalApproved)
                <div class="mb-3 d-flex gap-2 flex-wrap">
                    <a href="{{ route('tasks.board', $application->id) }}" class="btn btn-primary rounded-pill px-3">Open Student Tasks Board</a>
                    @if($application->training_completed_at)
                    <a href="{{ route('training.complete', $application->id) }}" class="btn btn-outline-primary rounded-pill px-3">Completion Screen</a>
                    @endif
                </div>
                @endif

                @if($trainingEndAt)
                <p class="small text-muted mb-2">Training period ends on: {{ $trainingEndAt->format('Y-m-d') }}</p>
                @endif

                <div class="row g-3">
                    <div class="col-md-4"><i class="bi bi-envelope me-2 text-primary"></i>{{ $application->student->email ?? '-' }}</div>
                    <div class="col-md-4"><i class="bi bi-telephone me-2 text-primary"></i>{{ $application->student->phone_number ?? '-' }}</div>
                    <div class="col-md-4"><i class="bi bi-person-badge me-2 text-primary"></i>{{ $application->student->university_id ?? '-' }}</div>
                    <div class="col-md-6"><i class="bi bi-briefcase me-2 text-primary"></i>{{ $application->opportunity->title ?? '-' }}</div>
                    <div class="col-md-6"><i class="bi bi-tag me-2 text-primary"></i>{{ ucfirst($application->opportunity->type ?? '-') }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($canComplete || $application->company_final_score !== null)
    <div class="bg-white rounded-4 p-4 p-md-5 mb-4 border shadow-sm">
        <h2 class="h5 fw-bold mb-3">Company Final Evaluation</h2>

        @if(! $application->training_completed_at)
        <form method="POST" action="{{ route('company.applications.complete-training', $application->id) }}" class="row g-3">
            @csrf
            <div class="col-md-3">
                <label class="form-label">Final Score /100</label>
                <input type="number" name="company_final_score" min="0" max="100" required class="form-control" value="{{ old('company_final_score', $application->company_final_score) }}">
            </div>
            <div class="col-md-9">
                <label class="form-label">Final Written Evaluation</label>
                <textarea name="company_final_note" rows="3" class="form-control" required>{{ old('company_final_note', $application->company_final_note) }}</textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary rounded-pill px-4" type="submit">End Training & Save Evaluation</button>
            </div>
        </form>
        @else
        <div class="alert alert-light border mb-0">
            <div><strong>Your score:</strong> {{ $application->company_final_score ?? '-' }}/100</div>
            <div class="mt-1"><strong>Your note:</strong> {{ $application->company_final_note ?? '-' }}</div>
        </div>
        @endif
    </div>
    @endif

    <div class="bg-white rounded-4 p-4 p-md-5 border shadow-sm">
        <h2 class="h5 fw-bold mb-4">Application Details</h2>

        <div class="row g-4">
            <div class="col-md-6">
                <label class="small text-muted d-block mb-2">Skills</label>
                <div class="border rounded-3 p-3">{{ $application->skills ?? '-' }}</div>
            </div>

            <div class="col-md-6">
                <label class="small text-muted d-block mb-2">Motivation</label>
                <div class="border rounded-3 p-3">{{ $application->motivation ?? '-' }}</div>
            </div>

            <div class="col-md-4">
                <label class="small text-muted d-block mb-2">Company Status</label>
                <div class="border rounded-3 p-3">{{ ucfirst($application->company_status) }}</div>
            </div>

            <div class="col-md-4">
                <label class="small text-muted d-block mb-2">Supervisor Status</label>
                <div class="border rounded-3 p-3">{{ ucfirst($application->supervisor_status) }}</div>
            </div>

            <div class="col-md-4">
                <label class="small text-muted d-block mb-2">Final Status</label>
                <div class="border rounded-3 p-3">{{ ucfirst($application->final_status) }}</div>
            </div>

            <div class="col-12">
                <label class="small text-muted d-block mb-2">CV</label>
                <div class="border rounded-3 p-3">
                    @if($application->cv)
                    <a href="{{ asset('storage/' . $application->cv) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-file-earmark-pdf me-2"></i> Open CV
                    </a>
                    @else
                    -
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
