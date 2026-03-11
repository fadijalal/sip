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
            <span>{{ $currentDate }}</span>
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
        <div class="stat-note">Students + Supervisors</div>
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
        <div class="stat-note">Registered companies</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon icon-orange">
                <i class="fa-regular fa-clipboard"></i>
            </div>
            <div class="trend"><i class="fa-solid fa-arrow-trend-up"></i></div>
        </div>
        <div class="stat-number">{{ $totalAll }}</div>
        <div class="stat-label">Total Accounts</div>
        <div class="stat-note">All system users</div>
    </div>
</div>

<div class="section-card">
    <div class="section-head">
        <div class="section-title">Recent Activity</div>
        <button class="view-all-btn">View All</button>
    </div>

    <div class="activity-list">
        @forelse($recentActivities as $user)
        @php
        $isCompany = $user->role === 'company';
        $isPending = $user->status === 'pending';
        $isActive = $user->status === 'active';

        $iconClass = $isCompany ? 'soft-green' : 'soft-blue';
        $icon = $isCompany ? 'fa-regular fa-building' : 'fa-regular fa-user';

        $title = $isCompany
        ? 'Company registration submitted'
        : 'New supervisor registered';

        $subTitle = $isCompany
        ? ($user->company_name ?: $user->name)
        : $user->name;

        $statusClass = $isActive ? 'status-completed' : 'status-pending';
        $statusText = ucfirst($user->status);
        @endphp

        <div class="activity-item" id="user-row-{{ $user->id }}">
            <div class="activity-left">
                <div class="activity-icon {{ $iconClass }}">
                    <i class="{{ $icon }}"></i>
                </div>
                <div>
                    <div class="activity-title">{{ $title }}</div>
                    <div class="activity-sub">{{ $subTitle }}</div>
                    <div class="activity-date">{{ \Carbon\Carbon::parse($user->created_at)->format('m/d/Y') }}</div>
                </div>
            </div>

            <div class="activity-actions">
                <span class="status-pill {{ $statusClass }}" id="status-pill-{{ $user->id }}">
                    {{ $statusText }}
                </span>

                <button class="icon-btn" type="button" title="View">
                    <i class="fa-regular fa-eye"></i>
                </button>

                @if($isPending)
                <button
                    class="icon-btn primary approve-btn"
                    type="button"
                    title="Approve"
                    data-id="{{ $user->id }}"
                    data-role="{{ $user->role }}">
                    <i class="fa-solid fa-check"></i>
                </button>
                @endif
            </div>
        </div>
        @empty
        <div class="activity-item">
            <div class="activity-left">
                <div>
                    <div class="activity-title">No recent activity found</div>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = '{{ csrf_token() }}';

        document.querySelectorAll('.approve-btn').forEach(button => {
            button.addEventListener('click', async function() {
                const userId = this.dataset.id;
                const role = this.dataset.role;

                let url = '';

                if (role === 'supervisor') {
                    url = `/admin/supervisors/${userId}/status`;
                } else if (role === 'company') {
                    url = `/admin/companies/${userId}/status`;
                }

                if (!url) return;

                this.disabled = true;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'active'
                        })
                    });

                    const result = await response.json();

                    if (result.status) {
                        const statusPill = document.getElementById(`status-pill-${userId}`);
                        statusPill.classList.remove('status-pending');
                        statusPill.classList.add('status-completed');
                        statusPill.textContent = 'Active';

                        this.remove();
                    } else {
                        this.disabled = false;
                        alert(result.message || 'Something went wrong');
                    }
                } catch (error) {
                    this.disabled = false;
                    alert('Failed to update user status');
                }
            });
        });
    });
</script>
@endsection