<template>
  <div class="login-page">
    <div class="login-container">
      <div class="login-card animate__animated animate__fadeIn">
        <!-- الشعار -->
        <div class="text-center mb-4">
          <div class="logo-box">
            <i class="bi bi-mortarboard-fill"></i>
          </div>
          <h3 class="fw-bold mb-1">{{ t('welcome_back', { name: '' }) }}</h3>
          <p class="text-muted small">{{ t('login_to_platform') }}</p>
        </div>

        <!-- اختيار نوع المستخدم -->
        <div class="role-selector mb-4">
          <div class="role-buttons">
            <button
              v-for="role in roles"
              :key="role.value"
              type="button"
              class="role-btn"
              :class="{ active: selectedRole === role.value }"
              @click="selectedRole = role.value"
            >
              <i :class="role.icon"></i>
              <span>{{ t(role.label) }}</span>
            </button>
          </div>
        </div>

        <!-- شارة نوع الوصول -->
        <div class="access-badge" :class="badgeClass">
          <i :class="currentRoleIcon"></i>
          {{ t(currentRoleLabel) }}
        </div>

        <!-- نموذج تسجيل الدخول -->
        <form @submit.prevent="handleLogin">
          <!-- حقل البريد الإلكتروني / رقم الطالب / رقم المشرف -->
          <div class="mb-4">
            <label class="form-label fw-bold small">{{ t(currentUserLabel) }}</label>
            <div class="input-group">
              <span class="input-group-text">
                <i :class="currentUserIcon"></i>
              </span>
              <input
                type="text"
                class="form-control"
                :placeholder="t(currentUserPlaceholder)"
                v-model="form.identifier"
                required
                :disabled="isLoading"
              />
            </div>
          </div>

          <!-- حقل كلمة المرور -->
          <div class="mb-3">
            <label class="form-label fw-bold small">{{ t('password_label') }}</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-lock"></i>
              </span>
              <input
                :type="showPassword ? 'text' : 'password'"
                class="form-control"
                :placeholder="t('password_placeholder')"
                v-model="form.password"
                required
                :disabled="isLoading"
              />
              <button
                class="input-group-text password-toggle"
                type="button"
                @click="showPassword = !showPassword"
              >
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
          </div>

          <!-- خيارات إضافية (تذكرني / نسيت كلمة المرور) -->
          <div class="d-flex justify-content-between align-items-center my-3">
            <div class="form-check">
              <input
                type="checkbox"
                class="form-check-input"
                id="rememberMe"
                v-model="form.remember"
              />
              <label class="form-check-label small" for="rememberMe" v-text="t('remember_me')"></label>
            </div>
            <a href="#" class="forgot-link" @click.prevent="handleForgot" v-text="t('forgot_password')"></a>
          </div>

          <!-- زر تسجيل الدخول -->
          <button
            type="submit"
            class="btn-login"
            :class="btnClass"
            :disabled="isLoading"
          >
            <span v-if="!isLoading">
              <i :class="btnIcon" class="me-2"></i>
              {{ t('sign_in_as', { role: t(currentRoleLabel) }) }}
            </span>
            <span v-else>
              <span class="spinner-border spinner-border-sm me-2"></span>
              {{ t('signing_in') }}
            </span>
          </button>

          <!-- رابط إنشاء حساب (للمستخدمين العاديين والشركات) -->
          <div class="text-center mt-4">
            <small class="text-muted">
              {{ t('no_account') }}
              <a href="#" @click.prevent="goToRegister" class="register-link fw-bold">
                {{ selectedRole === 'company' ? t('register_company') : t('register_now') }}
              </a>
            </small>
          </div>
        </form>

        <!-- رسالة إضافية للمشرفين -->
        <div v-if="selectedRole === 'supervisor'" class="first-time-box mt-4">
          <i class="bi bi-info-circle me-2"></i>
          {{ t('supervisor_first_time') }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from '@/composables/useI18n'
import { useToastStore } from '@/stores/toast'

const { t, currentLang } = useI18n()
const router = useRouter()
const authStore = useAuthStore()
const toastStore = useToastStore()

// أنواع المستخدمين
const roles = [
  { value: 'student', label: 'student_access', icon: 'bi bi-mortarboard-fill' },
  { value: 'supervisor', label: 'supervisor_access', icon: 'bi bi-chalkboard-fill' },
  { value: 'company', label: 'company_access', icon: 'bi bi-building' },
  { value: 'admin', label: 'admin_access', icon: 'bi bi-shield-lock-fill' }
]

// الدور المحدد
const selectedRole = ref('student')

// حالة النموذج
const isLoading = ref(false)
const showPassword = ref(false)

const form = reactive({
  identifier: '',
  password: '',
  remember: false
})

// ========== إعدادات ديناميكية حسب الدور ==========
const currentRoleLabel = computed(() => {
  const labels = {
    student: 'student_access',
    supervisor: 'supervisor_access',
    company: 'company_access',
    admin: 'admin_access'
  }
  return labels[selectedRole.value] || 'student_access'
})

const currentRoleIcon = computed(() => {
  const icons = {
    student: 'bi bi-mortarboard-fill',
    supervisor: 'bi bi-chalkboard-fill',
    company: 'bi bi-building',
    admin: 'bi bi-shield-lock-fill'
  }
  return icons[selectedRole.value] || 'bi bi-person'
})

const currentUserLabel = computed(() => {
  const labels = {
    student: 'university_id',
    supervisor: 'university_id',
    company: 'email_label',
    admin: 'email_label'
  }
  return labels[selectedRole.value] || 'email_label'
})

const currentUserPlaceholder = computed(() => {
  const placeholders = {
    student: 'student_id_placeholder',
    supervisor: 'supervisor_id_placeholder',
    company: 'email_placeholder',
    admin: 'email_placeholder'
  }
  return placeholders[selectedRole.value] || 'email_placeholder'
})

const currentUserIcon = computed(() => {
  const icons = {
    student: 'fas fa-hashtag',
    supervisor: 'fas fa-id-card',
    company: 'fas fa-envelope',
    admin: 'fas fa-envelope'
  }
  return icons[selectedRole.value] || 'fas fa-envelope'
})

const badgeClass = computed(() => {
  const classes = {
    student: 'badge-student',
    supervisor: 'badge-supervisor',
    company: 'badge-company',
    admin: 'badge-admin'
  }
  return classes[selectedRole.value] || 'badge-student'
})

const btnClass = computed(() => {
  const classes = {
    student: 'btn-student',
    supervisor: 'btn-supervisor',
    company: 'btn-company',
    admin: 'btn-admin'
  }
  return classes[selectedRole.value] || 'btn-student'
})

const btnIcon = computed(() => {
  const icons = {
    student: 'fas fa-right-to-bracket',
    supervisor: 'fas fa-chalkboard-user',
    company: 'fas fa-building',
    admin: 'fas fa-user-shield'
  }
  return icons[selectedRole.value] || 'fas fa-right-to-bracket'
})

// ========== دوال تسجيل الدخول ==========
const handleLogin = async () => {
  isLoading.value = true

  try {
    let credentials = {}

    // بناء بيانات الاعتماد حسب نوع المستخدم
    if (selectedRole.value === 'company' || selectedRole.value === 'admin') {
      credentials = { email: form.identifier, password: form.password }
    } else {
      // الطالب والمشرف يستخدمان student_id أو employee_id
      credentials = {
        [selectedRole.value === 'student' ? 'student_id' : 'employee_id']: form.identifier,
        password: form.password
      }
    }

    const result = await authStore.login(credentials, selectedRole.value)

    if (result.success) {
      // التوجيه للوحة المناسبة
      const routes = {
        student: '/student/dashboard',
        supervisor: '/supervisor/dashboard',
        company: '/company/dashboard',
        admin: '/admin/dashboard'
      }
      router.push(routes[selectedRole.value])
    } else {
      toastStore.addToast({
        type: 'error',
        title: t('error'),
        message: result.error || t('invalid_credentials')
      })
    }
  } catch (error) {
    toastStore.addToast({
      type: 'error',
      title: t('error'),
      message: t('login_failed')
    })
  } finally {
    isLoading.value = false
  }
}

const handleForgot = () => {
  toastStore.addToast({
    type: 'info',
    title: t('info'),
    message: t('forgot_password_message')
  })
}

const goToRegister = () => {
  if (selectedRole.value === 'company') {
    router.push('/company/register')
  } else {
    router.push('/register')
  }
}

onMounted(() => {
  document.documentElement.lang = currentLang.value
  document.documentElement.dir = currentLang.value === 'ar' ? 'rtl' : 'ltr'
})
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.login-container {
  width: 100%;
  max-width: 480px;
}

.login-card {
  background: white;
  border-radius: 32px;
  padding: 40px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  transition: transform 0.3s ease;
}

.login-card:hover {
  transform: translateY(-5px);
}

.logo-box {
  width: 70px;
  height: 70px;
  background: linear-gradient(135deg, #7c3aed, #6d28d9);
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
  color: white;
  font-size: 30px;
  box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.3);
}

/* أزرار اختيار الدور */
.role-selector {
  background: #f1f5f9;
  border-radius: 16px;
  padding: 6px;
}

.role-buttons {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.role-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 10px 12px;
  border: none;
  border-radius: 12px;
  background: transparent;
  color: #64748b;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.role-btn i {
  font-size: 16px;
}

.role-btn:hover {
  background: rgba(124, 58, 237, 0.1);
  color: #7c3aed;
}

.role-btn.active {
  background: #7c3aed;
  color: white;
  box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
}

.role-btn.active.student-active {
  background: #7c3aed;
}

.role-btn.active.supervisor-active {
  background: #4f46e5;
}

.role-btn.active.company-active {
  background: #10b981;
}

.role-btn.active.admin-active {
  background: #ef4444;
}

/* شارة نوع الوصول */
.access-badge {
  display: block;
  width: fit-content;
  margin: 20px auto 25px;
  padding: 8px 24px;
  border-radius: 50px;
  font-weight: 600;
  font-size: 13px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.badge-student {
  background: #f3f0ff;
  color: #7c3aed;
}

.badge-supervisor {
  background: #e0e7ff;
  color: #4f46e5;
}

.badge-company {
  background: #d1fae5;
  color: #10b981;
}

.badge-admin {
  background: #fee2e2;
  color: #ef4444;
}

.form-label {
  display: block;
  margin-bottom: 8px;
  color: #1e293b;
}

.input-group {
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
}

.input-group:focus-within {
  border-color: var(--accent);
  box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
}

.input-group-text {
  background: transparent;
  border: none;
  color: #94a3b8;
  padding: 0 15px;
}

.form-control {
  border: none;
  padding: 12px 15px 12px 0;
  font-size: 15px;
  background: transparent;
}

.form-control:focus {
  box-shadow: none;
  outline: none;
}

.password-toggle {
  cursor: pointer;
  background: transparent;
  border: none;
  color: #94a3b8;
  padding: 0 15px;
}

.password-toggle:hover {
  color: var(--accent);
}

.form-check-input:checked {
  background-color: var(--accent);
  border-color: var(--accent);
}

.forgot-link {
  color: var(--accent);
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
}

.forgot-link:hover {
  text-decoration: underline;
}

/* أزرار تسجيل الدخول حسب الدور */
.btn-login {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  cursor: pointer;
  margin-top: 20px;
}

.btn-student {
  background: linear-gradient(135deg, #7c3aed, #6d28d9);
  color: white;
  box-shadow: 0 10px 15px -3px rgba(124, 58, 237, 0.3);
}

.btn-supervisor {
  background: linear-gradient(135deg, #4f46e5, #4338ca);
  color: white;
  box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
}

.btn-company {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
}

.btn-admin {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: white;
  box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3);
}

.btn-login:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
}

.btn-login:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.register-link {
  color: var(--accent);
  text-decoration: none;
}

.register-link:hover {
  text-decoration: underline;
}

.first-time-box {
  background: #eff6ff;
  border-radius: 12px;
  padding: 12px 16px;
  text-align: center;
  font-size: 12px;
  color: #1e40af;
  border: 1px solid #dbeafe;
  margin-top: 20px;
}

/* الوضع الليلي */
[data-theme="dark"] .login-card {
  background: #1e293b;
}

[data-theme="dark"] .form-label {
  color: #f3f4f6;
}

[data-theme="dark"] .form-control {
  color: #f3f4f6;
}

[data-theme="dark"] .form-control::placeholder {
  color: #9ca3af;
}

[data-theme="dark"] .input-group {
  border-color: #4b5563;
}

[data-theme="dark"] .role-selector {
  background: #2d3748;
}

[data-theme="dark"] .role-btn {
  color: #a0aec0;
}

[data-theme="dark"] .first-time-box {
  background: #1e3a8a33;
  color: #93c5fd;
  border-color: #2563eb33;
}

/* Responsive */
@media (max-width: 576px) {
  .login-card {
    padding: 30px 20px;
  }

  .logo-box {
    width: 60px;
    height: 60px;
    font-size: 24px;
  }

  .role-buttons {
    flex-direction: column;
  }

  .role-btn {
    justify-content: center;
  }

  h3 {
    font-size: 1.5rem;
  }

  .btn-login {
    padding: 12px;
    font-size: 14px;
  }
}
</style>
