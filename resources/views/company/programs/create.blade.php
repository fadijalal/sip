@extends('company.layouts.app')

@section('title', 'Create Training Program')

@push('styles')
<style>
    .custom-card {
        background: var(--card-bg);
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, .03);
        padding: 30px;
        border: 1px solid var(--border-color);
    }

    .form-control,
    .form-select,
    textarea {
        background-color: var(--card-bg);
        color: var(--text-main);
        border: 1.5px solid var(--border-color);
        border-radius: 10px;
        padding: 12px 15px;
        font-size: .95rem;
    }

    .btn-custom-outline {
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 10px 25px;
        font-weight: 500;
        color: var(--text-main);
    }

    .btn-primary-custom {
        background-color: var(--accent-purple);
        border: none;
        border-radius: 10px;
        padding: 10px 30px;
        font-weight: 500;
        color: white;
        box-shadow: 0 4px 10px rgba(59, 82, 255, .2);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('company.programs.index') }}" class="text-decoration-none text-muted">
                <i class="bi bi-chevron-left me-1"></i> Back to Programs
            </a>
            <h2 class="fw-bold mt-3 mb-1">Create Training Program</h2>
            <p class="text-muted">Fill in the details to create a new training opportunity</p>
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

    <div class="custom-card">
        <form method="POST" action="{{ route('company.programs.store') }}">
            @csrf

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Program Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Type *</label>
                    <select name="type" class="form-select" required>
                        <option value="">Select type</option>
                        <option value="training" {{ old('type') == 'training' ? 'selected' : '' }}>Training</option>
                        <option value="job" {{ old('type') == 'job' ? 'selected' : '' }}>Job</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Field</label>
                    <input type="text" name="field" class="form-control" value="{{ old('field') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Work Type</label>
                    <select name="work_type" class="form-select">
                        <option value="">Select work type</option>
                        <option value="onsite" {{ old('work_type') == 'onsite' ? 'selected' : '' }}>Onsite</option>
                        <option value="remote" {{ old('work_type') == 'remote' ? 'selected' : '' }}>Remote</option>
                        <option value="hybrid" {{ old('work_type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Education Level</label>
                    <input type="text" name="education_level" class="form-control" value="{{ old('education_level') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Duration (months)</label>
                    <input type="number" name="duration" class="form-control" value="{{ old('duration') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Deadline</label>
                    <input type="date" name="deadline" class="form-control" value="{{ old('deadline') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="open" {{ old('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Description *</label>
                    <textarea name="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Requirements</label>
                    <textarea name="requirements" class="form-control" rows="4">{{ old('requirements') }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('company.programs.index') }}" class="btn btn-custom-outline">Cancel</a>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom">Create Program</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection