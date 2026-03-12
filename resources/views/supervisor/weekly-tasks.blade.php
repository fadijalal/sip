@extends('supervisor.layouts.app')

@section('title', 'Weekly Tasks Monitoring')

@section('content')
<header class="mb-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Weekly Tasks Monitoring</h2>
        <p class="text-muted mb-0">Design preview only — backend will be connected later</p>
    </div>

    <button class="theme-toggle-btn" onclick="toggleTheme()">
        <i id="themeIcon" class="bi bi-moon-stars-fill"></i>
    </button>
</header>

<div class="row row-cols-2 row-cols-sm-3 row-cols-md-3 row-cols-lg-5 g-3 g-sm-4 mb-5">
    <div class="col">
        <div class="stat-card" style="background:#eff6ff;">
            <h6>Total Tasks</h6>
            <h3>8</h3>
        </div>
    </div>
    <div class="col">
        <div class="stat-card" style="background:#f0fdf4;">
            <h6>Completed</h6>
            <h3>3</h3>
        </div>
    </div>
    <div class="col">
        <div class="stat-card" style="background:#ecfeff;">
            <h6>In Progress</h6>
            <h3>2</h3>
        </div>
    </div>
    <div class="col">
        <div class="stat-card" style="background:#ffffff;">
            <h6>Pending</h6>
            <h3>1</h3>
        </div>
    </div>
    <div class="col">
        <div class="stat-card" style="background:#fef2f2;">
            <h6>Late</h6>
            <h3>2</h3>
        </div>
    </div>
</div>

<div class="bg-white rounded-4 p-4 border shadow-sm">
    <h5 class="fw-bold mb-3">Weekly Tasks UI Preview</h5>
    <p class="text-muted mb-0">
        This screen is ready visually and will be connected after completing the task system logic.
    </p>
</div>
@endsection