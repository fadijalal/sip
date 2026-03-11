@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="topbar">
    <div>
        <div class="page-title">Admin Dashboard</div>
        <div class="page-subtitle">Manage users, companies, and training programs</div>
    </div>

    <div class="topbar-right">
        <div class="date-pill">
            <i class="fa-regular fa-calendar"></i>
            <span>Dec 28, 2025</span>
        </div>

        <div class="avatar-pill">AD</div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon icon-blue">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="trend"><i class="fa-solid fa-arrow-trend-up"></i></div>
        </div>
        <div class="stat-number">{{ $totalUsers }}</div>
        <div class="stat-label">Total Users</div>
        <div class="stat-note">+12 this week</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon icon-green">
                <i class="fa-regular fa-building"></i>
            </div>
            <div class="trend"><i class="fa-solid fa-arrow-trend-up"></i></div>
        </div>
        <div class="stat-number">{{ $totalCompanies }}</div>
        <div class="stat-label">Total Companies</div>
        <div class="stat-note">+3 pending approval</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon icon-orange">
                <i class="fa-regular fa-clipboard"></i>
            </div>
            <div class="trend"><i class="fa-solid fa-arrow-trend-up"></i></div>
        </div>
        <div class="stat-number">{{ $totalUsers }}</div>
        <div class="stat-label">Active Programs</div>
        <div class="stat-note">+8 this month</div>
    </div>
</div>

<div class="section-card">
    <div class="section-head">
        <div class="section-title">Recent Activity</div>
        <button class="view-all-btn">View All</button>
    </div>

    <div class="activity-list">
        <div class="activity-item">
            <div class="activity-left">
                <div class="activity-icon soft-blue">
                    <i class="fa-regular fa-user"></i>
                </div>
                <div>
                    <div class="activity-title">New supervisor registered</div>
                    <div class="activity-sub">Dr. Sarah Chen</div>
                    <div class="activity-date">12/28/2024</div>
                </div>
            </div>

            <div class="activity-actions">
                <span class="status-pill status-completed">Completed</span>
                <button class="icon-btn"><i class="fa-regular fa-eye"></i></button>
            </div>
        </div>

        <div class="activity-item">
            <div class="activity-left">
                <div class="activity-icon soft-green">
                    <i class="fa-regular fa-building"></i>
                </div>
                <div>
                    <div class="activity-title">Company registration pending approval</div>
                    <div class="activity-sub">TechStart Solutions</div>
                    <div class="activity-date">12/28/2024</div>
                </div>
            </div>

            <div class="activity-actions">
                <span class="status-pill status-pending">Pending</span>
                <button class="icon-btn"><i class="fa-regular fa-eye"></i></button>
                <button class="icon-btn primary"><i class="fa-solid fa-check"></i></button>
            </div>
        </div>

        <div class="activity-item">
            <div class="activity-left">
                <div class="activity-icon soft-orange">
                    <i class="fa-regular fa-clipboard"></i>
                </div>
                <div>
                    <div class="activity-title">New training program submitted</div>
                    <div class="activity-sub">DataCorp - Data Science Training</div>
                    <div class="activity-date">12/27/2024</div>
                </div>
            </div>

            <div class="activity-actions">
                <span class="status-pill status-pending">Pending</span>
                <button class="icon-btn"><i class="fa-regular fa-eye"></i></button>
                <button class="icon-btn primary"><i class="fa-solid fa-check"></i></button>
            </div>
        </div>

        <div class="activity-item">
            <div class="activity-left">
                <div class="activity-icon soft-blue">
                    <i class="fa-regular fa-user"></i>
                </div>
                <div>
                    <div class="activity-title">Student registration completed</div>
                    <div class="activity-sub">Emma Wilson</div>
                    <div class="activity-date">12/27/2024</div>
                </div>
            </div>

            <div class="activity-actions">
                <span class="status-pill status-completed">Completed</span>
                <button class="icon-btn"><i class="fa-regular fa-eye"></i></button>
            </div>
        </div>

        <div class="activity-item">
            <div class="activity-left">
                <div class="activity-icon soft-green">
                    <i class="fa-regular fa-building"></i>
                </div>
                <div>
                    <div class="activity-title">Company approved</div>
                    <div class="activity-sub">InnovateTech Inc.</div>
                    <div class="activity-date">12/26/2024</div>
                </div>
            </div>

            <div class="activity-actions">
                <span class="status-pill status-completed">Completed</span>
                <button class="icon-btn"><i class="fa-regular fa-eye"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection