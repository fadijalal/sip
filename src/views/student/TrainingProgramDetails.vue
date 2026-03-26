<template>
  <div class="program-details-page">
    <!-- Hero Section -->
    <div class="hero-banner" data-aos="fade-in">
      <div class="container">
        <router-link to="/student/browse-programs" class="breadcrumb-custom">
          <i class="bi bi-chevron-left me-1"></i>
          {{ t('back_to_programs') }}
        </router-link>

        <div class="d-flex gap-2 mb-3 flex-wrap">
          <span class="badge-outline">{{ t(program.level) }}</span>
          <span class="badge-outline">{{ program.duration }}</span>
          <span class="badge-outline">
            <i class="bi bi-star-fill text-warning me-1"></i>
            {{ program.rating }} ({{ program.reviews }} {{ t('reviews') }})
          </span>
        </div>

        <h1 class="fw-bold mb-3">{{ t(program.title) }}</h1>
        <p class="fs-5 opacity-75">{{ t(program.subtitle) }}</p>

        <div class="d-flex gap-4 mt-4 small flex-wrap">
          <span><i class="bi bi-people me-2"></i>{{ program.enrolled }} {{ t('students_enrolled') }}</span>
          <span><i class="bi bi-clock me-2"></i>{{ program.hours }} {{ t('hours') }}</span>
          <span><i class="bi bi-calendar-check me-2"></i>{{ program.months }} {{ t('months') }}</span>
        </div>
      </div>
    </div>

    <!-- المحتوى الرئيسي -->
    <div class="container mt-5 pb-5">
      <div class="row g-4">
        <!-- العمود الأيسر - المحتوى -->
        <div class="col-lg-8" data-aos="fade-up">
          <!-- تبويبات التنقل -->
          <ul class="nav nav-tabs nav-tabs-custom">
            <li class="nav-item">
              <button 
                class="nav-link" 
                :class="{ active: activeTab === 'overview' }"
                @click="activeTab = 'overview'"
              >
                {{ t('overview') }}
              </button>
            </li>
            <li class="nav-item">
              <button 
                class="nav-link" 
                :class="{ active: activeTab === 'curriculum' }"
                @click="activeTab = 'curriculum'"
              >
                {{ t('curriculum') }}
              </button>
            </li>
            <li class="nav-item">
              <button 
                class="nav-link" 
                :class="{ active: activeTab === 'reviews' }"
                @click="activeTab = 'reviews'"
              >
                {{ t('reviews') }}
              </button>
            </li>
          </ul>

          <!-- محتوى حسب التبويب النشط -->
          <div class="content-card" v-if="activeTab === 'overview'">
            <h5 class="section-title">{{ t('about_program') }}</h5>
            <p class="text-muted lh-lg">{{ t('about_program_text') }}</p>
          </div>

          <div class="content-card" v-if="activeTab === 'overview'">
            <h5 class="section-title">{{ t('what_you_learn') }}</h5>
            <div class="row">
              <div v-for="(item, index) in learnItems" :key="index" class="col-md-6">
                <div class="check-item">
                  <i class="bi bi-check2-circle"></i>
                  <span>{{ t(item) }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="content-card" v-if="activeTab === 'overview'">
            <h5 class="section-title">{{ t('prerequisites') }}</h5>
            <div class="alert-box">
              <div class="d-flex gap-2 mb-2">
                <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                <strong>{{ t('important') }}</strong>
              </div>
              <ul class="mb-0">
                <li v-for="(req, index) in prerequisites" :key="index">
                  {{ t(req) }}
                </li>
              </ul>
            </div>
          </div>

          <div class="content-card" v-if="activeTab === 'overview'">
            <h5 class="section-title">{{ t('tools_tech') }}</h5>
            <div class="d-flex flex-wrap">
              <span v-for="tool in tools" :key="tool" class="tech-tag">
                {{ tool }}
              </span>
            </div>
          </div>
        </div>

        <!-- العمود الأيمن - المعلومات الجانبية -->
        <div class="col-lg-4" data-aos="fade-right">
          <div class="info-card-sidebar mb-4">
            <h6 class="fw-bold mb-4">{{ t('program_info') }}</h6>
            
            <div class="info-row">
              <span class="text-muted">{{ t('start_date') }}</span>
              <span class="fw-bold">{{ program.startDate }}</span>
            </div>
            
            <div class="info-row">
              <span class="text-muted">{{ t('duration') }}</span>
              <span class="fw-bold">{{ program.durationValue }}</span>
            </div>
            
            <div class="info-row mb-4">
              <span class="text-muted">{{ t('seats_left') }}</span>
              <span class="text-success fw-bold">
                <i class="bi bi-people me-1"></i>{{ program.seatsLeft }}
              </span>
            </div>

            <button class="btn-apply-big mb-3" @click="applyToProgram">
              {{ t('apply_btn') }}
            </button>

            <div class="d-flex gap-2">
              <button class="btn-share">
                <i class="bi bi-share me-2"></i>{{ t('share_btn') }}
              </button>
              <button class="btn-wishlist" @click="toggleWishlist">
                <i :class="isInWishlist ? 'bi bi-bookmark-fill' : 'bi bi-bookmark'"></i>
                {{ t('wishlist_btn') }}
              </button>
            </div>
          </div>

          <div class="info-card-sidebar stats-card">
            <h6 class="fw-bold mb-3">{{ t('program_stats') }}</h6>
            
            <div class="stat-row">
              <small>{{ t('enrolled_students') }}</small>
              <small class="fw-bold">{{ program.enrolled }}</small>
            </div>
            <div class="progress mb-3">
              <div class="progress-bar bg-primary" :style="{ width: enrollmentPercentage + '%' }"></div>
            </div>

            <div class="stat-row">
              <small>{{ t('completion_rate') }}</small>
              <small class="fw-bold">{{ program.completionRate }}%</small>
            </div>
          </div>

          <div class="help-card">
            <i class="bi bi-chat-dots-fill fs-2 mb-2 d-block"></i>
            <h6>{{ t('need_help') }}</h6>
            <p class="small opacity-75">{{ t('chat_advisor_text') }}</p>
            <button class="btn-chat">{{ t('chat_advisor_btn') }}</button>
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

const { t } = useI18n()
const route = useRoute()
const router = useRouter()

// ========== حالة التبويب ==========
const activeTab = ref('overview')

// ========== بيانات البرنامج (محاكاة) ==========
const program = ref({
  id: 1,
  title: 'program_title',
  subtitle: 'program_subtitle',
  level: 'level_advanced',
  duration: '12 weeks',
  rating: 4.9,
  reviews: 142,
  enrolled: 142,
  hours: '120 hours',
  months: '3 months',
  startDate: 'January 15, 2025',
  durationValue: '3 Months',
  seatsLeft: 8,
  completionRate: 92,
  enrollmentPercentage: 85
})

// ========== قوائم المحتوى ==========
const learnItems = ref([
  'learn_1', 'learn_2', 'learn_3', 'learn_4', 'learn_5', 'learn_6'
])

const prerequisites = ref([
  'req_1', 'req_2', 'req_3'
])

const tools = ref([
  'React & Next.js', 'Node.js & Express', 'PostgreSQL & MongoDB',
  'AWS Cloud Services', 'Docker & Kubernetes', 'Terraform',
  'GraphQL & Apollo', 'Redis Cache'
])

// ========== حالة المفضلة ==========
const isInWishlist = ref(false)

// ========== دوال مساعدة ==========
const enrollmentPercentage = computed(() => {
  return program.value.enrollmentPercentage
})

// ========== دوال التفاعل ==========
const applyToProgram = () => {
  router.push('/student/application-status')
}

const toggleWishlist = () => {
  isInWishlist.value = !isInWishlist.value
}

// تهيئة AOS
onMounted(() => {
  AOS.init({
    duration: 1000,
    once: true,
    easing: 'ease-in-out-sine'
  })
})
</script>

<style scoped>
.program-details-page {
  min-height: 100vh;
}

.hero-banner {
  background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
  color: white;
  padding: 60px 0;
  border-radius: 0 0 40px 40px;
}

.breadcrumb-custom {
  color: rgba(255, 255, 255, 0.8);
  font-size: 14px;
  text-decoration: none;
  display: inline-block;
  margin-bottom: 20px;
  transition: all 0.2s ease;
}

.breadcrumb-custom:hover {
  color: white;
}

.badge-outline {
  border: 1px solid rgba(255, 255, 255, 0.4);
  background: rgba(255, 255, 255, 0.1);
  padding: 5px 15px;
  border-radius: 20px;
  font-size: 12px;
}

.nav-tabs-custom {
  border-bottom: 2px solid var(--border-color);
  margin-bottom: 30px;
  gap: 10px;
}

.nav-tabs-custom .nav-link {
  border: none;
  font-weight: 600;
  color: var(--text-muted);
  padding: 12px 25px;
  background: transparent;
  cursor: pointer;
  transition: all 0.3s ease;
}

.nav-tabs-custom .nav-link.active {
  color: var(--accent);
  border-bottom: 3px solid var(--accent);
}

.content-card {
  background: var(--card-bg);
  border-radius: 24px;
  padding: 35px;
  margin-bottom: 25px;
  border: 1px solid var(--border-color);
}

.section-title {
  font-weight: 700;
  font-size: 20px;
  margin-bottom: 20px;
  color: var(--text-dark);
}

.check-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 15px;
  font-size: 14px;
}

.check-item i {
  color: #10b981;
  font-size: 18px;
  flex-shrink: 0;
  margin-top: 2px;
}

.alert-box {
  background: #fffbeb;
  border-left: 5px solid #f59e0b;
  padding: 20px;
  border-radius: 15px;
  font-size: 14px;
}

.alert-box ul {
  padding-left: 20px;
  margin-top: 10px;
}

.alert-box li {
  margin-bottom: 5px;
}

.tech-tag {
  background: #f3f0ff;
  color: var(--accent);
  padding: 6px 15px;
  border-radius: 10px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
  margin: 4px;
}

.info-card-sidebar {
  background: var(--card-bg);
  border-radius: 24px;
  padding: 25px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
  border: 1px solid var(--border-color);
  position: sticky;
  top: 20px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid var(--border-color);
}

.info-row:last-of-type {
  border-bottom: none;
}

.btn-apply-big {
  background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
  color: white;
  border: none;
  width: 100%;
  padding: 15px;
  border-radius: 15px;
  font-weight: 700;
  transition: all 0.3s ease;
  cursor: pointer;
}

.btn-apply-big:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 20px rgba(124, 58, 237, 0.3);
}

.btn-share,
.btn-wishlist {
  flex: 1;
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  padding: 10px;
  font-size: 13px;
  font-weight: 500;
  color: var(--text-dark);
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-share:hover,
.btn-wishlist:hover {
  background: var(--accent-soft);
  border-color: var(--accent);
  color: var(--accent);
}

.stats-card {
  background: var(--card-bg);
  margin-top: 20px;
}

.stat-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 5px;
}

.progress {
  height: 6px;
  border-radius: 10px;
  background: var(--border-color);
}

.help-card {
  background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
  border-radius: 24px;
  padding: 25px;
  color: white;
  text-align: center;
  margin-top: 20px;
}

.btn-chat {
  background: white;
  color: var(--accent);
  border: none;
  border-radius: 30px;
  padding: 10px 20px;
  font-weight: 600;
  width: 100%;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-chat:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(255, 255, 255, 0.2);
}

/* Responsive */
@media (max-width: 992px) {
  .info-card-sidebar {
    position: static;
    margin-bottom: 20px;
  }
  
  .hero-banner {
    padding: 40px 0;
  }
  
  .hero-banner h1 {
    font-size: 28px;
  }
}

@media (max-width: 768px) {
  .content-card {
    padding: 25px;
  }
  
  .section-title {
    font-size: 18px;
  }
  
  .nav-tabs-custom .nav-link {
    padding: 10px 15px;
    font-size: 14px;
  }
}

@media (max-width: 576px) {
  .hero-banner {
    padding: 30px 0;
  }
  
  .hero-banner h1 {
    font-size: 24px;
  }
  
  .badge-outline {
    font-size: 11px;
    padding: 4px 10px;
  }
  
  .d-flex.gap-4 {
    gap: 10px !important;
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>