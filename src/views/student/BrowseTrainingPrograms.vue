<template>
  <div class="browse-programs-page">
    <!-- رأس الصفحة -->
    <div class="page-header mb-4" data-aos="fade-down">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="header-icon">
            <i class="bi bi-mortarboard-fill"></i>
          </div>
          <div>
            <h4 class="fw-bold mb-1">{{ t('training_programs') }}</h4>
            <p class="text-muted mb-0">{{ t('explore_skills') }}</p>
          </div>
        </div>
        <router-link to="/student/dashboard" class="btn-back">
          <i class="bi bi-arrow-left me-2"></i>
          {{ t('back_dashboard') }}
        </router-link>
      </div>
    </div>

    <!-- شريط البحث -->
    <div class="search-container mb-4" data-aos="zoom-in">
      <i class="bi bi-search search-icon"></i>
      <input 
        type="text" 
        class="search-input" 
        :placeholder="t('search_placeholder')"
        v-model="searchQuery"
        @input="filterPrograms"
      />
    </div>

    <!-- المحتوى الرئيسي -->
    <div class="row g-4">
      <!-- الفلاتر الجانبية -->
      <div class="col-md-3" data-aos="fade-up">
        <div class="filter-card sticky-top" style="top: 100px">
          <h6 class="fw-bold mb-4">
            <i class="bi bi-sliders2-vertical me-2"></i>
            {{ t('filters') }}
          </h6>

          <!-- فلتر التخصص -->
          <div class="mb-4">
            <label class="form-label small fw-bold text-muted">
              {{ t('specialization') }}
            </label>
            <select class="form-select" v-model="filters.specialization">
              <option value="all">{{ t('all_specializations') }}</option>
              <option value="tech">{{ t('software_development') }}</option>
              <option value="design">{{ t('uiux_design') }}</option>
              <option value="marketing">{{ t('digital_marketing') }}</option>
            </select>
          </div>

          <!-- فلتر المدة -->
          <div class="mb-4">
            <label class="form-label small fw-bold text-muted">
              {{ t('duration') }}
            </label>
            <select class="form-select" v-model="filters.duration">
              <option value="all">{{ t('all_durations') }}</option>
              <option value="3">{{ t('months_3') }}</option>
              <option value="6">{{ t('months_6') }}</option>
              <option value="12">{{ t('months_12') }}</option>
            </select>
          </div>

          <!-- فلتر الوضع -->
          <div class="mb-4">
            <label class="form-label small fw-bold text-muted">
              {{ t('mode') }}
            </label>
            <select class="form-select" v-model="filters.mode">
              <option value="all">{{ t('all_modes') }}</option>
              <option value="online">{{ t('online') }}</option>
              <option value="hybrid">{{ t('hybrid') }}</option>
              <option value="onsite">{{ t('onsite') }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- قائمة البرامج -->
      <div class="col-md-9">
        <!-- معلومات عدد البرامج والترتيب -->
        <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-left">
          <h6 class="fw-bold text-muted">
            {{ filteredPrograms.length }} {{ t('programs_available') }}
          </h6>
          <select class="sort-select" v-model="sortBy">
            <option value="newest">{{ t('sort_newest') }}</option>
            <option value="popular">{{ t('sort_popular') }}</option>
            <option value="rating">{{ t('sort_rating') }}</option>
          </select>
        </div>

        <!-- شبكة البرامج -->
        <div class="row g-4">
          <div 
            v-for="program in sortedPrograms" 
            :key="program.id"
            class="col-md-6"
            data-aos="fade-up"
            :data-aos-delay="program.id * 50"
          >
            <div class="program-card" @click="viewProgramDetails(program)">
              <!-- رأس البطاقة مع التقييم والمفضلة -->
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="rating-badge">
                  <i class="bi bi-star-fill me-1"></i>
                  {{ program.rating }}
                  <small class="text-muted fw-normal">
                    ({{ program.enrolled }} {{ t('enrolled') }})
                  </small>
                </div>
                <button class="btn-favorite" @click.stop="toggleFavorite(program)">
                  <i :class="program.isFavorite ? 'bi bi-heart-fill text-danger' : 'bi bi-heart'"></i>
                </button>
              </div>

              <!-- عنوان البرنامج -->
              <h5 class="fw-bold mb-3 lh-base">{{ t(program.title) }}</h5>

              <!-- وسوم البرنامج -->
              <div class="mb-3 d-flex gap-2 flex-wrap">
                <span class="tag" :class="`tag-${program.level}`">
                  {{ t(program.level) }}
                </span>
                <span class="tag" :class="`tag-${program.mode}`">
                  {{ t(program.mode) }}
                </span>
              </div>

              <!-- وصف البرنامج -->
              <p class="text-muted small mb-4">{{ t(program.description) }}</p>

              <!-- المهارات -->
              <div class="d-flex flex-wrap gap-2 mb-4">
                <span 
                  v-for="skill in program.skills.slice(0, 3)" 
                  :key="skill"
                  class="skill-item"
                >
                  {{ t(skill) }}
                </span>
                <span v-if="program.skills.length > 3" class="skill-item text-primary">
                  +{{ program.skills.length - 3 }} {{ t('more') }}
                </span>
              </div>

              <!-- معلومات المدة والبدء -->
              <div class="details-box mb-4">
                <div>
                  <small class="text-muted d-block mb-1">
                    <i class="bi bi-clock me-1"></i> {{ t('duration') }}
                  </small>
                  <span class="fw-bold small">{{ program.duration }}</span>
                </div>
                <div>
                  <small class="text-muted d-block mb-1">
                    <i class="bi bi-calendar-event me-1"></i> {{ t('starts') }}
                  </small>
                  <span class="fw-bold small">{{ program.startDate }}</span>
                </div>
              </div>

              <!-- المدرب وزر التقديم -->
              <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                  <img 
                    :src="program.instructor.avatar" 
                    class="instructor-img rounded-circle" 
                    width="35"
                  />
                  <div style="line-height: 1">
                    <small class="fw-bold d-block mb-1">{{ program.instructor.name }}</small>
                    <small class="text-muted" style="font-size: 10px">
                      {{ t(program.instructor.title) }}
                    </small>
                  </div>
                </div>
                <button class="btn-apply">
                  {{ t('apply_now') }} <i class="bi bi-arrow-right ms-2"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from '@/composables/useI18n'
import AOS from 'aos'

const { t } = useI18n()
const router = useRouter()

// ========== بيانات البحث والتصفية ==========
const searchQuery = ref('')
const sortBy = ref('newest')

const filters = ref({
  specialization: 'all',
  duration: 'all',
  mode: 'all'
})

// ========== بيانات البرامج ==========
const programs = ref([
  {
    id: 1,
    title: 'advanced_web_dev',
    rating: 4.9,
    enrolled: 142,
    level: 'advanced',
    mode: 'hybrid',
    description: 'advanced_web_desc',
    skills: ['react', 'nextjs', 'aws', 'microservices'],
    duration: '120 hours',
    startDate: 'Jan 15, 2025',
    isFavorite: false,
    instructor: {
      name: 'Dr. Sarah Johnson',
      title: 'senior_architect',
      avatar: 'https://ui-avatars.com/api/?name=Sarah+J&background=7c3aed&color=fff'
    }
  },
  {
    id: 2,
    title: 'data_science_ml',
    rating: 4.8,
    enrolled: 218,
    level: 'intermediate',
    mode: 'online',
    description: 'data_science_desc',
    skills: ['python', 'tensorflow', 'visualization'],
    duration: '100 hours',
    startDate: 'Jan 20, 2025',
    isFavorite: true,
    instructor: {
      name: 'Prof. Michael Chen',
      title: 'data_scientist',
      avatar: 'https://ui-avatars.com/api/?name=Michael+C&background=10b981&color=fff'
    }
  }
])

// ========== دوال التصفية ==========
const filterPrograms = () => {
  // الفلتر يتم تلقائياً عبر computed
}

const filteredPrograms = computed(() => {
  return programs.value.filter(program => {
    // فلتر البحث
    const matchesSearch = t(program.title).toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         t(program.description).toLowerCase().includes(searchQuery.value.toLowerCase())
    
    // فلتر التخصص (سنفترض أن كل برنامج له تخصص)
    const specializationMatch = filters.value.specialization === 'all' || 
                               program.skills.includes(filters.value.specialization)
    
    return matchesSearch && specializationMatch
  })
})

// ========== ترتيب البرامج ==========
const sortedPrograms = computed(() => {
  const sorted = [...filteredPrograms.value]
  
  switch (sortBy.value) {
    case 'newest':
      return sorted // نفترض أنهم مرتبين بالأحدث
    case 'popular':
      return sorted.sort((a, b) => b.enrolled - a.enrolled)
    case 'rating':
      return sorted.sort((a, b) => b.rating - a.rating)
    default:
      return sorted
  }
})

// ========== دوال التفاعل ==========
const toggleFavorite = (program) => {
  program.isFavorite = !program.isFavorite
}

const viewProgramDetails = (program) => {
  router.push(`/student/program/${program.id}`)
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
.browse-programs-page {
  padding: 20px 0;
}

.header-icon {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #7c3aed, #a855f7);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 24px;
}

.btn-back {
  background: white;
  border: 1px solid var(--border-color);
  border-radius: 30px;
  padding: 8px 20px;
  color: var(--text-muted);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.btn-back:hover {
  background: var(--accent-soft);
  color: var(--accent);
  border-color: var(--accent);
}

.search-container {
  position: relative;
  max-width: 800px;
  margin: 0 auto;
}

.search-icon {
  position: absolute;
  left: 20px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--accent);
  z-index: 10;
}

.search-input {
  width: 100%;
  border-radius: 20px;
  padding: 15px 25px 15px 50px;
  border: none;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  background: var(--card-bg);
  color: var(--text-dark);
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  box-shadow: 0 10px 30px rgba(124, 58, 237, 0.15);
  transform: scale(1.01);
}

.filter-card {
  background: var(--card-bg);
  border-radius: 24px;
  padding: 25px;
  border: 1px solid var(--border-color);
}

.form-select {
  background: var(--input-bg);
  border: 1px solid var(--border-color);
  border-radius: 10px;
  padding: 10px;
  color: var(--text-dark);
  width: 100%;
}

.sort-select {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  border-radius: 30px;
  padding: 8px 20px;
  color: var(--text-dark);
  font-size: 14px;
  cursor: pointer;
}

.program-card {
  background: var(--card-bg);
  border-radius: 28px;
  padding: 30px;
  border: 1px solid transparent;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  height: 100%;
  position: relative;
  cursor: pointer;
}

.program-card:hover {
  transform: translateY(-12px);
  border-color: var(--accent);
  box-shadow: 0 25px 50px rgba(124, 58, 237, 0.1);
}

.rating-badge {
  background: #fff9db;
  color: #f08c00;
  padding: 5px 12px;
  border-radius: 10px;
  font-weight: 700;
  font-size: 14px;
}

.btn-favorite {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 1px solid var(--border-color);
  background: var(--card-bg);
  color: var(--text-muted);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-favorite:hover {
  background: #fee2e2;
  border-color: #fecaca;
}

.tag {
  padding: 6px 14px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.tag-advanced {
  background: #fee2e2;
  color: #ef4444;
}

.tag-intermediate {
  background: #fef3c7;
  color: #d97706;
}

.tag-hybrid {
  background: #f3f0ff;
  color: var(--accent);
}

.tag-online {
  background: #e0f2fe;
  color: #0284c7;
}

.tag-onsite {
  background: #e0e7ff;
  color: #4f46e5;
}

.skill-item {
  background: #f1f5f9;
  padding: 4px 12px;
  border-radius: 8px;
  font-size: 11px;
  color: #475569;
  font-weight: 500;
}

.details-box {
  background: #f8fafc;
  border-radius: 20px;
  padding: 15px;
  display: flex;
  justify-content: space-between;
}

[data-theme="dark"] .details-box {
  background: #1e293b;
}

.btn-apply {
  background: linear-gradient(135deg, #7c3aed, #a855f7);
  border: none;
  color: white;
  padding: 12px 25px;
  border-radius: 15px;
  font-weight: 600;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.btn-apply:hover {
  box-shadow: 0 10px 20px rgba(124, 58, 237, 0.3);
  transform: scale(1.05);
}

.instructor-img {
  transition: all 0.3s ease;
  cursor: pointer;
}

.instructor-img:hover {
  transform: scale(1.2) rotate(5deg);
}

/* Responsive */
@media (max-width: 768px) {
  .program-card {
    padding: 20px;
  }
  
  .details-box {
    flex-direction: column;
    gap: 10px;
  }
  
  .btn-apply {
    padding: 10px 20px;
    font-size: 13px;
  }
}

@media (max-width: 576px) {
  .program-card {
    padding: 18px;
  }
  
  .d-flex.justify-content-between {
    flex-direction: column;
    align-items: flex-start !important;
    gap: 10px;
  }
  
  .btn-favorite {
    align-self: flex-end;
  }
}
</style>