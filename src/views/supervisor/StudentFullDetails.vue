<template>
  <div class="student-details-page">
    <!-- رابط العودة -->
    <div class="mb-4">
      <router-link to="/supervisor/students" class="back-link">
        <i class="bi bi-arrow-left me-2"></i>
        {{ t('back_to_students') }}
      </router-link>
    </div>

    <!-- بطاقة الملف الشخصي الرئيسية -->
    <div class="profile-card mb-4" data-aos="fade-down">
      <div class="d-flex flex-wrap gap-4 align-items-start justify-content-between">
        <div class="d-flex gap-4 flex-wrap">
          <!-- الصورة الشخصية -->
          <div class="avatar-box" :style="{ background: student.avatarColor }">
            {{ student.initials }}
          </div>

          <!-- المعلومات الأساسية -->
          <div>
            <div class="d-flex align-items-center gap-3 mb-2 flex-wrap">
              <h3 class="fw-bold mb-0">{{ student.name }}</h3>
              <span class="status-badge" :class="statusClass">
                <i :class="statusIcon"></i>
                {{ t(student.status) }}
              </span>
            </div>

            <!-- شبكة المعلومات -->
            <div class="info-grid">
              <div class="info-item">
                <i class="bi bi-envelope text-primary"></i>
                <span>{{ student.email }}</span>
              </div>
              <div class="info-item">
                <i class="bi bi-telephone text-primary"></i>
                <span>{{ student.phone }}</span>
              </div>
              <div class="info-item">
                <i class="bi bi-building text-primary"></i>
                <span>{{ student.company }}</span>
              </div>
              <div class="info-item">
                <i class="bi bi-mortarboard text-primary"></i>
                <span>{{ t(student.program) }}</span>
              </div>
              <div class="info-item">
                <i class="bi bi-geo-alt text-primary"></i>
                <span>{{ student.location }}</span>
              </div>
              <div class="info-item">
                <i class="bi bi-person-badge text-primary"></i>
                <span>{{ t('supervisor') }}: Dr. Sarah Chen</span>
              </div>
            </div>
          </div>
        </div>

        <!-- أزرار الإجراءات -->
        <div class="d-flex gap-2 flex-wrap">
          <button class="btn-outline-custom" @click="addNote">
            <i class="bi bi-chat-left-text me-2"></i>
            {{ t('add_note') }}
          </button>
          <button class="btn-custom-purple" @click="downloadReport">
            <i class="bi bi-download me-2"></i>
            {{ t('download_report') }}
          </button>
        </div>
      </div>
    </div>

    <!-- تقدم التدريب والملاحظات -->
    <div class="row g-4 mb-4">
      <!-- تقدم التدريب -->
      <div class="col-lg-7">
        <div class="details-card">
          <h6 class="card-title">
            <i class="bi bi-bar-chart-line text-primary me-2"></i>
            {{ t('training_progress') }}
          </h6>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="progress-container">
                <div class="d-flex justify-content-between small fw-bold mb-2">
                  <span>{{ t('training_hours') }}</span>
                  <span>{{ student.hoursCompleted }}/{{ student.hoursTotal }}h</span>
                </div>
                <div class="progress-custom">
                  <div 
                    class="progress-fill bg-primary"
                    :style="{ width: hoursPercentage + '%' }"
                  ></div>
                </div>
                <small class="text-muted mt-2 d-block">
                  {{ hoursPercentage }}% {{ t('complete') }}
                </small>
              </div>
            </div>

            <div class="col-md-6">
              <div class="progress-container green">
                <div class="d-flex justify-content-between small fw-bold mb-2">
                  <span>{{ t('tasks_completed') }}</span>
                  <span>{{ student.tasksCompleted }}/{{ student.tasksTotal }}</span>
                </div>
                <div class="progress-custom">
                  <div 
                    class="progress-fill bg-success"
                    :style="{ width: tasksPercentage + '%' }"
                  ></div>
                </div>
                <small class="text-muted mt-2 d-block">
                  {{ tasksPercentage }}% {{ t('complete') }}
                </small>
              </div>
            </div>
          </div>

          <div class="mt-4 pt-3 border-top d-flex gap-5 text-muted small flex-wrap">
            <span>
              <i class="bi bi-calendar me-2"></i>
              {{ t('enrolled') }}: {{ formatDate(student.enrolledDate) }}
            </span>
            <span>
              <i class="bi bi-calendar-check me-2"></i>
              {{ t('expected_end') }}: {{ formatDate(student.expectedEndDate) }}
            </span>
          </div>
        </div>
      </div>

      <!-- ملاحظات المشرف -->
      <div class="col-lg-5">
        <div class="details-card">
          <h6 class="card-title">
            <i class="bi bi-journal-text text-primary me-2"></i>
            {{ t('supervisor_notes') }}
          </h6>

          <div class="notes-list">
            <div v-for="note in notes" :key="note.id" class="note-bubble">
              <div class="d-flex justify-content-between mb-2">
                <span class="fw-bold small">{{ note.author }}</span>
                <span class="text-muted small">{{ formatDate(note.date) }}</span>
              </div>
              <p class="small mb-0">{{ note.content }}</p>
            </div>
          </div>

          <button class="btn w-100 border py-2 rounded-3 small fw-bold text-muted mt-3" @click="addNote">
            <i class="bi bi-plus-lg me-2"></i>
            {{ t('add_new_note') }}
          </button>
        </div>
      </div>
    </div>

    <!-- المهام الأسبوعية والتقارير -->
    <div class="row g-4">
      <!-- المهام الأسبوعية -->
      <div class="col-lg-8">
        <div class="details-card">
          <h6 class="card-title text-success">
            <i class="bi bi-check2-circle me-2"></i>
            {{ t('weekly_tasks_overview') }}
          </h6>

          <div v-for="task in weeklyTasks" :key="task.id" class="task-item">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
              <div>
                <span class="fw-bold d-block">{{ t(task.title) }}</span>
                <small class="text-muted">{{ t('week') }} {{ task.week }}</small>
              </div>
              <span class="badge" :class="task.gradeClass">{{ task.grade }}</span>
            </div>
            <div v-if="task.feedback" class="mt-2 feedback-box">
              <i class="bi bi-quote me-1"></i>
              {{ task.feedback }}
            </div>
          </div>
        </div>
      </div>

      <!-- مقاييس الأداء -->
      <div class="col-lg-4">
        <div class="details-card bg-soft-blue">
          <h6 class="card-title">{{ t('performance_metrics') }}</h6>

          <div class="metric-row">
            <div class="metric-label">
              <span>{{ t('avg_task_score') }}</span>
              <span class="fw-bold">{{ metrics.avgScore }}</span>
            </div>
            <div class="metric-bar bg-success" :style="{ width: metrics.avgScorePercent + '%' }"></div>
          </div>

          <div class="metric-row">
            <div class="metric-label">
              <span>{{ t('report_quality') }}</span>
              <span class="fw-bold">{{ metrics.reportQuality }}/100</span>
            </div>
            <div class="metric-bar bg-primary" :style="{ width: metrics.reportQuality + '%' }"></div>
          </div>

          <div class="metric-row">
            <div class="metric-label">
              <span>{{ t('attendance') }}</span>
              <span class="fw-bold">{{ metrics.attendance }}%</span>
            </div>
            <div class="metric-bar bg-danger" :style="{ width: metrics.attendance + '%' }"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from '@/composables/useI18n'
import AOS from 'aos'

const { t, formatDate } = useI18n()
const route = useRoute()
const router = useRouter()

// ========== بيانات الطالب ==========
const student = ref({
  id: route.params.id || 1,
  name: 'Emma Thompson',
  email: 'emma.thompson@email.com',
  initials: 'ET',
  phone: '+1 (555) 123-4567',
  company: 'TechCorp Inc.',
  program: 'program_software',
  status: 'on-track',
  location: 'San Francisco, CA',
  hoursCompleted: 180,
  hoursTotal: 240,
  tasksCompleted: 12,
  tasksTotal: 15,
  enrolledDate: '2024-09-15',
  expectedEndDate: '2025-05-15',
  avatarColor: '#4f46e5'
})

// ========== الملاحظات ==========
const notes = ref([
  {
    id: 1,
    author: 'Dr. Sarah Chen',
    date: '2024-12-20',
    content: 'Emma is making excellent progress. Her technical skills are advancing rapidly and she\'s very proactive in seeking feedback.'
  }
])

// ========== المهام الأسبوعية ==========
const weeklyTasks = ref([
  {
    id: 1,
    title: 'Implement Authentication System',
    week: 12,
    grade: 'A',
    gradeClass: 'bg-primary-subtle text-primary border border-primary-subtle',
    feedback: 'Excellent implementation with proper security measures.'
  },
  {
    id: 2,
    title: 'Database Optimization Project',
    week: 13,
    grade: 'A-',
    gradeClass: 'bg-success-subtle text-success',
    feedback: 'Good work, but could use more indexing.'
  }
])

// ========== مقاييس الأداء ==========
const metrics = ref({
  avgScore: 'A-',
  avgScorePercent: 85,
  reportQuality: 92,
  attendance: 98
})

// ========== دوال مساعدة ==========
const hoursPercentage = computed(() => {
  return Math.round((student.value.hoursCompleted / student.value.hoursTotal) * 100)
})

const tasksPercentage = computed(() => {
  return Math.round((student.value.tasksCompleted / student.value.tasksTotal) * 100)
})

const statusClass = computed(() => `status-${student.value.status}`)

const statusIcon = computed(() => {
  const icons = {
    'on-track': 'bi bi-check-circle-fill',
    'at-risk': 'bi bi-exclamation-triangle-fill'
  }
  return icons[student.value.status] || 'bi bi-circle'
})

// ========== دوال التفاعل ==========
const addNote = () => {
  alert(t('add_note_functionality'))
}

const downloadReport = () => {
  alert(t('downloading_report'))
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
.student-details-page {
  padding: 20px 0;
}

.back-link {
  text-decoration: none;
  color: var(--text-muted);
  font-size: 14px;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  transition: all 0.2s ease;
}

.back-link:hover {
  color: var(--accent);
}

.profile-card {
  background: var(--card-bg);
  border-radius: 24px;
  padding: 30px;
  border: 1px solid var(--border-color);
  box-shadow: var(--card-shadow);
}

.avatar-box {
  width: 85px;
  height: 85px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 32px;
  font-weight: 700;
  flex-shrink: 0;
}

.status-badge {
  padding: 4px 14px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.status-on-track {
  background: #f0fdf4;
  color: #22c55e;
}

.status-at-risk {
  background: #fffbeb;
  color: #f59e0b;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 15px;
  margin-top: 20px;
}

.info-item {
  font-size: 13px;
  color: var(--text-muted);
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-outline-custom {
  border: 1px solid var(--border-color);
  background: var(--card-bg);
  color: var(--text-dark);
  padding: 10px 22px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.btn-outline-custom:hover {
  background: var(--accent-soft);
  border-color: var(--accent);
  color: var(--accent);
}

.btn-custom-purple {
  background: var(--accent);
  color: white;
  border: none;
  padding: 10px 22px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.btn-custom-purple:hover {
  opacity: 0.9;
  transform: translateY(-1px);
}

.details-card {
  background: var(--card-bg);
  border-radius: 24px;
  padding: 24px;
  border: 1px solid var(--border-color);
  box-shadow: var(--card-shadow);
  height: 100%;
}

.card-title {
  font-size: 15px;
  font-weight: 700;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
}

.progress-container {
  background: #eff6ff;
  border-radius: 18px;
  padding: 20px;
}

.progress-container.green {
  background: #f0fdf4;
}

.progress-custom {
  height: 8px;
  border-radius: 10px;
  background: rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  transition: width 1s ease;
}

.note-bubble {
  background: #f8fafc;
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 15px;
}

[data-theme="dark"] .note-bubble {
  background: #1e293b;
}

.task-item {
  border: 1px solid var(--border-color);
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 15px;
}

.feedback-box {
  background: #f8fafc;
  padding: 12px;
  border-radius: 12px;
  font-size: 13px;
  color: var(--text-muted);
  border-left: 3px solid var(--accent);
  margin-top: 10px;
}

[data-theme="dark"] .feedback-box {
  background: #1e293b;
}

.metric-row {
  margin-bottom: 18px;
}

.metric-label {
  font-size: 12px;
  color: var(--text-muted);
  display: flex;
  justify-content: space-between;
  margin-bottom: 6px;
}

.metric-bar {
  height: 6px;
  border-radius: 10px;
  transition: width 1s ease;
}

.bg-soft-blue {
  background: #eff6ff;
}

/* Responsive */
@media (max-width: 992px) {
  .info-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .profile-card {
    padding: 20px;
  }
  
  .avatar-box {
    width: 70px;
    height: 70px;
    font-size: 28px;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 576px) {
  .btn-outline-custom,
  .btn-custom-purple {
    width: 100%;
  }
  
  .task-item .d-flex {
    flex-direction: column;
    align-items: flex-start !important;
  }
}
</style>