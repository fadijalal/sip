@extends('student.layouts.app')

@section('title', 'My Applications')

@section('content')
@php
$activeApplication = $activeApplication ?? null;
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold mb-1">My Applications</h3>
        <p class="text-muted mb-0">Track the status of your submitted applications</p>
    </div>
</div>

@if($activeApplication)
<div class="alert alert-success rounded-4 border-0 shadow-sm">
    <strong>Accepted Program:</strong>
    {{ $activeApplication->opportunity->title ?? '-' }}
</div>
@endif

<div class="row g-4">
    @forelse($applications as $application)
    <div class="col-12">
        <div class="p-4 rounded-4 border bg-white shadow-sm">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h5 class="fw-bold mb-1">{{ $application->opportunity->title ?? '-' }}</h5>
                    <div class="text-muted small">
                        {{ $application->opportunity->companyUser->company_name ?? $application->opportunity->companyUser->name ?? '-' }}
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark border">
                        Company: {{ ucfirst($application->company_status ?? 'pending') }}
                    </span>
                    <span class="badge bg-light text-dark border">
                        Supervisor: {{ ucfirst($application->supervisor_status ?? 'pending') }}
                    </span>
                    <span class="badge {{ $application->final_status === 'approved' ? 'bg-success' : ($application->final_status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                        Final: {{ ucfirst($application->final_status ?? 'pending') }}
                    </span>
                </div>
            </div>

            @if($application->motivation)
            <p class="text-muted small mt-3 mb-0">{{ $application->motivation }}</p>
            @endif
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-light border rounded-4">
            You have not submitted any applications yet.
        </div>
    </div>
    @endforelse
</div>
@endsection