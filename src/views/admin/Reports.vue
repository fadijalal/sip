<template>
  <div class="admin-reports-page">
    <!-- Header -->
    <div class="header-section mb-4">
      <h2 class="fw-bold mb-1">{{ t('manage_reports') }}</h2>
      <p class="text-muted mb-0">{{ t('view_manage_reports') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-5">
      <div class="col-md-4" v-for="stat in reportStats" :key="stat.key">
        <div class="stat-card">
          <div class="stat-icon" :class="stat.iconClass">
            <i :class="stat.icon"></i>
          </div>
          <div class="stat-content">
            <p class="text-muted mb-1">{{ t(stat.label) }}</p>
            <h3 class="fw-bold mb-0">{{ stat.value }}</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-5">
      <div class="col-lg-6">
        <div class="chart-card">
          <h6 class="fw-bold mb-4">
            <i class="bi bi-bar-chart-fill text-primary me-2"></i>
            {{ t('user_registrations') }}
          </h6>
          <div class="chart-container">
            <canvas id="usersChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="chart-card">
          <h6 class="fw-bold mb-4">
            <i class="bi bi-bar-chart-fill text-success me-2"></i>
            {{ t('program_completions') }}
          </h6>
          <div class="chart-container">
            <canvas id="programsChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Reports Table -->
    <div class="reports-section">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">{{ t('recent_reports') }}</h5>
        <button class="btn btn-primary btn-sm" @click="generateReport">
          <i class="bi bi-plus-circle me-2"></i>
          {{ t('generate_report') }}
        </button>
      </div>

      <div class="table-container">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>{{ t('report_name') }}</th>
                <th>{{ t('type') }}</th>
                <th>{{ t('generated') }}</th>
                <th>{{ t('format') }}</th>
                <th>{{ t('size') }}</th>
                <th>{{ t('actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="report in reports" :key="report.id">
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <i :class="report.icon" class="fs-4" :style="{ color: report.color }"></i>
                    <span class="fw-bold">{{ t(report.name) }}</span>
                  </div>
                </td>
                <td>{{ t(report.type) }}</td>
                <td>{{ report.generatedDate }}</td>
                <td>{{ report.format }}</td>
                <td>{{ report.size }}</td>
                <td>
                  <div class="action-buttons">
                    <button class="btn-action" @click="downloadReport(report)" title="Download">
                      <i class="bi bi-download"></i>
                    </button>
                    <button class="btn-action" @click="previewReport(report)" title="Preview">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn-action text-danger" @click="deleteReport(report)" title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from '@/composables/useI18n'
import Chart from 'chart.js/auto'

const { t } = useI18n()

// Stats
const reportStats = ref([
  {
    key: 'total',
    icon: 'bi bi-file-earmark-text',
    iconClass: 'bg-primary text-white',
    label: 'total_reports',
    value: '24'
  },
  {
    key: 'monthly',
    icon: 'bi bi-calendar-check',
    iconClass: 'bg-success text-white',
    label: 'reports_this_month',
    value: '8'
  },
  {
    key: 'pending',
    icon: 'bi bi-clock-history',
    iconClass: 'bg-warning text-white',
    label: 'pending_generation',
    value: '3'
  }
])

// Reports data
const reports = ref([
  {
    id: 1,
    name: 'monthly_performance',
    type: 'performance',
    generatedDate: '2024-12-27',
    format: 'PDF',
    size: '2.4 MB',
    icon: 'bi bi-file-earmark-pdf',
    color: '#dc2626'
  },
  {
    id: 2,
    name: 'company_activity',
    type: 'activity',
    generatedDate: '2024-12-26',
    format: 'Excel',
    size: '1.8 MB',
    icon: 'bi bi-file-earmark-spreadsheet',
    color: '#22c55e'
  },
  {
    id: 3,
    name: 'user_statistics',
    type: 'statistics',
    generatedDate: '2024-12-25',
    format: 'CSV',
    size: '956 KB',
    icon: 'bi bi-file-earmark-code',
    color: '#3b82f6'
  }
])

// Initialize charts
const initCharts = () => {
  // Users Chart
  const usersCtx = document.getElementById('usersChart')?.getContext('2d')
  if (usersCtx) {
    new Chart(usersCtx, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
          label: t('new_users'),
          data: [25, 40, 30, 55, 45, 70],
          backgroundColor: '#3b82f6',
          borderRadius: 8,
          barThickness: 30
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { display: false },
          x: { grid: { display: false }, ticks: { color: '#9ca3af' } }
        }
      }
    })
  }

  // Programs Chart
  const programsCtx = document.getElementById('programsChart')?.getContext('2d')
  if (programsCtx) {
    new Chart(programsCtx, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
          label: t('completions'),
          data: [15, 30, 45, 35, 60, 50],
          backgroundColor: '#22c55e',
          borderRadius: 8,
          barThickness: 30
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { display: false },
          x: { grid: { display: false }, ticks: { color: '#9ca3af' } }
        }
      }
    })
  }
}

// Actions
const generateReport = () => {
  alert(t('generating_new_report'))
}

const downloadReport = (report) => {
  alert(t('downloading_report', { name: t(report.name) }))
}

const previewReport = (report) => {
  alert(t('previewing_report', { name: t(report.name) }))
}

const deleteReport = (report) => {
  if (confirm(t('confirm_delete_report'))) {
    reports.value = reports.value.filter(r => r.id !== report.id)
  }
}

onMounted(() => {
  initCharts()
})
</script>

<style scoped>
.admin-reports-page {
  padding: 20px 0;
}

.stat-card {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 24px;
  border: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  gap: 20px;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}

.stat-content {
  flex: 1;
}

.chart-card {
  background: var(--card-bg);
  border-radius: 20px;
  padding: 24px;
  border: 1px solid var(--border-color);
  height: 100%;
}

.chart-container {
  height: 250px;
  position: relative;
}

.table-container {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 20px;
  border: 1px solid var(--border-color);
}

.action-buttons {
  display: flex;
  gap: 5px;
}

.btn-action {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: 1px solid var(--border-color);
  background: var(--card-bg);
  color: var(--text-muted);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-action:hover {
  background: var(--accent-soft);
  color: var(--accent);
  border-color: var(--accent);
}

/* Responsive */
@media (max-width: 768px) {
  .chart-container {
    height: 200px;
  }
  
  .stat-card {
    padding: 20px;
  }
  
  .stat-icon {
    width: 40px;
    height: 40px;
    font-size: 18px;
  }
}
</style>