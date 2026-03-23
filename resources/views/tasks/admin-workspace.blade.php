@extends('admin.layouts.app')

@section('title', 'Global Tasks Workspace')

@section('content')
<div class="topbar">
    <div>
        <div class="page-title">Global Tasks Workspace</div>
        <div class="page-subtitle">Create one task for all approved trainees</div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-4 border shadow-sm p-4 mb-4">
    <h5 class="fw-bold mb-3">Broadcast Task to All Approved Students</h5>
    <form method="POST" action="{{ route('tasks.admin.broadcast') }}" class="row g-3">
        @csrf
        <div class="col-md-4">
            <input type="text" name="title" class="form-control" placeholder="Task title" required>
        </div>
        <div class="col-md-4">
            <input type="date" name="due_date" class="form-control">
        </div>
        <div class="col-md-4">
            <select name="label" class="form-select">
                <option value="">Label</option>
                <option value="red">Urgent</option>
                <option value="green">Low</option>
                <option value="blue">Feature</option>
            </select>
        </div>
        <div class="col-12">
            <textarea name="details" rows="3" class="form-control" placeholder="Task details"></textarea>
        </div>
        <div class="col-12">
            <button class="btn btn-primary">Broadcast Task</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-4 border shadow-sm p-4">
    <h6 class="fw-bold">Approved Students Count: {{ $approvedApplications->count() }}</h6>
    <div class="table-responsive mt-3">
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Program</th>
                    <th>Open Workspace</th>
                </tr>
            </thead>
            <tbody>
                @forelse($approvedApplications as $app)
                <tr>
                    <td>{{ $app->student->name ?? '-' }}</td>
                    <td>{{ $app->opportunity->title ?? '-' }}</td>
                    <td><a class="btn btn-sm btn-outline-primary" href="{{ route('tasks.board', $app->id) }}">Open</a></td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted">No approved applications</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
