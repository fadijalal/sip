@extends('admin.layouts.app')

@section('title', 'Manage Companies')

@section('content')
<style>
    .companies-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(180px, 1fr));
        gap: 22px;
        margin-bottom: 34px;
    }

    .companies-stat-card {
        border-radius: 24px;
        padding: 28px 28px 24px;
        min-height: 168px;
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.08);
        border: 1px solid #eef2f7;
        background: #fff;
    }

    .companies-stat-card .label {
        font-size: 15px;
        color: #4b5563;
        margin-bottom: 28px;
        font-weight: 500;
    }

    .companies-stat-card .value {
        font-size: 40px;
        font-weight: 500;
        line-height: 1;
        color: #0f172a;
    }

    .companies-stat-card.green {
        background: #edf8f1;
    }

    .companies-stat-card.green .value {
        color: #0f8a3a;
    }

    .companies-stat-card.orange {
        background: #f9f2e4;
    }

    .companies-stat-card.orange .value {
        color: #d94c0f;
    }

    .companies-stat-card.blue {
        background: #eef3ff;
    }

    .companies-stat-card.blue .value {
        color: #2454ff;
    }

    .companies-table-card {
        background: #fff;
        border-radius: 28px;
        border: 1px solid #edf0f5;
        box-shadow: 0 22px 38px rgba(15, 23, 42, 0.10);
        overflow: hidden;
    }

    .companies-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .companies-table {
        width: 100%;
        border-collapse: collapse;
    }

    .companies-table thead th {
        background: #fbfcfe;
        color: #3f4a5c;
        font-size: 15px;
        font-weight: 700;
        padding: 20px 18px;
        text-align: left;
        border-bottom: 1px solid #edf0f5;
        white-space: nowrap;
    }

    .companies-table tbody td {
        padding: 22px 18px;
        border-bottom: 1px solid #edf0f5;
        vertical-align: middle;
        color: #111827;
        font-size: 15px;
    }

    .companies-table tbody tr:last-child td {
        border-bottom: none;
    }

    .company-cell {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .company-icon-box {
        width: 50px;
        height: 50px;
        min-width: 50px;
        border-radius: 18px;
        background: #f97316;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .company-title {
        font-size: 17px;
        font-weight: 500;
        line-height: 1.45;
        color: #1f2937;
    }

    .contact-stack {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #667085;
        font-size: 14px;
        white-space: nowrap;
    }

    .contact-item i {
        font-size: 15px;
        color: #667085;
    }

    .program-box {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
    }

    .program-box i {
        color: #2454ff;
        font-size: 18px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 88px;
        height: 34px;
        padding: 0 16px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 500;
    }

    .status-approved {
        background: #cfeeda;
        color: #138a43;
    }

    .status-pending {
        background: #f6dfbd;
        color: #d05a11;
    }

    .status-rejected {
        background: #fee2e2;
        color: #dc2626;
    }

    .actions-box {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: nowrap;
    }

    .table-action-btn {
        width: 46px;
        height: 46px;
        min-width: 46px;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: .2s ease;
    }

    .table-action-btn i {
        line-height: 1;
    }

    .table-action-btn.view-btn {
        color: #111827;
    }

    .table-action-btn.approve-btn {
        background: #10b245;
        border-color: #10b245;
        color: #fff;
    }

    .table-action-btn.reject-btn {
        background: #ff0000;
        border-color: #ff0000;
        color: #fff;
    }

    .table-action-btn.delete-btn {
        background: #ff0000;
        border-color: #ff0000;
        color: #fff;
    }

    .table-action-btn.disabled-state {
        opacity: .55;
        pointer-events: none;
    }

    @media (max-width: 1200px) {
        .companies-stats {
            grid-template-columns: repeat(2, minmax(180px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .companies-stats {
            grid-template-columns: 1fr;
        }

        .companies-table {
            min-width: 980px;
        }
    }
</style>

<div class="topbar">
    <div>
        <div class="page-title">Manage Companies</div>
        <div class="page-subtitle">View and manage companies</div>
    </div>
</div>

<div class="companies-stats">
    <div class="companies-stat-card">
        <div class="label">Total Companies</div>
        <div class="value">{{ $totalCompanies }}</div>
    </div>

    <div class="companies-stat-card green">
        <div class="label">Approved</div>
        <div class="value">{{ $approvedCompanies }}</div>
    </div>

    <div class="companies-stat-card orange">
        <div class="label">Pending</div>
        <div class="value">{{ $pendingCompanies }}</div>
    </div>

    <div class="companies-stat-card blue">
        <div class="label">Total Programs</div>
        <div class="value">18</div>
    </div>
</div>

<div class="companies-table-card">
    <div class="companies-table-wrap">
        <table class="companies-table">
            <thead>
                <tr>
                    <th style="width:26%;">Company</th>
                    <th style="width:26%;">Contact</th>
                    <th style="width:14%;">Industry</th>
                    <th style="width:10%;">Programs</th>
                    <th style="width:12%;">Status</th>
                    <th style="width:12%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                @php
                $isPending = $company->status === 'pending';
                $isActive = $company->status === 'active';
                $isRejected = $company->status === 'rejected';
                @endphp

                <tr id="company-row-{{ $company->id }}">
                    <td>
                        <div class="company-cell">
                            <div class="company-icon-box">
                                <i class="fa-regular fa-building"></i>
                            </div>

                            <div>
                                <div class="company-title">
                                    {{ $company->company_name ?? $company->name }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <div class="contact-stack">
                            <div class="contact-item">
                                <i class="fa-regular fa-envelope"></i>
                                <span>{{ $company->email ?? '-' }}</span>
                            </div>

                            <div class="contact-item">
                                <i class="fa-solid fa-phone"></i>
                                <span>{{ $company->phone_number ?? '-' }}</span>
                            </div>
                        </div>
                    </td>

                    <td>Technology</td>

                    <td>
                        <div class="program-box">
                            <i class="fa-regular fa-clipboard"></i>
                            <span>3</span>
                        </div>
                    </td>

                    <td>
                        <span
                            id="company-status-pill-{{ $company->id }}"
                            class="status-badge
                                    {{ $isActive ? 'status-approved' : '' }}
                                    {{ $isPending ? 'status-pending' : '' }}
                                    {{ $isRejected ? 'status-rejected' : '' }}">
                            {{ $isActive ? 'Approved' : ucfirst($company->status) }}
                        </span>
                    </td>

                    <td>
                        <div class="actions-box">
                            <button class="table-action-btn view-btn" type="button" title="View">
                                <i class="fa-regular fa-eye"></i>
                            </button>

                            @if($isPending)
                            <button
                                class="table-action-btn approve-btn company-action-btn"
                                type="button"
                                title="Approve"
                                data-id="{{ $company->id }}"
                                data-action="active">
                                <i class="fa-solid fa-check"></i>
                            </button>

                            <button
                                class="table-action-btn reject-btn company-action-btn"
                                type="button"
                                title="Reject"
                                data-id="{{ $company->id }}"
                                data-action="reject">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                            @else
                            <button
                                class="table-action-btn delete-btn company-action-btn"
                                type="button"
                                title="Delete"
                                data-id="{{ $company->id }}"
                                data-action="delete">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                            @endif

                            @if($isPending)
                            <button
                                class="table-action-btn delete-btn company-action-btn"
                                type="button"
                                title="Delete"
                                data-id="{{ $company->id }}"
                                data-action="delete"
                                style="display:none;">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">No companies found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = '{{ csrf_token() }}';

        document.querySelectorAll('.company-action-btn').forEach(button => {
            button.addEventListener('click', async function() {
                const id = this.dataset.id;
                const action = this.dataset.action;

                if (action === 'delete' && !confirm('Are you sure you want to delete this company?')) {
                    return;
                }

                this.disabled = true;
                this.classList.add('disabled-state');

                try {
                    const response = await fetch(`/admin/companies/${id}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            action
                        })
                    });

                    const result = await response.json();

                    if (result.status) {
                        if (action === 'delete') {
                            document.getElementById(`company-row-${id}`).remove();
                            return;
                        }

                        const statusPill = document.getElementById(`company-status-pill-${id}`);
                        const row = document.getElementById(`company-row-${id}`);

                        if (action === 'active') {
                            statusPill.className = 'status-badge status-approved';
                            statusPill.textContent = 'Approved';
                        }

                        if (action === 'reject') {
                            statusPill.className = 'status-badge status-rejected';
                            statusPill.textContent = 'Rejected';
                        }

                        row.querySelectorAll(`[data-id="${id}"][data-action="active"], [data-id="${id}"][data-action="reject"]`)
                            .forEach(btn => btn.remove());
                    } else {
                        this.disabled = false;
                        this.classList.remove('disabled-state');
                        alert(result.message || 'Something went wrong');
                    }
                } catch (error) {
                    this.disabled = false;
                    this.classList.remove('disabled-state');
                    alert('Failed to update company');
                }
            });
        });
    });
</script>
@endsection