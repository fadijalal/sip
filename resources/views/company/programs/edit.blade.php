@extends('company.layouts.app')

@section('title', 'Edit Program')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('company.programs.index') }}" class="text-decoration-none text-muted">
                <i class="bi bi-chevron-left me-1"></i> Back to Programs
            </a>
            <h2 class="fw-bold mt-3 mb-1">Edit Program</h2>
            <p class="text-muted">Update your training opportunity</p>
        </div>

        <button class="theme-toggle-btn" onclick="toggleTheme()">
            <i id="themeToggleIcon" class="bi bi-moon-stars-fill"></i>
        </button>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-4 p-4 shadow-sm border">
        <form method="POST" action="{{ route('company.programs.update', $program->id) }}">
            @csrf

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Program Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $program->title) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Type *</label>
                    <select name="type" class="form-select" required>
                        <option value="training" {{ old('type', $program->type) == 'training' ? 'selected' : '' }}>Training</option>
                        <option value="job" {{ old('type', $program->type) == 'job' ? 'selected' : '' }}>Job</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Field</label>
                    <input type="text" name="field" class="form-control" value="{{ old('field', $program->field) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $program->city) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Work Type</label>
                    <select name="work_type" class="form-select">
                        <option value="">Select work type</option>
                        <option value="onsite" {{ old('work_type', $program->work_type) == 'onsite' ? 'selected' : '' }}>Onsite</option>
                        <option value="remote" {{ old('work_type', $program->work_type) == 'remote' ? 'selected' : '' }}>Remote</option>
                        <option value="hybrid" {{ old('work_type', $program->work_type) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Education Level</label>
                    <input type="text" name="education_level" class="form-control" value="{{ old('education_level', $program->education_level) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Duration (months)</label>
                    <input type="number" name="duration" class="form-control" value="{{ old('duration', $program->duration) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Deadline</label>
                    <input type="date" name="deadline" class="form-control" value="{{ old('deadline', $program->deadline) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="open" {{ old('status', $program->status) == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ old('status', $program->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Description *</label>
                    <textarea name="description" class="form-control" rows="5" required>{{ old('description', $program->description) }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Requirements</label>
                    <textarea name="requirements" class="form-control" rows="4">{{ old('requirements', $program->requirements) }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('company.programs.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Program</button>
            </div>
        </form>
    </div>
</div>
@endsection