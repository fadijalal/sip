@extends('supervisor.layouts.app')

@section('title', 'Supervisor Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold">Supervisor Dashboard</h3>
        <p class="text-muted">Monitor student progress and training activities</p>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="bg-white border rounded-pill px-3 py-2 d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-key text-primary"></i>
            <span class="fw-semibold" id="supervisorCodeText">{{ $supervisor->supervisor_code ?? '-' }}</span>
            <button class="btn btn-sm btn-light border-0 p-1" onclick="copySupervisorCode()" title="Copy code">
                <i class="bi bi-copy"></i>
            </button>
        </div>

        <button class="theme-toggle-btn" onclick="toggleTheme()">
            <i id="themeIcon" class="bi bi-moon-stars-fill"></i>
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-3 g-lg-4 mb-5">
    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
        <div class="stat-card" style="background: rgba(59,130,246,.1); border-left:5px solid #3b82f6;">
            <div class="icon-rounded" style="background:#3b82f6;">
                <i class="bi bi-people-fill"></i>
            </div>
            <small class="text-muted d-block">Total Students</small>
            <h2 class="fw-bold">{{ $totalStudents }}</h2>
            <span class="text-primary small fw-bold">All linked students</span>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
        <div class="stat-card" style="background: rgba(245,158,11,.1); border-left:5px solid #f59e0b;">
            <div class="icon-rounded" style="background:#f59e0b;">
                <i class="bi bi-clock-history"></i>
            </div>
            <small class="text-muted d-block">Pending Students</small>
            <h2 class="fw-bold">{{ $pendingStudents }}</h2>
            <span class="text-warning small fw-bold">Waiting approval</span>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
        <div class="stat-card" style="background: rgba(34,197,94,.1); border-left:5px solid #22c55e;">
            <div class="icon-rounded" style="background:#22c55e;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <small class="text-muted d-block">Active Students</small>
            <h2 class="fw-bold">{{ $activeStudents }}</h2>
            <span class="text-success small fw-bold">Approved accounts</span>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
        <div class="stat-card" style="background: rgba(239,68,68,.1); border-left:5px solid #ef4444;">
            <div class="icon-rounded" style="background:#ef4444;">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <small class="text-muted d-block">Rejected Students</small>
            <h2 class="fw-bold">{{ $rejectedStudents }}</h2>
            <span class="text-danger small fw-bold">Rejected accounts</span>
        </div>
    </div>
</div>

<div class="bg-white p-3 p-md-4 rounded-4 border text-center mb-4">
    <h5 class="fw-bold mb-1">Students Linked To You</h5>
    <p class="text-muted small">Progress is calculated from approval date until training duration ends</p>
</div>

<div class="row g-4">
    @forelse($studentCards as $item)
    @php
    $student = $item['student'];
    $opportunity = $item['opportunity'];
    $progress = $item['progress'];
    $statusLabel = $item['status_label'];
    $isOnTrack = $statusLabel === 'On Track';
    $initials = strtoupper(substr($student->name ?? 'ST', 0, 1)) . strtoupper(substr(explode(' ', trim($student->name ?? ''))[1] ?? '', 0, 1));
    @endphp

    <div class="col-xl-6 col-lg-12 col-md-12">
        <div class="student-card">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div class="d-flex gap-3">
                    <div class="avatar-lg">{{ $initials ?: 'ST' }}</div>
                    <div>
                        <h6 class="fw-bold mb-0">{{ $student->name ?? '-' }}</h6>
                        <small class="text-muted">{{ $student->email ?? '-' }}</small>
                    </div>
                </div>

                <span class="status-badge {{ $isOnTrack ? 'status-on-track' : 'status-at-risk' }}">
                    <i class="bi bi-dot"></i> {{ $statusLabel }}
                </span>
            </div>

            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                    <i class="bi bi-briefcase"></i> {{ $opportunity->title ?? '-' }}
                </div>
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <i class="bi bi-calendar-range"></i> {{ $opportunity->duration ?? 0 }} month(s)
                </div>
            </div>

            <div class="mb-4">
                <div class="progress-label">
                    <span>Training Progress</span>
                    <span>{{ $progress }}%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar"
                        style="width: {{ $progress }}%; background-color: {{ $isOnTrack ? '#22c55e' : '#f59e0b' }};">
                    </div>
                </div>
            </div>

            <div class="card-actions">
                <a href="{{ route('supervisor.students.index') }}" class="btn-outline-card">
                    <i class="bi bi-eye"></i> View
                </a>
                <a href="{{ route('supervisor.students.pending') }}" class="btn-outline-card">
                    <i class="bi bi-person-check"></i> Manage
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-light border">No approved students linked to this supervisor yet.</div>
    </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
    function copySupervisorCode() {
        const text = document.getElementById('supervisorCodeText').innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('Supervisor code copied');
        });
    }
</script>
@endpush