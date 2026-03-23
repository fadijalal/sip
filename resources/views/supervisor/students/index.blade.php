@extends('supervisor.layouts.app')

@section('title', 'Students')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Students</h2>
        <p class="text-muted mb-0">Manage all students linked to your supervisor code</p>
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
    <div class="col-12 col-sm-6 col-md-4">
        <div class="stat-card">
            <div class="icon-rounded" style="background:#3b82f6;">
                <i class="bi bi-people-fill"></i>
            </div>
            <small class="text-muted d-block">Total Students</small>
            <h2 class="fw-bold">{{ $totalStudents }}</h2>
            <span class="text-primary small fw-bold">All linked students</span>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4">
        <div class="stat-card">
            <div class="icon-rounded" style="background:#f59e0b;">
                <i class="bi bi-clock-history"></i>
            </div>
            <small class="text-muted d-block">Pending Students</small>
            <h2 class="fw-bold">{{ $totalPending }}</h2>
            <span class="text-warning small fw-bold">Need approval</span>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4">
        <div class="stat-card">
            <div class="icon-rounded" style="background:#22c55e;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <small class="text-muted d-block">Approved Students</small>
            <h2 class="fw-bold">{{ $totalApproved }}</h2>
            <span class="text-success small fw-bold">Active accounts</span>
        </div>
    </div>
</div>

{{-- Pending Section --}}
<div class="bg-white rounded-4 border p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">Pending Students</h4>
            <p class="text-muted mb-0 small">Students waiting for your approval</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>University ID</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($pendingStudents as $student)
                <tr>
                    <td class="fw-semibold">{{ $student->name }}</td>
                    <td>{{ $student->university_id ?? '-' }}</td>
                    <td>{{ $student->email ?? '-' }}</td>
                    <td>{{ $student->phone_number ?? '-' }}</td>
                    <td>
                        <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">
                            {{ ucfirst($student->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <form method="POST" action="{{ route('supervisor.students.approve', $student->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm rounded-3">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>

                            <form method="POST" action="{{ route('supervisor.students.reject', $student->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm rounded-3">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No pending students found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Approved Section --}}
<div class="bg-white rounded-4 border p-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">Approved Students</h4>
            <p class="text-muted mb-0 small">Students approved and currently active in training</p>
        </div>
    </div>

    <div class="row g-4">
        @forelse($approvedStudents as $item)
        @php
        $student = $item['student'];
        $opportunity = $item['opportunity'];
        $progress = $item['progress'];
        $statusLabel = $item['status_label'];
        $isOnTrack = $statusLabel === 'On Track';
        $initials = strtoupper(substr($student->name ?? 'ST', 0, 1)) . strtoupper(substr(explode(' ', trim($student->name ?? ''))[1] ?? '', 0, 1));
        @endphp

        <div class="col-xl-6 col-lg-6 col-md-12">
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
                        <i class="bi bi-briefcase"></i>
                        {{ $opportunity->title ?? '-' }}
                    </div>

                    <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                        <i class="bi bi-calendar-range"></i>
                        {{ $opportunity->duration ?? 0 }} month(s)
                    </div>

                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <i class="bi bi-person-badge"></i>
                        {{ $student->university_id ?? '-' }}
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
                    @if($item['application'])<a href="{{ route('tasks.board', $item['application']->id) }}" class="btn-outline-card" style="text-decoration:none;"><i class="bi bi-eye"></i> Tasks</a>@else<button class="btn-outline-card" disabled><i class="bi bi-eye"></i> No Tasks</button>@endif
                    <button class="btn-outline-card">
                        <i class="bi bi-chat-left-text"></i> Note
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-light border text-center">No approved students found</div>
        </div>
        @endforelse
    </div>
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
