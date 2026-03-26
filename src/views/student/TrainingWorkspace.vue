<template>
  <div class="training-workspace">
    <!-- Header -->
    <header class="workspace-header">
      <div class="container-fluid px-3 px-md-4 px-lg-5">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div class="d-flex align-items-center gap-3">
            <div class="header-icon">
              <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-0">{{ t('training_workspace') }}</h6>
              <small class="text-muted">{{ currentProgram }}</small>
            </div>
          </div>

          <div class="d-flex gap-2 align-items-center">
            <!-- Theme Toggle -->
            <button class="theme-toggle" @click="toggleTheme">
              <i class="bi" :class="isDark ? 'bi-sun-fill' : 'bi-moon-stars-fill'"></i>
            </button>

            <button class="btn btn-light border btn-sm px-4 rounded-pill">
              <i class="bi bi-camera-video me-2"></i>
              {{ t('join_live') }}
            </button>

            <button class="btn btn-primary btn-sm px-4 rounded-pill">
              {{ t('contact_trainer') }}
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <div class="container-fluid px-3 px-md-4 px-lg-5 py-4">
      <!-- Stats Cards -->
      <div class="row g-3 g-md-4 mb-5">
        <div class="col-12 col-sm-6 col-md-6 col-lg-3" v-for="stat in stats" :key="stat.key">
          <div class="stat-card" :style="{ background: stat.color }">
            <div class="stat-value fs-3 fw-bold">{{ stat.value }}</div>
            <div class="stat-label small opacity-75">{{ t(stat.label) }}</div>
            <i :class="stat.icon" class="stat-bg-icon"></i>
          </div>
        </div>
      </div>

      <!-- Weekly Tasks Section -->
      <div class="row g-3 g-md-4">
        <!-- Main Tasks Column -->
        <div class="col-12 col-lg-8">
          <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-2 mb-sm-0">{{ t('weekly_tasks') }}</h5>
            <select class="form-select w-auto border-0 shadow-sm rounded-3 small">
              <option v-for="week in 12" :key="week" :selected="week === 3">
                {{ t('week') }} {{ week }}
              </option>
            </select>
          </div>

          <!-- Task Cards -->
          <div v-for="task in weeklyTasks" :key="task.id" class="task-card mb-4">
            <!-- Task Header -->
            <div class="d-flex gap-2 mb-3 flex-wrap">
              <span class="badge-custom" :style="{ background: task.typeBg, color: task.typeColor }">
                {{ t(task.type) }}
              </span>
              <span v-if="task.status === 'completed'" class="badge-custom" style="background: #f0fdf4; color: #22c55e">
                <i class="bi bi-check-circle me-1"></i>
                {{ t('completed') }}
              </span>
            </div>

            <!-- Task Content -->
            <h5 class="fw-bold">{{ t(task.title) }}</h5>
            <p class="text-muted small">{{ t(task.description) }}</p>

            <!-- Grade Section (if completed) -->
            <div v-if="task.grade" class="grade-section">
              <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                <span class="fw-bold text-success">{{ t('grade') }}</span>
                <span class="fw-bold fs-4 text-success">{{ task.grade }}</span>
              </div>
              <p class="small text-muted mb-0">
                <strong>{{ t('feedback') }}</strong> {{ task.feedback }}
              </p>
            </div>

            <!-- Submit Button (if pending) -->
            <button
              v-if="task.status === 'pending'"
              class="btn-submit mt-3"
              @click="submitTask(task)"
            >
              {{ t('submit_assignment') }}
            </button>
          </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-12 col-lg-4">
          <!-- Quick Actions -->
          <div class="content-card">
            <h6 class="fw-bold mb-4">{{ t('quick_actions') }}</h6>
            <a v-for="action in quickActions" :key="action.label" href="#" class="quick-item">
              <i :class="action.icon"></i>
              <span>{{ t(action.label) }}</span>
            </a>
          </div>

          <!-- Progress Analytics -->
          <div class="content-card">
            <h6 class="fw-bold mb-4">{{ t('progress_analytics') }}</h6>
            <div v-for="week in progressData" :key="week.week" class="mb-3">
              <div class="d-flex justify-content-between small mb-1">
                <span>{{ t('week') }} {{ week.week }}</span>
                <span class="fw-bold">{{ week.percentage }}%</span>
              </div>
              <div class="progress-custom">
                <div
                  class="progress-fill"
                  :class="week.barClass"
                  :style="{ width: week.percentage + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from '@/composables/useI18n'
import { useTheme } from '@/composables/useTheme'

const { t } = useI18n()
const { isDark, toggleTheme } = useTheme()
const router = useRouter()

// Current program name
const currentProgram = ref('Advanced Web Development & Cloud Architecture')

// Stats cards
const stats = ref([
  { key: 'progress', value: '25%', label: 'overall_progress', icon: 'bi bi-bullseye', color: '#7c3aed' },
  { key: 'tasks', value: '15/60', label: 'tasks_completed', icon: 'bi bi-check-all', color: '#6366f1' },
  { key: 'week', value: 'Week 3', label: 'of_12_weeks', icon: 'bi bi-calendar3', color: '#3b82f6' },
  { key: 'deadlines', value: '3', label: 'upcoming_deadlines', icon: 'bi bi-alarm', color: '#f59e0b' }
])

// Weekly tasks
const weeklyTasks = ref([
  {
    id: 1,
    type: 'assignment',
    typeBg: '#eff6ff',
    typeColor: '#3b82f6',
    title: 'react_hooks_assignment',
    description: 'react_hooks_description',
    status: 'completed',
    grade: '95%',
    feedback: 'Excellent work! Your implementation shows deep understanding.'
  },
  {
    id: 2,
    type: 'quiz',
    typeBg: '#f5f3ff',
    typeColor: '#7c3aed',
    title: 'redux_quiz',
    description: 'redux_quiz_description',
    status: 'pending'
  }
])

// Quick actions
const quickActions = ref([
  { icon: 'bi bi-book', label: 'course_materials' },
  { icon: 'bi bi-camera-reels', label: 'recorded_sessions' },
  { icon: 'bi bi-chat-dots', label: 'discussion_forum' },
  { icon: 'bi bi-journal-text', label: 'view_grades' }
])

// Progress data
const progressData = ref([
  { week: 1, percentage: 100, barClass: 'bg-success' },
  { week: 2, percentage: 50, barClass: 'bg-warning' },
  { week: 3, percentage: 0, barClass: 'bg-secondary' }
])

// Methods
const submitTask = (task) => {
  router.push(`/student/submit-task/${task.id}`)
}
</script>

<style scoped>
.training-workspace {
  min-height: 100vh;
  background: var(--main-bg);
}

.workspace-header {
  background: var(--header-bg);
  border-bottom: 1px solid var(--border-color);
  padding: 15px 0;
  position: sticky;
  top: 0;
  z-index: 100;
  backdrop-filter: blur(10px);
}

.header-icon {
  width: 45px;
  height: 45px;
  background: var(--accent);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 20px;
}

.theme-toggle {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  border: 1px solid var(--border-color);
  background: var(--card-bg);
  color: var(--text-dark);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.theme-toggle:hover {
  background: var(--accent-soft);
  color: var(--accent);
  border-color: var(--accent);
}

.stat-card {
  border-radius: 20px;
  padding: 24px;
  color: white;
  position: relative;
  overflow: hidden;
  border: none;
  height: 100%;
}

.stat-bg-icon {
  position: absolute;
  right: 15px;
  bottom: 10px;
  font-size: 2.5rem;
  opacity: 0.2;
}

.form-select {
  background: var(--card-bg);
  color: var(--text-dark);
  border-color: var(--border-color);
  padding: 8px 30px 8px 15px;
}

.task-card {
  background: var(--card-bg);
  border-radius: 24px;
  border: 1px solid var(--border-color);
  padding: 28px;
  transition: all 0.3s ease;
}

.task-card:hover {
  box-shadow: var(--hover-shadow);
}

.badge-custom {
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 600;
}

.grade-section {
  background: rgba(34, 197, 94, 0.1);
  border: 1px solid #22c55e;
  border-radius: 20px;
  padding: 20px;
  margin-top: 15px;
}

.btn-submit {
  background: var(--accent);
  color: white;
  border: none;
  border-radius: 12px;
  padding: 12px;
  width: 100%;
  font-weight: 600;
  transition: all 0.3s ease;
  cursor: pointer;
}

.btn-submit:hover {
  background: #6d28d9;
  transform: translateY(-2px);
}

.content-card {
  background: var(--card-bg);
  border-radius: 24px;
  border: 1px solid var(--border-color);
  padding: 28px;
  margin-bottom: 24px;
}

.quick-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border: 1px solid var(--border-color);
  border-radius: 12px;
  margin-bottom: 10px;
  text-decoration: none;
  color: var(--text-muted);
  transition: all 0.2s ease;
}

.quick-item:hover {
  background: var(--accent-soft);
  color: var(--accent);
  border-color: var(--accent);
}

.progress-custom {
  height: 8px;
  background: var(--border-color);
  border-radius: 10px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 10px;
  transition: width 0.5s ease;
}

/* Responsive */
@media (max-width: 768px) {
  .task-card {
    padding: 20px;
  }

  .content-card {
    padding: 20px;
  }

  .grade-section .d-flex {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
}

@media (max-width: 576px) {
  .stat-card {
    padding: 18px;
  }

  .stat-card .fs-3 {
    font-size: 1.5rem !important;
  }

  .quick-item {
    padding: 10px 12px;
  }
}
</style>
