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
    <div class="row g-3">
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
    </div>
</div>

<div class="bg-white rounded-4 border shadow-sm p-3">
    @livewire('kanban-board', ['applicationId' => $activeApplication->id])
</div>
@else
<div class="bg-white rounded-4 border shadow-sm p-5 text-center">
    <h5 class="fw-bold mb-2">Kanban Board</h5>
    <p class="text-muted mb-0">
        Kanban board will appear here after final approval from both company and supervisor.
    </p>
</div>
@endif
@endsection