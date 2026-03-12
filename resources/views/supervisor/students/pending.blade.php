@extends('supervisor.layouts.app')

@section('title', 'Pending Students')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold">Pending Students</h2>
        <p class="text-muted">Approve or reject newly registered students linked to your supervisor code</p>
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

<div class="bg-white rounded-4 border shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>University ID</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Supervisor Code</th>
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
                        <span class="badge bg-warning-subtle text-warning">{{ ucfirst($student->status) }}</span>
                    </td>
                    <td>{{ $student->supervisor_code ?? '-' }}</td>
                    <td class="d-flex gap-2">
                        <form method="POST" action="{{ route('supervisor.students.approve', $student->id) }}">
                            @csrf
                            <button class="btn btn-success btn-sm">
                                <i class="bi bi-check"></i>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('supervisor.students.reject', $student->id) }}">
                            @csrf
                            <button class="btn btn-danger btn-sm">
                                <i class="bi bi-x"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">No pending students found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
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