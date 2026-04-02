<template>
  <div class="trainer-reports-page">
    <!-- رأس الصفحة -->
    <div class="page-header mb-4" data-aos="fade-down">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <h2 class="fw-bold mb-2">{{ t('reports_analytics') }}</h2>
          <p class="text-muted mb-0">{{ t('generate_download_reports') }}</p>
        </div>
        <div class="d-flex gap-2">
          <button class="btn-refresh" @click="refreshData">
            <i class="bi bi-arrow-repeat me-2"></i>
            {{ t('refresh_data') }}
          </button>
          <button class="btn-generate" @click="openGenerateModal">
            <i class="bi bi-plus-lg me-2"></i>
            {{ t('generate_report') }}
          </button>
        </div>
      </div>
    </div>

    <!-- بطاقات الإحصائيات -->
    <div class="row g-4 mb-5">
      <div class="col-md-3" v-for="stat in reportStats" :key="stat.key" data-aos="fade-up">
        <div class="stat-card">
          <div class="d-flex justify-content-between align-items-start">
            <div class="stat-icon" :class="stat.iconClass">
              <i :class="stat.icon"></i>
            </div>
            <span class="trend" :class="stat.trendClass">
              <i :class="stat.trendIcon"></i> {{ stat.trend }}
            </span>
          </div>
          <div class="stat-content">
            <p class="text-muted mb-1">{{ t(stat.label) }}</p>
            <h3 class="fw-bold mb-0">{{ stat.value }}</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- شريط البحث والتصفية -->
    <div class="row g-3 mb-4" data-aos="fade-up">
      <div class="col-md-6">
        <div class="search-wrapper">
          <i class="bi bi-search"></i>
          <input 
            type="text" 
            class="form-control" 
            :placeholder="t('search_reports')"
            v-model="searchQuery"
          />
        </div>
      </div>
      <div class="col-md-3">
        <select class="form-select" v-model="dateFilter">
          <option value="month">{{ t('this_month') }}</option>
          <option value="last-month">{{ t('last_month') }}</option>
          <option value="quarter">{{ t('this_quarter') }}</option>
        </select>
      </div>
      <div class="col-md-3">
        <select class="form-select" v-model="programFilter">
          <option value="all">{{ t('all_programs') }}</option>
          <option value="web">{{ t('web_development') }}</option>
          <option value="data">{{ t('data_science') }}</option>
        </select>
      </div>
    </div>

    <!-- قائمة التقارير -->
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="reports-list" data-aos="fade-right">
          <h5 class="fw-bold mb-4">
            <i class="bi bi-file-text text-primary me-2"></i>
            {{ t('available_reports') }} ({{ filteredReports.length }})
          </h5>

          <div v-for="report in filteredReports" :key="report.id" class="report-item">
            <div class="d-flex gap-3 flex-wrap">
              <!-- أيقونة التقرير -->
              <div class="report-icon" :class="report.iconClass">
                <i :class="report.icon"></i>
              </div>

              <!-- محتوى التقرير -->
              <div class="report-content flex-grow-1">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                  <div>
                    <h6 class="fw-bold mb-1">
                      {{ t(report.title) }}
                      <span class="badge ms-2" :class="report.badgeClass">
                        {{ t(report.category) }}
                      </span>
                    </h6>
                    <p class="small text-muted mb-2">{{ t(report.description) }}</p>
                  </div>
                </div>

                <!-- معلومات الملف -->
                <div class="report-meta">
                  <span><i class="far fa-calendar-alt me-1"></i> {{ t('last_generated') }}: {{ report.lastGenerated }}</span>
                  <span><i :class="report.formatIcon"></i> {{ report.format }}</span>
                  <span><i class="fas fa-hdd me-1"></i> {{ report.size }}</span>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="report-actions">
                  <button class="btn-action" @click="downloadReport(report)">
                    <i class="bi bi-download me-1"></i> {{ t('download') }}
                  </button>
                  <button class="btn-action" @click="previewReport(report)">
                    <i class="bi bi-eye me-1"></i> {{ t('preview') }}
                  </button>
                  <button class="btn-action" @click="shareReport(report)">
                    <i class="bi bi-share me-1"></i> {{ t('share') }}
                  </button>
                  <button class="btn-action" @click="printReport(report)">
                    <i class="bi bi-printer me-1"></i> {{ t('print') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- العمود الجانبي -->
      <div class="col-lg-4">
        <!-- توزيع الأداء -->
        <div class="sidebar-card mb-4" data-aos="fade-left">
          <h6 class="fw-bold mb-4">
            <i class="far fa-clock text-primary me-2"></i>
            {{ t('performance_distribution') }}
          </h6>

          <div v-for="(item, index) in performanceData" :key="index" class="mb-3">
            <div class="d-flex justify-content-between small mb-1">
              <span>{{ t(item.label) }}</span>
              <span>{{ item.count }} {{ t('students') }}</span>
            </div>
            <div class="progress">
              <div 
                class="progress-bar" 
                :class="item.barClass"
                :style="{ width: item.percentage + '%' }"
              ></div>
            </div>
          </div>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="sidebar-card mb-4" data-aos="fade-left" data-aos-delay="100">
          <h6 class="fw-bold mb-3">
            <i class="fas fa-bolt text-primary me-2"></i>
            {{ t('quick_stats') }}
          </h6>
          <ul class="list-unstyled">
            <li v-for="stat in quickStats" :key="stat.key" class="stat-item">
              <span class="text-muted">
<i :class="[stat.icon, stat.iconClass, 'me-2']"></i>
                {{ t(stat.label) }}
              </span>
              <span class="fw-bold">{{ stat.value }}</span>
            </li>
          </ul>
        </div>

        <!-- خيارات التصدير -->
        <div class="sidebar-card" data-aos="fade-left" data-aos-delay="200">
          <h6 class="fw-bold mb-3">
            <i class="fas fa-file-export text-success me-2"></i>
            {{ t('export_options') }}
          </h6>
          <div class="d-grid gap-2">
            <button class="btn-export" @click="exportReport('pdf')">
              <i class="far fa-file-pdf text-danger me-2"></i>
              {{ t('export_pdf') }}
            </button>
            <button class="btn-export" @click="exportReport('excel')">
              <i class="far fa-file-excel text-success me-2"></i>
              {{ t('export_excel') }}
            </button>
            <button class="btn-export" @click="exportReport('csv')">
              <i class="fas fa-file-csv text-info me-2"></i>
              {{ t('export_csv') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- مودال إنشاء تقرير -->
    <Teleport to="body">
      <div v-if="showGenerateModal" class="modal-overlay" @click.self="closeGenerateModal">
        <div class="modal-content animate__animated animate__fadeInUp">
          <div class="modal-header">
            <h5 class="fw-bold mb-0">{{ t('generate_new_report') }}</h5>
            <button class="btn-close" @click="closeGenerateModal">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="generateReport">
              <div class="mb-3">
                <label class="form-label fw-bold">{{ t('report_type') }}</label>
                <select class="form-select" v-model="newReport.type">
                  <option value="performance">{{ t('performance_report') }}</option>
                  <option value="progress">{{ t('progress_report') }}</option>
                  <option value="completion">{{ t('completion_report') }}</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold">{{ t('date_range') }}</label>
                <select class="form-select" v-model="newReport.range">
                  <option value="week">{{ t('this_week') }}</option>
                  <option value="month">{{ t('this_month') }}</option>
                  <option value="quarter">{{ t('this_quarter') }}</option>
                  <option value="year">{{ t('this_year') }}</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold">{{ t('format') }}</label>
                <div class="d-flex gap-3">
                  <label class="format-option">
                    <input type="radio" value="pdf" v-model="newReport.format" />
                    <i class="far fa-file-pdf text-danger"></i>
                    PDF
                  </label>
                  <label class="format-option">
                    <input type="radio" value="excel" v-model="newReport.format" />
                    <i class="far fa-file-excel text-success"></i>
                    Excel
                  </label>
                  <label class="format-option">
                    <input type="radio" value="csv" v-model="newReport.format" />
                    <i class="fas fa-file-csv text-info"></i>
                    CSV
                  </label>
                </div>
              </div>

              <button type="submit" class="btn-generate-modal w-100" :disabled="isGenerating">
                <span v-if="!isGenerating">
                  <i class="bi bi-file-earmark-plus me-2"></i>
                  {{ t('generate_report') }}
                </span>
                <span v-else>
                  <span class="spinner-border spinner-border-sm me-2"></span>
                  {{ t('generating') }}
                </span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from '@/composables/useI18n'
import AOS from 'aos'

const { t } = useI18n()

// ========== إحصائيات التقارير ==========
const reportStats = ref([
  {
    key: 'students',
    icon: 'fas fa-users',
    iconClass: 'bg-primary text-white',
    label: 'total_students',
    value: '24',
    trend: '+8.5%',
    trendClass: 'trend-up',
    trendIcon: 'fas fa-arrow-up'
  },
  {
    key: 'grade',
    icon: 'fas fa-medal',
    iconClass: 'bg-warning text-white',
    label: 'average_grade',
    value: '87%',
    trend: '+3.2%',
    trendClass: 'trend-up',
    trendIcon: 'fas fa-arrow-up'
  },
  {
    key: 'completion',
    icon: 'fas fa-check-circle',
    iconClass: 'bg-success text-white',
    label: 'completion_rate',
    value: '92%',
    trend: '+5.1%',
    trendClass: 'trend-up',
    trendIcon: 'fas fa-arrow-up'
  },
  {
    key: 'tasks',
    icon: 'fas fa-bullseye',
    iconClass: 'bg-info text-white',
    label: 'active_tasks',
    value: '12',
    trend: '-2.3%',
    trendClass: 'trend-down',
    trendIcon: 'fas fa-arrow-down'
  }
])

// ========== بيانات التقارير ==========
const reports = ref([
  {
    id: 1,
    title: 'student_performance_report',
    description: 'performance_report_desc',
    category: 'student',
    badgeClass: 'bg-primary-subtle text-primary',
    icon: 'fas fa-user-graduate',
    iconClass: 'bg-light text-primary',
    lastGenerated: 'Dec 27, 2024',
    format: 'PDF',
    formatIcon: 'far fa-file-pdf text-danger',
    size: '2.4 MB'
  },
  {
    id: 2,
    title: 'program_progress_summary',
    description: 'progress_summary_desc',
    category: 'program',
    badgeClass: 'bg-warning-subtle text-warning',
    icon: 'fas fa-book',
    iconClass: 'bg-light text-warning',
    lastGenerated: 'Dec 20, 2024',
    format: 'Excel',
    formatIcon: 'far fa-file-excel text-success',
    size: '1.8 MB'
  }
])

// ========== بيانات الأداء ==========
const performanceData = ref([
  { label: 'excellent', count: 8, percentage: 33, barClass: 'bg-success' },
  { label: 'good', count: 10, percentage: 42, barClass: 'bg-primary' },
  { label: 'average', count: 4, percentage: 17, barClass: 'bg-warning' },
  { label: 'below_average', count: 2, percentage: 8, barClass: 'bg-danger' }
])

// ========== إحصائيات سريعة ==========
const quickStats = ref([
  { key: 'graded', icon: 'fas fa-check-double', iconClass: 'text-success', label: 'submissions_graded', value: '158' },
  { key: 'pending', icon: 'far fa-clock', iconClass: 'text-warning', label: 'pending_reviews', value: '8' },
  { key: 'response', icon: 'far fa-star', iconClass: 'text-primary', label: 'avg_response_time', value: '2.3 days' }
])

// ========== التصفية ==========
const searchQuery = ref('')
const dateFilter = ref('month')
const programFilter = ref('all')

const filteredReports = computed(() => {
  return reports.value.filter(report => 
    t(report.title).toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    t(report.description).toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

// ========== مودال إنشاء تقرير ==========
const showGenerateModal = ref(false)
const isGenerating = ref(false)

const newReport = ref({
  type: 'performance',
  range: 'month',
  format: 'pdf'
})

const openGenerateModal = () => {
  showGenerateModal.value = true
  document.body.style.overflow = 'hidden'
}

const closeGenerateModal = () => {
  showGenerateModal.value = false
  document.body.style.overflow = ''
}

const generateReport = async () => {
  isGenerating.value = true
  try {
    await new Promise(resolve => setTimeout(resolve, 2000))
    alert(t('report_generated'))
    closeGenerateModal()
  } finally {
    isGenerating.value = false
  }
}

// ========== دوال التفاعل ==========
const refreshData = () => {
  alert(t('data_refreshed'))
}

const downloadReport = (report) => {
  alert(t('downloading_report', { name: t(report.title) }))
}

const previewReport = (report) => {
  alert(t('previewing_report', { name: t(report.title) }))
}

const shareReport = (report) => {
  alert(t('sharing_report', { name: t(report.title) }))
}

const printReport = (report) => {
  alert(t('printing_report', { name: t(report.title) }))
}

const exportReport = (format) => {
  alert(t('exporting_as', { format: format.toUpperCase() }))
}

// تهيئة AOS
onMounted(() => {
  AOS.init({
    duration: 800,
    once: true
  })
})
</script>

<style scoped>
.trainer-reports-page {
  padding: 20px 0;
}

.btn-refresh,
.btn-generate {
  padding: 10px 20px;
  border-radius: 10px;
  font-weight: 500;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
}

.btn-refresh {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  color: var(--text-dark);
}

.btn-refresh:hover {
  background: var(--accent-soft);
  border-color: var(--accent);
  color: var(--accent);
}

.btn-generate {
  background: var(--accent);
  color: white;
}

.btn-generate:hover {
  background: #6d28d9;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(124, 58, 237, 0.3);
}

.stat-card {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 20px;
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--hover-shadow);
}

.stat-icon {
  width: 45px;
  height: 45px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}

.trend {
  font-size: 12px;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 6px;
}

.trend-up {
  background: rgba(34, 197, 94, 0.1);
  color: #22c55e;
}

.trend-down {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
}

.stat-content {
  margin-top: 15px;
}

.search-wrapper {
  position: relative;
}

.search-wrapper i {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
}

.search-wrapper input {
  padding-left: 45px;
  border-radius: 12px;
  border: 1px solid var(--border-color);
  background: var(--input-bg);
  color: var(--text-dark);
}

.form-select {
  border-radius: 12px;
  border: 1px solid var(--border-color);
  background: var(--input-bg);
  color: var(--text-dark);
  padding: 10px;
}

.report-item {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 20px;
  border: 1px solid var(--border-color);
  margin-bottom: 15px;
  transition: all 0.3s ease;
}

.report-item:hover {
  box-shadow: var(--hover-shadow);
}

.report-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.report-meta {
  display: flex;
  gap: 20px;
  font-size: 12px;
  color: var(--text-muted);
  margin: 10px 0;
  flex-wrap: wrap;
}

.report-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.btn-action {
  background: transparent;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 6px 12px;
  font-size: 12px;
  color: var(--text-muted);
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
}

.btn-action:hover {
  background: var(--accent-soft);
  border-color: var(--accent);
  color: var(--accent);
}

.sidebar-card {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 20px;
  border: 1px solid var(--border-color);
}

.stat-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid var(--border-color);
}

.stat-item:last-child {
  border-bottom: none;
}

.btn-export {
  background: transparent;
  border: 1px solid var(--border-color);
  border-radius: 10px;
  padding: 10px;
  text-align: left;
  color: var(--text-dark);
  transition: all 0.2s ease;
  cursor: pointer;
}

.btn-export:hover {
  background: var(--accent-soft);
  border-color: var(--accent);
}

/* مودال */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1100;
  padding: 20px;
}

.modal-content {
  background: var(--card-bg);
  border-radius: 24px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-body {
  padding: 24px;
}

.btn-close {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: 1px solid var(--border-color);
  background: transparent;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.format-option {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 8px 12px;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  cursor: pointer;
}

.format-option input {
  margin-right: 5px;
}

.btn-generate-modal {
  background: var(--accent);
  color: white;
  border: none;
  border-radius: 10px;
  padding: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-generate-modal:hover:not(:disabled) {
  background: #6d28d9;
  transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
  .report-actions {
    flex-wrap: wrap;
  }
  
  .btn-action {
    flex: 1;
  }
  
  .report-meta {
    gap: 10px;
  }
}

@media (max-width: 576px) {
  .d-flex.justify-content-between {
    flex-direction: column;
    align-items: flex-start !important;
  }
  
  .btn-refresh,
  .btn-generate {
    width: 100%;
  }
}
</style>