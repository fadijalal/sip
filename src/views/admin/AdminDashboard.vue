<template>
  <div class="admin-dashboard">
    <!-- رأس الصفحة -->
    <div class="dashboard-header mb-5" data-aos="fade-down">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <h2 class="fw-bold mb-2">{{ t('admin_dashboard') }}</h2>
          <p class="text-muted mb-0">{{ t('manage_text') }}</p>
        </div>
      </div>
    </div>

    <!-- بطاقات الإحصائيات الرئيسية -->
    <div class="row g-4 mb-5">
      <div class="col-12 col-sm-6 col-md-3" v-for="stat in mainStats" :key="stat.key">
        <StatCard
          :icon="stat.icon"
          :label="t(stat.label)"
          :value="stat.value"
          :trend="stat.trend"
          :trend-up="stat.trendUp"
          :variant="stat.variant"
          :background-icon="stat.bgIcon"
          @click="handleStatClick(stat)"
        />
      </div>
    </div>

    <!-- النشاط الأخير والشركات المعلقة -->
    <div class="row g-4 mb-5">
      <!-- النشاط الأخير -->
      <div class="col-lg-8">
        <div class="activity-card">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">{{ t('recent_activity') }}</h5>
            <button class="btn-view-all" @click="viewAllActivity">
              {{ t('view_all') }} <i class="bi bi-arrow-right ms-2"></i>
            </button>
          </div>

          <div class="activity-list">
            <div 
              v-for="activity in recentActivities" 
              :key="activity.id"
              class="activity-item"
            >
              <div class="d-flex align-items-center gap-3">
                <div class="activity-icon" :class="activity.iconClass">
                  <i :class="activity.icon"></i>
                </div>
                <div>
                  <h6 class="mb-1 fw-bold">{{ t(activity.title) }}</h6>
                  <small class="text-muted">{{ activity.description }}</small>
                </div>
              </div>
              <div class="activity-time">
                <span class="badge" :class="activity.badgeClass">
                  {{ t(activity.status) }}
                </span>
                <small class="text-muted">{{ activity.time }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- الشركات المعلقة -->
      <div class="col-lg-4">
        <div class="pending-companies-card">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">{{ t('pending_companies') }}</h5>
            <span class="pending-count">{{ pendingCompanies.length }}</span>
          </div>

          <div class="companies-list">
            <div 
              v-for="company in pendingCompanies" 
              :key="company.id"
              class="company-item"
            >
              <div class="d-flex align-items-center gap-3">
                <div class="company-icon">
                  <i class="bi bi-building"></i>
                </div>
                <div>
                  <h6 class="mb-1 fw-bold">{{ company.name }}</h6>
                  <small class="text-muted">{{ company.industry }}</small>
                </div>
              </div>
              <div class="company-actions">
                <button class="btn-approve" @click="approveCompany(company)">
                  <i class="bi bi-check-lg"></i>
                </button>
                <button class="btn-reject" @click="rejectCompany(company)">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
            </div>
          </div>

          <button class="btn-view-all-companies mt-4" @click="viewAllCompanies">
            {{ t('view_all_companies') }} <i class="bi bi-arrow-right ms-2"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- إحصائيات البرامج والرسوم البيانية -->
    <div class="row g-4">
      <div class="col-md-6">
        <div class="chart-card">
          <h6 class="fw-bold mb-4">
            <i class="bi bi-bar-chart-fill text-primary me-2"></i>
            {{ t('user_registrations') }}
          </h6>
          <div class="chart-container">
            <!-- هنا سيتم وضع الرسم البياني باستخدام Chart.js -->
            <canvas id="usersChart"></canvas>
          </div>
        </div>
      </div>

      <div class="col-md-6">
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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from '@/composables/useI18n'
import StatCard from '@/components/common/StatCard.vue'
import Chart from 'chart.js/auto'
import AOS from 'aos'

const { t } = useI18n()
const router = useRouter()

// ========== الإحصائيات الرئيسية ==========
const mainStats = ref([
  {
    key: 'total_users',
    icon: 'bi bi-people-fill',
    label: 'total_users',
    value: '248',
    trend: '+12 ' + t('this_week'),
    trendUp: true,
    variant: 'primary',
    bgIcon: 'bi bi-people'
  },
  {
    key: 'total_companies',
    icon: 'bi bi-building',
    label: 'total_companies',
    value: '32',
    trend: '+3 ' + t('pending'),
    trendUp: true,
    variant: 'success',
    bgIcon: 'bi bi-building'
  },
  {
    key: 'active_programs',
    icon: 'bi bi-list-check',
    label: 'active_programs',
    value: '64',
    trend: '+8 ' + t('this_month'),
    trendUp: true,
    variant: 'warning',
    bgIcon: 'bi bi-list-check'
  },
  {
    key: 'pending_approvals',
    icon: 'bi bi-clock-history',
    label: 'pending_approvals',
    value: '12',
    trend: t('needs_review'),
    variant: 'danger',
    bgIcon: 'bi bi-clock'
  }
])

// ========== النشاط الأخير ==========
const recentActivities = ref([
  {
    id: 1,
    title: 'new_supervisor',
    description: 'Dr. Sarah Chen • 12/28/2024',
    icon: 'bi bi-person-plus-fill',
    iconClass: 'bg-purple-light',
    status: 'completed',
    badgeClass: 'badge-completed',
    time: '2 hours ago'
  },
  {
    id: 2,
    title: 'company_pending',
    description: 'TechStart Solutions • 12/28/2024',
    icon: 'bi bi-building',
    iconClass: 'bg-green-light',
    status: 'pending',
    badgeClass: 'badge-pending',
    time: '5 hours ago'
  }
])

// ========== الشركات المعلقة ==========
const pendingCompanies = ref([
  {
    id: 1,
    name: 'TechCorp Inc.',
    industry: t('technology'),
    email: 'contact@techcorp.com'
  },
  {
    id: 2,
    name: 'DataScience Pro',
    industry: t('data_analytics'),
    email: 'info@datascipro.com'
  },
  {
    id: 3,
    name: 'CloudScale Solutions',
    industry: t('cloud_computing'),
    email: 'hello@cloudscale.io'
  }
])

// ========== دوال التفاعل ==========
const handleStatClick = (stat) => {
  const routes = {
    total_users: '/admin/users',
    total_companies: '/admin/companies',
    active_programs: '/admin/programs',
    pending_approvals: '/admin/approvals'
  }
  if (routes[stat.key]) {
    router.push(routes[stat.key])
  }
}

const viewAllActivity = () => {
  router.push('/admin/activity')
}

const viewAllCompanies = () => {
  router.push('/admin/companies')
}

const approveCompany = (company) => {
  if (confirm(t('confirm_approve_company'))) {
    pendingCompanies.value = pendingCompanies.value.filter(c => c.id !== company.id)
    // إظهار رسالة نجاح
  }
}

const rejectCompany = (company) => {
  if (confirm(t('confirm_reject_company'))) {
    pendingCompanies.value = pendingCompanies.value.filter(c => c.id !== company.id)
  }
}

// ========== إنشاء الرسوم البيانية ==========
const initCharts = () => {
  // رسم بياني للمستخدمين
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
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { display: false },
          x: {
            grid: { display: false },
            ticks: { color: '#9ca3af' }
          }
        }
      }
    })
  }

  // رسم بياني للبرامج
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
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { display: false },
          x: {
            grid: { display: false },
            ticks: { color: '#9ca3af' }
          }
        }
      }
    })
  }
}

// تهيئة
onMounted(() => {
  AOS.init({
    duration: 800,
    once: true,
    easing: 'ease-in-out-sine'
  })
  
  initCharts()
})
</script>

<style scoped>
.admin-dashboard {
  padding: 20px 0;
}

.activity-card,
.pending-companies-card,
.chart-card {
  background: var(--card-bg);
  border-radius: 24px;
  padding: 24px;
  border: 1px solid var(--border-color);
  box-shadow: var(--card-shadow);
  height: 100%;
}

.btn-view-all {
  background: transparent;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 500;
  color: var(--text-muted);
  transition: all 0.3s ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
}

.btn-view-all:hover {
  background: var(--accent-soft);
  color: var(--accent);
  border-color: var(--accent);
}

.activity-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  border: 1px solid var(--border-color);
  border-radius: 16px;
  margin-bottom: 12px;
  transition: all 0.3s ease;
}

.activity-item:hover {
  background: var(--accent-soft);
}

.activity-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

.bg-purple-light {
  background: #eef2ff;
  color: #4f46e5;
}

.bg-green-light {
  background: #f0fdf4;
  color: #22c55e;
}

.activity-time {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 5px;
}

.badge-completed {
  background: #f0fdf4;
  color: #15803d;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
}

.badge-pending {
  background: #fffbeb;
  color: #b45309;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
}

.pending-count {
  background: #ef4444;
  color: white;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 600;
}

.companies-list {
  max-height: 300px;
  overflow-y: auto;
}

.company-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  border-bottom: 1px solid var(--border-color);
}

.company-item:last-child {
  border-bottom: none;
}

.company-icon {
  width: 38px;
  height: 38px;
  background: var(--accent-soft);
  color: var(--accent);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.company-actions {
  display: flex;
  gap: 8px;
}

.btn-approve,
.btn-reject {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-approve {
  background: #22c55e;
  color: white;
}

.btn-approve:hover {
  background: #16a34a;
}

.btn-reject {
  background: #ef4444;
  color: white;
}

.btn-reject:hover {
  background: #dc2626;
}

.btn-view-all-companies {
  width: 100%;
  padding: 12px;
  border: 1px dashed var(--border-color);
  border-radius: 12px;
  background: transparent;
  color: var(--text-muted);
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-view-all-companies:hover {
  border-color: var(--accent);
  color: var(--accent);
}

.chart-container {
  height: 250px;
  position: relative;
}

/* Responsive */
@media (max-width: 768px) {
  .activity-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  
  .activity-time {
    align-items: flex-start;
    width: 100%;
  }
}

@media (max-width: 576px) {
  .chart-container {
    height: 200px;
  }
}
</style>