@extends('supervisor.layouts.app')

@section('title', 'Weekly Tasks Monitoring')

@section('content')
<header class="mb-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Weekly Tasks Monitoring</h2>
        <p class="text-muted mb-0">General workspace for your students + direct access to each student board</p>
    </div>

    <button class="theme-toggle-btn" onclick="toggleTheme()">
        <i id="themeIcon" class="bi bi-moon-stars-fill"></i>
    </button>
</header>

<div class="row row-cols-2 row-cols-sm-2 row-cols-md-4 g-3 g-sm-4 mb-5">
    <div class="col">
        <div class="stat-card" style="background:#eff6ff;">
            <h6>Total Tasks</h6>
            <h3>{{ $totalTasks }}</h3>
        </div>
    </div>
    <div class="col">
        <div class="stat-card" style="background:#f0fdf4;">
            <h6>Completed</h6>
            <h3>{{ $doneCount }}</h3>
        </div>
    </div>
    <div class="col">
        <div class="stat-card" style="background:#ecfeff;">
            <h6>In Progress</h6>
            <h3>{{ $progressCount }}</h3>
        </div>
    </div>
    <div class="col">
        <div class="stat-card" style="background:#ffffff;">
            <h6>Pending</h6>
            <h3>{{ $todoCount }}</h3>
        </div>
    </div>
</div>

<div class="bg-white rounded-4 p-4 border shadow-sm mb-4">
    <h5 class="fw-bold mb-3">Create General Task For All Your Students</h5>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('tasks.supervisor.broadcast') }}" class="row g-3">
        @csrf
        <div class="col-md-6">
            <label class="form-label">Task Title</label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">Due Date</label>
            <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}">
            @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">Label</label>
            <select name="label" class="form-select @error('label') is-invalid @enderror">
                <option value="">No label</option>
                <option value="red" @selected(old('label') === 'red')>Urgent</option>
                <option value="green" @selected(old('label') === 'green')>Low</option>
                <option value="blue" @selected(old('label') === 'blue')>Feature</option>
            </select>
            @error('label') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12">
            <label class="form-label">Details</label>
            <textarea name="details" rows="3" class="form-control @error('details') is-invalid @enderror" placeholder="Task details for students...">{{ old('details') }}</textarea>
            @error('details') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12">
            <button class="btn btn-primary rounded-pill px-4" type="submit">
                <i class="bi bi-send me-1"></i> Publish To All My Students
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-4 p-4 border shadow-sm">
    <h5 class="fw-bold mb-3">Students Private Workspaces</h5>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Program</th>
                    <th>Open Board</th>
                </tr>
            </thead>
            <tbody>
                @forelse($approvedApplications as $application)
                <tr>
                    <td>{{ $application->student->name ?? '-' }}</td>
                    <td>{{ $application->opportunity->title ?? '-' }}</td>
                    <td>
                        <a href="{{ route('tasks.board', $application->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            Open Student Workspace
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted py-3">No approved students found yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
