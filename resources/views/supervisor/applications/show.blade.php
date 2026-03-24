@extends('supervisor.layouts.app')

@section('title', 'Application Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('supervisor.applications.index') }}" class="btn btn-light border rounded-pill">
        <i class="bi bi-arrow-left"></i> Back
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

            @if($isFinalApproved)
            <div class="mt-3 d-flex gap-2 flex-wrap">
                <a href="{{ route('tasks.board', $application->id) }}" class="btn btn-primary rounded-pill">Open Student Tasks Board</a>
                @if($application->training_completed_at)
                <a href="{{ route('training.complete', $application->id) }}" class="btn btn-outline-primary rounded-pill">Completion Screen</a>
                @endif
            </div>
            @endif
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

                @if($trainingEndAt)
                <div class="col-md-6">
                    <div class="text-muted small">Training End Date</div>
                    <div class="fw-semibold">{{ $trainingEndAt->format('Y-m-d') }}</div>
                </div>
                @endif

                @if($application->training_completed_at)
                <div class="col-md-6">
                    <div class="text-muted small">Completed At</div>
                    <div class="fw-semibold">{{ $application->training_completed_at->format('Y-m-d h:i A') }}</div>
                </div>
                @endif
            </div>
        </div>

        @if($canComplete || $application->supervisor_final_score !== null)
        <div class="bg-white rounded-4 border shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-3">Supervisor Final Evaluation</h5>

            @if(! $application->training_completed_at)
            <form method="POST" action="{{ route('supervisor.applications.complete-training', $application->id) }}" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Final Score /100</label>
                    <input type="number" name="supervisor_final_score" min="0" max="100" required class="form-control" value="{{ old('supervisor_final_score', $application->supervisor_final_score) }}">
                </div>
                <div class="col-md-9">
                    <label class="form-label">Final Written Evaluation</label>
                    <textarea name="supervisor_final_note" rows="3" class="form-control" required>{{ old('supervisor_final_note', $application->supervisor_final_note) }}</textarea>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary rounded-pill px-4" type="submit">End Training & Save Evaluation</button>
                </div>
            </form>
            @else
            <div class="alert alert-light border mb-0">
                <div><strong>Your score:</strong> {{ $application->supervisor_final_score ?? '-' }}/100</div>
                <div class="mt-1"><strong>Your note:</strong> {{ $application->supervisor_final_note ?? '-' }}</div>
            </div>
            @endif
        </div>
        @endif

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
