@extends('admin.layouts.app')

@section('title', 'Users Management')

@section('content')
<div class="topbar">
    <div>
        <div class="page-title">Users Management</div>
        <div class="page-subtitle">Students and supervisors only</div>
    </div>

    <div class="topbar-right">
        <div class="date-pill">
            <i class="fa-regular fa-calendar"></i>
            <span>Dec 28, 2025</span>
        </div>

        <div class="avatar-pill">AD</div>
    </div>
</div>

<div class="table-card">
    <div class="section-head">
        <div class="section-title">Users List</div>
    </div>

    <div class="filters-bar">
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="pending">Pending</button>
        <button class="filter-btn" data-filter="approved">Approved</button>
        <button class="filter-btn" data-filter="rejected">Rejected</button>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <tr>
                    <td colspan="5" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    let allUsers = [];
    let currentStatusFilter = 'all';

    async function loadUsers() {
        try {
            const response = await fetch("{{ route('admin.users.data') }}");
            const result = await response.json();

            if (result.status) {
                allUsers = result.data;
                renderUsers();
            }
        } catch (error) {
            document.getElementById('usersTableBody').innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger">Failed to load users</td>
                </tr>
            `;
        }
    }

    function renderUsers() {
        const tbody = document.getElementById('usersTableBody');

        let filteredUsers = allUsers;

        if (currentStatusFilter !== 'all') {
            filteredUsers = allUsers.filter(user =>
                (user.status ?? '').toLowerCase() === currentStatusFilter
            );
        }

        if (filteredUsers.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center">No users found</td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = filteredUsers.map((user, index) => {
            const roleClass = user.role === 'student' ? 'role-student' : 'role-supervisor';

            let statusClass = 'status-pending';
            if ((user.status ?? '').toLowerCase() === 'approved') {
                statusClass = 'status-completed';
            } else if ((user.status ?? '').toLowerCase() === 'rejected') {
                statusClass = 'status-pending';
            }

            return `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <div class="user-name">${user.name ?? '-'}</div>
                        <div class="user-email">${user.email ?? '-'}</div>
                    </td>
                    <td>
                        <span class="role-badge ${roleClass}">
                            ${capitalize(user.role)}
                        </span>
                    </td>
                    <td>
                        <span class="status-pill ${statusClass}">
                            ${capitalize(user.status ?? 'pending')}
                        </span>
                    </td>
                    <td>${formatDate(user.created_at)}</td>
                </tr>
            `;
        }).join('');
    }

    function capitalize(text) {
        if (!text) return '-';
        return text.charAt(0).toUpperCase() + text.slice(1);
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US');
    }

    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            currentStatusFilter = this.getAttribute('data-filter');
            renderUsers();
        });
    });

    loadUsers();
</script>
@endsection