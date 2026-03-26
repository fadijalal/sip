<template>
  <div class="students-page">
    <!-- رأس الصفحة -->
    <div class="page-header mb-5" data-aos="fade-down">
      <div>
        <h2 class="fw-bold mb-2">{{ t('students_header') }}</h2>
        <p class="text-muted mb-0">{{ t('students_desc') }}</p>
      </div>
    </div>

    <!-- بطاقات الإحصائيات -->
    <div class="row g-4 mb-5">
      <div class="col-sm-6 col-md-4" v-for="stat in studentStats" :key="stat.key">
        <div class="stat-card-mini" :class="stat.bgClass">
          <div class="d-flex align-items-center gap-3">
            <div class="stat-icon-mini" :class="stat.iconClass">
              <i :class="stat.icon"></i>
            </div>
            <div>
              <div class="stat-label-mini">{{ t(stat.label) }}</div>
              <div class="stat-value-mini">{{ stat.value }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- شريط البحث والتصفية -->
    <div class="filter-section mb-4">
      <div class="search-wrapper">
        <i class="bi bi-search"></i>
        <input 
          type="text" 
          class="search-input" 
          :placeholder="t('search_students')"
          v-model="searchQuery"
          @input="filterStudents"
        />
      </div>
      
      <select class="form-select" v-model="programFilter">
        <option value="all">{{ t('all_programs') }}</option>
        <option value="software">{{ t('program_software') }}</option>
        <option value="ux">{{ t('program_ux') }}</option>
        <option value="data">{{ t('program_data') }}</option>
      </select>
    </div>

    <!-- شبكة الطلاب -->
    <div class="row g-4" id="studentsGrid">
      <div 
        v-for="student in filteredStudents" 
        :key="student.id"
        class="col-xl-6 col-lg-12"
        data-aos="fade-up"
        :data-aos-delay="student.id * 50"
      >
        <StudentCard
          :name="student.name"
          :email="student.email"
          :initials="student.initials"
          :company="student.company"
          :program="t(student.program)"
          :status="student.status"
          :hours-completed="student.hoursCompleted"
          :hours-total="student.hoursTotal"
          :tasks-completed="student.tasksCompleted"
          :tasks-total="student.tasksTotal"
          @click="viewStudentDetails(student)"
          @view="viewStudentDetails(student)"
          @note="addNote(student)"
        />
      </div>
    </div>

    <!-- مودال إضافة ملاحظة -->
    <Teleport to="body">
      <div v-if="showNoteModal" class="modal-overlay" @click.self="closeNoteModal">
        <div class="modal-content animate__animated animate__fadeInUp">
          <div class="modal-header">
            <h5 class="fw-bold mb-0">{{ t('add_note') }} - {{ selectedStudent?.name }}</h5>
            <button class="btn-close" @click="closeNoteModal">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveNote">
              <div class="mb-3">
                <label class="form-label fw-bold">{{ t('note_content') }}</label>
                <textarea 
                  class="form-control" 
                  rows="4"
                  v-model="noteContent"
                  :placeholder="t('note_placeholder')"
                  required
                ></textarea>
              </div>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-grow-1" @click="closeNoteModal">
                  {{ t('cancel') }}
                </button>
                <button type="submit" class="btn btn-primary flex-grow-1" :disabled="isSaving">
                  <span v-if="!isSaving">
                    <i class="bi bi-check-lg me-2"></i>
                    {{ t('save_note') }}
                  </span>
                  <span v-else>
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    {{ t('saving') }}
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from '@/composables/useI18n'
import StudentCard from '@/components/common/StudentCard.vue'
import AOS from 'aos'

const { t } = useI18n()
const router = useRouter()

// ========== إحصائيات الطلاب ==========
const studentStats = ref([
  {
    key: 'total',
    label: 'total_students',
    value: '8',
    bgClass: 'bg-primary-subtle',
    iconClass: 'text-primary',
    icon: 'bi bi-people-fill'
  },
  {
    key: 'on-track',
    label: 'on_track',
    value: '6',
    bgClass: 'bg-success-subtle',
    iconClass: 'text-success',
    icon: 'bi bi-check-circle-fill'
  },
  {
    key: 'attention',
    label: 'needs_attention',
    value: '2',
    bgClass: 'bg-warning-subtle',
    iconClass: 'text-warning',
    icon: 'bi bi-exclamation-triangle-fill'
  }
])

// ========== بيانات الطلاب ==========
const students = ref([
  {
    id: 1,
    name: 'Emma Thompson',
    email: 'emma.thompson@email.com',
    initials: 'ET',
    company: 'TechCorp Inc.',
    program: 'program_software',
    status: 'on-track',
    hoursCompleted: 180,
    hoursTotal: 240,
    tasksCompleted: 12,
    tasksTotal: 15,
    avatarColor: '#4f46e5'
  },
  {
    id: 2,
    name: 'Sarah Johnson',
    email: 'sarah.johnson@email.com',
    initials: 'SJ',
    company: 'DesignHub',
    program: 'program_ux',
    status: 'at-risk',
    hoursCompleted: 95,
    hoursTotal: 240,
    tasksCompleted: 5,
    tasksTotal: 15,
    avatarColor: '#f59e0b'
  },
  {
    id: 3,
    name: 'Michael Chen',
    email: 'michael.chen@email.com',
    initials: 'MC',
    company: 'DataWorks',
    program: 'program_data',
    status: 'on-track',
    hoursCompleted: 210,
    hoursTotal: 240,
    tasksCompleted: 14,
    tasksTotal: 15,
    avatarColor: '#10b981'
  },
  {
    id: 4,
    name: 'Lisa Brown',
    email: 'lisa.brown@email.com',
    initials: 'LB',
    company: 'Creative Studio',
    program: 'program_ux',
    status: 'on-track',
    hoursCompleted: 160,
    hoursTotal: 240,
    tasksCompleted: 10,
    tasksTotal: 15,
    avatarColor: '#8b5cf6'
  }
])

// ========== التصفية ==========
const searchQuery = ref('')
const programFilter = ref('all')

const filteredStudents = computed(() => {
  return students.value.filter(student => {
    // فلتر البحث
    const matchesSearch = student.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         student.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         student.company.toLowerCase().includes(searchQuery.value.toLowerCase())
    
    // فلتر البرنامج
    const matchesProgram = programFilter.value === 'all' || 
                          student.program === programFilter.value
    
    return matchesSearch && matchesProgram
  })
})

const filterStudents = () => {
  // الفلتر يتم تلقائياً عبر computed
}

// ========== مودال الملاحظات ==========
const showNoteModal = ref(false)
const selectedStudent = ref(null)
const noteContent = ref('')
const isSaving = ref(false)

const addNote = (student) => {
  selectedStudent.value = student
  showNoteModal.value = true
  noteContent.value = ''
  document.body.style.overflow = 'hidden'
}

const closeNoteModal = () => {
  showNoteModal.value = false
  document.body.style.overflow = ''
  selectedStudent.value = null
  noteContent.value = ''
}

const saveNote = async () => {
  isSaving.value = true
  try {
    // محاكاة حفظ الملاحظة
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    console.log('Note saved for', selectedStudent.value.name, ':', noteContent.value)
    
    // إظهار رسالة نجاح
    alert(t('note_saved'))
    closeNoteModal()
  } finally {
    isSaving.value = false
  }
}

// ========== دوال التنقل ==========
const viewStudentDetails = (student) => {
  router.push(`/supervisor/student/${student.id}`)
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
.students-page {
  padding: 20px 0;
}

.stat-card-mini {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 20px;
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
  height: 100%;
}

.stat-card-mini:hover {
  transform: translateY(-5px);
  box-shadow: var(--hover-shadow);
}

.stat-icon-mini {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  background: var(--card-bg);
}

.stat-label-mini {
  font-size: 13px;
  color: var(--text-muted);
  margin-bottom: 4px;
}

.stat-value-mini {
  font-size: 24px;
  font-weight: 700;
  color: var(--text-dark);
  line-height: 1;
}

.bg-primary-subtle {
  background: rgba(59, 130, 246, 0.1);
}

.bg-success-subtle {
  background: rgba(34, 197, 94, 0.1);
}

.bg-warning-subtle {
  background: rgba(245, 158, 11, 0.1);
}

.filter-section {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 15px;
  display: flex;
  gap: 15px;
  margin-bottom: 30px;
  border: 1px solid var(--border-color);
  flex-wrap: wrap;
}

.search-wrapper {
  position: relative;
  flex: 2 1 300px;
}

.search-wrapper i {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
}

.search-input {
  width: 100%;
  padding: 10px 15px 10px 45px;
  border: 1px solid var(--border-color);
  border-radius: 10px;
  background: var(--input-bg);
  color: var(--text-dark);
}

.search-input:focus {
  outline: none;
  border-color: var(--accent);
}

.filter-section .form-select {
  flex: 1 1 200px;
  border-radius: 10px;
  border: 1px solid var(--border-color);
  background: var(--input-bg);
  color: var(--text-dark);
  padding: 10px;
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
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
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
  transition: all 0.2s ease;
}

.btn-close:hover {
  background: var(--accent-soft);
  border-color: var(--accent);
}

/* Responsive */
@media (max-width: 768px) {
  .filter-section {
    flex-direction: column;
  }
  
  .search-wrapper {
    width: 100%;
  }
  
  .filter-section .form-select {
    width: 100%;
  }
}

@media (max-width: 576px) {
  .stat-icon-mini {
    width: 40px;
    height: 40px;
    font-size: 18px;
  }
  
  .stat-value-mini {
    font-size: 20px;
  }
  
  .modal-content {
    max-width: 100%;
  }
}
</style>