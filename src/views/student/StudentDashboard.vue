<template>
  <div class="student-dashboard">
    <!-- الترحيب والمعلومات -->
    <div class="welcome-section mb-5" data-aos="fade-down">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <h2 class="fw-bold mb-2">{{ t('welcome_back', { name: studentName }) }}</h2>
          <p class="text-muted mb-0">
            {{ t('student_id') }}: {{ studentId }} | {{ t('student_account') }}
          </p>
        </div>
      </div>
    </div>

    <!-- بطاقات الإحصائيات -->
    <div class="row g-4 mb-5">
      <div class="col-sm-6 col-md-3" v-for="(stat, index) in stats" :key="index">
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

    <!-- بطاقة التقدم المميزة -->
    <div class="featured-progress mb-5" data-aos="fade-up">
      <div class="stat-card featured">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
          <div>
            <h5 class="fw-bold text-white mb-2">{{ t('training_progress') }}</h5>
            <p class="text-white-50 mb-0">{{ t('progress_description') }}</p>
          </div>
          <span class="progress-percentage fw-bold">{{ overallProgress }}%</span>
        </div>

        <!-- شريط التقدم -->
        <div class="progress-container mb-4">
          <div class="progress bg-white-20">
            <div
              class="progress-bar bg-white"
              :style="{ width: overallProgress + '%' }"
            ></div>
          </div>
        </div>

        <!-- أزرار الإجراءات -->
        <div class="d-flex gap-3 flex-wrap">
          <router-link
            to="/student/workspace"
            class="btn btn-light rounded-pill px-4 py-2"
          >
            <i class="bi bi-laptop me-2"></i>
            {{ t('explore_workspace') }}
          </router-link>
          <router-link
            to="/student/application-status"
            class="btn btn-outline-light rounded-pill px-4 py-2"
          >
            <i class="bi bi-file-earmark-text me-2"></i>
            {{ t('view_status') }}
          </router-link>
        </div>
      </div>
    </div>

    <!-- المهام الأسبوعية -->
    <div class="weekly-tasks-section">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold">
          {{ t('weekly_tasks') }}
          <span class="badge ms-2" :class="pendingTasksClass">
            {{ pendingTasks }} {{ t('pending') }}
          </span>
        </h5>
        <router-link to="/student/workspace" class="view-all-link">
          {{ t('view_all') }} <i class="bi bi-arrow-right ms-1"></i>
        </router-link>
      </div>

      <div class="row g-4">
        <div class="col-12" v-for="task in weeklyTasks" :key="task.id">
          <TaskCard
            :title="t(task.title)"
            :description="t(task.description)"
            :due-date="task.dueDate"
            :type="task.type"
            :status="task.status"
            :total-students="1"
            :submissions="task.completed ? 1 : 0"
            :points="task.points"
            :featured="task.featured"
            @click="goToTask(task)"
          >
            <template #actions>
              <button
                v-if="task.status === 'pending'"
                class="btn btn-primary btn-sm w-100"
                @click="submitTask(task)"
              >
                <i class="bi bi-send me-2"></i>
                {{ t('submit_task') }}
              </button>
              <button
                v-else
                class="btn btn-outline-success btn-sm w-100"
                @click="viewSubmission(task)"
              >
                <i class="bi bi-eye me-2"></i>
                {{ t('view_submission') }}
              </button>
            </template>
          </TaskCard>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from '@/composables/useI18n'
import StatCard from '@/components/common/StatCard.vue'
import TaskCard from '@/components/common/TaskCard.vue'
import AOS from 'aos'

const { t } = useI18n()
const router = useRouter()

// بيانات الطالب (من store لاحقاً)
const studentName = ref('Alex')
const studentId = ref('2152-1203-1502')

// إحصائيات الطالب
const stats = computed(() => [
  {
    icon: 'bi bi-journal-bookmark',
    label: 'total_programs',
    value: '1',
    variant: 'primary',
    bgIcon: 'bi bi-book-half',
    trend: '+1',
    trendUp: true
  },
  {
    icon: 'bi bi-check2-circle',
    label: 'total_tasks',
    value: '24',
    trend: '18 completed',
    trendUp: true,
    variant: 'success'
  },
  {
    icon: 'bi bi-hourglass-split',
    label: 'hours_left',
    value: '44h',
    trend: 'of 120 total hours',
    variant: 'warning'
  },
  {
    icon: 'bi bi-shield-check',
    label: 'accreditation',
    value: t('on_track'),
    variant: 'info',
    trend: 'meeting requirements'
  }
])

// التقدم العام
const overallProgress = ref(93)

// المهام الأسبوعية
const weeklyTasks = ref([
  {
    id: 1,
    title: 'react_hooks_assignment',
    description: 'react_hooks_description',
    dueDate: '2025-01-20',
    type: 'assignment',
    status: 'pending',
    points: 150,
    featured: true,
    completed: false
  },
  {
    id: 2,
    title: 'redux_quiz',
    description: 'redux_quiz_description',
    dueDate: '2025-01-15',
    type: 'quiz',
    status: 'completed',
    points: 50,
    completed: true
  }
])

const pendingTasks = computed(() => {
  return weeklyTasks.value.filter(t => t.status === 'pending').length
})

const pendingTasksClass = computed(() =>
  pendingTasks.value > 0 ? 'bg-warning text-dark' : 'bg-success'
)

// الدوال التفاعلية
const handleStatClick = (stat) => {
  console.log('Stat clicked:', stat)
}

const goToTask = (task) => {
  router.push(`/student/submit-task/${task.id}`)
}

const submitTask = (task) => {
  router.push(`/student/submit-task/${task.id}`)
}

const viewSubmission = (task) => {
  // التوجيه لعرض التقديم
  console.log('View submission:', task)
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
.student-dashboard {
  padding: 20px 0;
}

.welcome-section {
  padding: 20px 0;
}

.featured-progress {
  margin-top: 30px;
}

.stat-card.featured {
  background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
  color: white;
  padding: 40px;
  border-radius: 30px;
}

.stat-card.featured * {
  color: white !important;
}

.stat-card.featured .progress-container {
  max-width: 600px;
}

.bg-white-20 {
  background: rgba(255, 255, 255, 0.2);
}

.progress-bar.bg-white {
  background: white !important;
}

.progress-percentage {
  font-size: 2.5rem;
  font-weight: 700;
  line-height: 1;
}

.view-all-link {
  color: var(--accent);
  text-decoration: none;
  font-weight: 500;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.view-all-link:hover {
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
  .stat-card.featured {
    padding: 30px;
  }

  .progress-percentage {
    font-size: 2rem;
  }
}

@media (max-width: 576px) {
  .welcome-section h2 {
    font-size: 1.5rem;
  }

  .stat-card.featured {
    padding: 25px;
  }

  .progress-percentage {
    font-size: 1.8rem;
  }
}
</style>
