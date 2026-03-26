<template>
  <div class="weekly-tasks-monitoring">
    <!-- رأس الصفحة -->
    <div class="dashboard-header mb-5" data-aos="fade-down">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <h2 class="fw-bold mb-2">{{ t('weekly_tasks_monitoring') }}</h2>
          <p class="text-muted mb-0">{{ t('track_monitor_tasks') }}</p>
        </div>
      </div>
    </div>

    <!-- بطاقات الإحصائيات -->
    <div class="row g-3 g-sm-4 mb-5">
      <div class="col-6 col-md" v-for="stat in taskStats" :key="stat.key">
        <div class="stat-mini-card" :class="stat.bgClass">
          <div class="stat-mini-content">
            <div class="stat-mini-label">{{ t(stat.label) }}</div>
            <div class="stat-mini-value">{{ stat.value }}</div>
          </div>
          <i class="stat-mini-icon" :class="stat.icon"></i>
        </div>
      </div>
    </div>

    <!-- شريط البحث والتصفية -->
    <div class="filter-bar mb-4">
      <div class="filter-search-wrapper">
        <i class="bi bi-search"></i>
        <input 
          type="text" 
          class="search-input" 
          :placeholder="t('search_tasks')"
          v-model="searchQuery"
          @input="filterTasks"
        />
      </div>
      
      <select class="form-select" v-model="weekFilter">
        <option value="all">{{ t('all_weeks') }}</option>
        <option v-for="week in 12" :key="week" :value="`week-${week}`">
          {{ t('week') }} {{ week }}
        </option>
      </select>
      
      <select class="form-select" v-model="statusFilter">
        <option value="all">{{ t('all_status') }}</option>
        <option value="completed">{{ t('completed') }}</option>
        <option value="in-progress">{{ t('in_progress') }}</option>
        <option value="pending">{{ t('pending') }}</option>
        <option value="late">{{ t('late') }}</option>
      </select>
    </div>

    <!-- قائمة المهام -->
    <div class="tasks-list">
      <div 
        v-for="task in filteredTasks" 
        :key="task.id"
        class="task-item"
        :class="task.bgClass"
        data-aos="fade-up"
      >
        <div class="row align-items-center g-3">
          <!-- معلومات الطالب -->
          <div class="col-lg-4 col-md-12">
            <div class="d-flex align-items-center gap-3">
              <div class="avatar-box" :style="{ background: task.avatarColor }">
                {{ task.initials }}
              </div>
              <div>
                <h6 class="mb-0 fw-bold">{{ task.studentName }}</h6>
                <small class="text-muted">{{ t(task.program) }}</small>
              </div>
            </div>
          </div>

          <!-- معلومات المهمة -->
          <div class="col-lg-4 col-md-12">
            <h6 class="mb-1 fw-bold">{{ t(task.title) }}</h6>
            <small class="text-muted">
              <i class="bi bi-calendar3 me-1"></i>
              {{ t('week') }} {{ task.week }} • {{ t('due') }}: {{ formatDate(task.dueDate) }}
            </small>
          </div>

          <!-- شريط التقدم -->
          <div class="col-lg-4 col-md-12">
            <div class="d-flex justify-content-between mb-1 small fw-medium">
              <span>{{ t('hours_logged') }}</span>
              <span :class="getHoursClass(task)">
                {{ task.hoursLogged }}/{{ task.hoursTotal }}h
              </span>
            </div>
            <div class="progress-custom">
              <div 
                class="progress-fill"
                :class="getProgressClass(task)"
                :style="{ width: task.progress + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from '@/composables/useI18n'
import AOS from 'aos'

const { t, formatDate } = useI18n()

// ========== إحصائيات المهام ==========
const taskStats = ref([
  { key: 'total', label: 'total_tasks', value: '8', bgClass: 'bg-soft-blue', icon: 'bi bi-list-task' },
  { key: 'completed', label: 'completed', value: '3', bgClass: 'bg-soft-green', icon: 'bi bi-check-circle' },
  { key: 'in-progress', label: 'in_progress', value: '2', bgClass: 'bg-soft-cyan', icon: 'bi bi-arrow-repeat' },
  { key: 'pending', label: 'pending', value: '1', bgClass: 'bg-soft-white', icon: 'bi bi-clock' },
  { key: 'late', label: 'late', value: '2', bgClass: 'bg-soft-red', icon: 'bi bi-exclamation-triangle' }
])

// ========== بيانات المهام ==========
const tasks = ref([
  {
    id: 1,
    studentName: 'Emma Thompson',
    initials: 'ET',
    program: 'software_engineering',
    title: 'database_project',
    week: 13,
    dueDate: '2025-01-06',
    hoursLogged: 8,
    hoursTotal: 15,
    progress: 53,
    status: 'in-progress',
    bgClass: 'blue-task',
    avatarColor: '#4f46e5'
  },
  {
    id: 2,
    studentName: 'Michael Chen',
    initials: 'MC',
    program: 'data_science',
    title: 'ml_model_training',
    week: 13,
    dueDate: '2025-01-06',
    hoursLogged: 18,
    hoursTotal: 15,
    progress: 100,
    status: 'completed',
    bgClass: 'green-task',
    avatarColor: '#22c55e'
  },
  {
    id: 3,
    studentName: 'Sarah Johnson',
    initials: 'SJ',
    program: 'program_ux',
    title: 'UI_redesign',
    week: 12,
    dueDate: '2025-01-10',
    hoursLogged: 5,
    hoursTotal: 20,
    progress: 25,
    status: 'pending',
    bgClass: 'blue-task',
    avatarColor: '#f59e0b'
  }
])

// ========== التصفية ==========
const searchQuery = ref('')
const weekFilter = ref('all')
const statusFilter = ref('all')

const filteredTasks = computed(() => {
  return tasks.value.filter(task => {
    // فلتر البحث
    const matchesSearch = task.studentName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         t(task.title).toLowerCase().includes(searchQuery.value.toLowerCase())
    
    // فلتر الأسبوع
    const matchesWeek = weekFilter.value === 'all' || 
                       `week-${task.week}` === weekFilter.value
    
    // فلتر الحالة
    const matchesStatus = statusFilter.value === 'all' || 
                         task.status === statusFilter.value
    
    return matchesSearch && matchesWeek && matchesStatus
  })
})

const filterTasks = () => {
  // الفلتر يتم تلقائياً عبر computed
}

// ========== دوال مساعدة ==========
const getHoursClass = (task) => {
  if (task.hoursLogged >= task.hoursTotal) return 'text-success fw-bold'
  if (task.hoursLogged >= task.hoursTotal * 0.7) return 'text-warning fw-bold'
  return 'text-muted'
}

const getProgressClass = (task) => {
  if (task.status === 'completed') return 'progress-success'
  if (task.status === 'late') return 'progress-danger'
  if (task.progress >= 70) return 'progress-primary'
  return 'progress-warning'
}

// تهيئة AOS
onMounted(() => {
  AOS.init({
    duration: 800,
    once: true,
    easing: 'ease-in-out-sine'
  })
})
</script>

<style scoped>
.weekly-tasks-monitoring {
  padding: 20px 0;
}

.stat-mini-card {
  padding: 20px 16px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
  overflow: hidden;
  height: 100%;
}

.stat-mini-label {
  font-size: 12px;
  font-weight: 600;
  color: var(--text-muted);
  margin-bottom: 5px;
}

.stat-mini-value {
  font-size: 24px;
  font-weight: 700;
  color: var(--text-dark);
  line-height: 1;
}

.stat-mini-icon {
  font-size: 32px;
  opacity: 0.2;
  color: currentColor;
}

.bg-soft-blue {
  background: #eff6ff;
  color: #3b82f6;
}

.bg-soft-green {
  background: #f0fdf4;
  color: #22c55e;
}

.bg-soft-cyan {
  background: #ecfeff;
  color: #06b6d4;
}

.bg-soft-white {
  background: #ffffff;
  border: 1px solid #f1f5f9;
  color: #64748b;
}

.bg-soft-red {
  background: #fef2f2;
  color: #ef4444;
}

.filter-bar {
  background: var(--filter-bg);
  border-radius: 18px;
  padding: 10px 20px;
  box-shadow: var(--card-shadow);
  display: flex;
  gap: 15px;
  align-items: center;
  margin-bottom: 30px;
  border: 1px solid var(--border-color);
  flex-wrap: wrap;
}

.filter-search-wrapper {
  flex: 2 1 250px;
  display: flex;
  align-items: center;
  gap: 10px;
  border-right: 1px solid var(--border-color);
  padding-right: 15px;
}

.filter-search-wrapper i {
  color: var(--text-muted);
}

.search-input {
  border: none;
  background: transparent;
  width: 100%;
  outline: none;
  font-size: 14px;
  color: var(--text-dark);
}

.form-select {
  min-width: 140px;
  flex: 1 1 auto;
  border: 1px solid var(--border-color);
  border-radius: 10px;
  background: var(--card-bg);
  color: var(--text-dark);
  padding: 8px 12px;
}

.task-item {
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 15px;
  transition: all 0.3s ease;
  border: 1px solid transparent;
}

.task-item:hover {
  transform: translateY(-3px);
  box-shadow: var(--hover-shadow);
}

.blue-task {
  background: #eff6ff;
  border-color: #dbeafe;
}

.green-task {
  background: #f0fdf4;
  border-color: #dcfce7;
}

.avatar-box {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  color: white;
  flex-shrink: 0;
}

.progress-custom {
  height: 8px;
  background: rgba(0, 0, 0, 0.05);
  border-radius: 10px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 10px;
  transition: width 1s ease;
}

.progress-primary {
  background: #4f46e5;
}

.progress-success {
  background: #22c55e;
}

.progress-warning {
  background: #f59e0b;
}

.progress-danger {
  background: #ef4444;
}

/* الوضع الليلي */
[data-theme="dark"] .blue-task,
[data-theme="dark"] .green-task {
  background: #1e293b;
  border-color: #334155;
}

[data-theme="dark"] .bg-soft-white {
  background: #1e293b;
  border-color: #334155;
}

/* Responsive */
@media (max-width: 768px) {
  .filter-search-wrapper {
    width: 100%;
    border-right: none;
    border-bottom: 1px solid var(--border-color);
    padding-right: 0;
    padding-bottom: 10px;
    margin-bottom: 5px;
  }
  
  .form-select {
    width: calc(50% - 5px);
    min-width: unset;
  }
}

@media (max-width: 576px) {
  .stat-mini-card {
    padding: 15px 12px;
  }
  
  .stat-mini-value {
    font-size: 20px;
  }
  
  .stat-mini-icon {
    font-size: 24px;
  }
  
  .task-item {
    padding: 18px;
  }
  
  .avatar-box {
    width: 40px;
    height: 40px;
    font-size: 14px;
  }
}
</style>