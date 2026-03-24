@extends('student.layouts.app')

@section('title', 'Training Workspace')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold">Training Workspace</h3>
        <p class="text-muted mb-0">Your tasks board appears only after final approval</p>
    </div>

    <button class="theme-toggle-btn" onclick="toggleTheme()">
        <i id="themeIcon" class="bi bi-moon-stars-fill"></i>
    </button>
</div>

@if($activeApplication)
<div class="bg-white rounded-4 border shadow-sm p-4 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-6">
            <div class="small text-muted">Approved Opportunity</div>
            <div class="fw-bold fs-5">{{ $activeApplication->opportunity->title ?? '-' }}</div>
        </div>

        <div class="col-md-3">
            <div class="small text-muted">Company Status</div>
            <div class="fw-semibold text-success">{{ ucfirst($activeApplication->company_status) }}</div>
        </div>

        <div class="col-md-3">
            <div class="small text-muted">Supervisor Status</div>
            <div class="fw-semibold text-success">{{ ucfirst($activeApplication->supervisor_status) }}</div>
        </div>

        @if($trainingEndDate)
        <div class="col-12">
            <div class="small text-muted">Training End Date: {{ $trainingEndDate->format('Y-m-d') }}</div>
        </div>
        @endif

        @if($activeApplication->training_completed_at)
        <div class="col-12 mt-2">
            <div class="alert alert-success mb-2">Training completed successfully.</div>
            <a href="{{ route('training.complete', $activeApplication->id) }}" class="btn btn-success rounded-pill">Open Congratulations Screen</a>
        </div>
        @elseif($trainingEnded)
        <div class="col-12 mt-2">
            <div class="alert alert-warning mb-2">Training period ended. Waiting for final evaluation from company and supervisor.</div>
        </div>
        @else
        <div class="col-12 mt-2">
            <a href="{{ route('tasks.board', $activeApplication->id) }}" class="btn btn-primary rounded-pill">Open Tasks Board</a>
        </div>
        @endif
    </div>
</div>
@else
<div class="bg-white rounded-4 border shadow-sm p-5 text-center">
    <h5 class="fw-bold mb-2">Tasks Board</h5>
    <p class="text-muted mb-0">
        The board appears after final approval from both company and supervisor.
    </p>
</div>
@endif
@endsection
