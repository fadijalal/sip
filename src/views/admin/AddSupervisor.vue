<template>
  <div class="add-supervisor-page">
    <div class="page-header mb-4">
      <h2 class="fw-bold mb-2">{{ t('add_supervisor') }}</h2>
      <p class="text-muted mb-0">{{ t('add_new_supervisor_description') }}</p>
    </div>

    <div class="form-card">
      <form @submit.prevent="handleSubmit">
        <div class="row g-4">
          <div class="col-md-6">
            <label class="form-label fw-bold">{{ t('first_name') }} *</label>
            <input type="text" class="form-control" v-model="form.firstName" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">{{ t('last_name') }} *</label>
            <input type="text" class="form-control" v-model="form.lastName" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">{{ t('email') }} *</label>
            <input type="email" class="form-control" v-model="form.email" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">{{ t('employee_id') }} *</label>
            <input type="text" class="form-control" v-model="form.employeeId" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">{{ t('department') }} *</label>
            <input type="text" class="form-control" v-model="form.department" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">{{ t('password') }} *</label>
            <input type="password" class="form-control" v-model="form.password" required>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary w-100" :disabled="isSubmitting">
              <span v-if="!isSubmitting">
                <i class="bi bi-person-plus me-2"></i>
                {{ t('add_supervisor') }}
              </span>
              <span v-else>
                <span class="spinner-border spinner-border-sm me-2"></span>
                {{ t('adding') }}
              </span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from '@/composables/useI18n'
import { useToastStore } from '@/stores/toast'
import { adminAPI } from '@/services/api/admin'

const { t } = useI18n()
const router = useRouter()
const toastStore = useToastStore()

const isSubmitting = ref(false)

const form = reactive({
  firstName: '',
  lastName: '',
  email: '',
  employeeId: '',
  department: '',
  password: ''
})

const handleSubmit = async () => {
  if (form.password.length < 8) {
    toastStore.addToast({
      type: 'error',
      title: t('error'),
      message: t('password_too_short')
    })
    return
  }

  isSubmitting.value = true
  try {
    const payload = {
      name: `${form.firstName} ${form.lastName}`,
      email: form.email,
      employee_id: form.employeeId,
      department: form.department,
      password: form.password,
      role: 'supervisor'
    }

    await adminAPI.createUser(payload)

    toastStore.addToast({
      type: 'success',
      title: t('success'),
      message: t('supervisor_added_successfully')
    })

    router.push('/admin/users')
  } catch (error) {
    toastStore.addToast({
      type: 'error',
      title: t('error'),
      message: error.response?.data?.message || t('error_occurred')
    })
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
.add-supervisor-page {
  padding: 20px 0;
  max-width: 800px;
  margin: 0 auto;
}

.form-card {
  background: var(--card-bg);
  border-radius: 24px;
  padding: 30px;
  border: 1px solid var(--border-color);
  box-shadow: var(--card-shadow);
}

.form-label {
  font-size: 13px;
  font-weight: 600;
  color: var(--text-dark);
  margin-bottom: 8px;
  display: block;
}

.form-control {
  background: var(--input-bg);
  border: 1px solid var(--border-color);
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 14px;
  color: var(--text-dark);
  width: 100%;
}

.form-control:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
  outline: none;
}

.btn-primary {
  background: linear-gradient(135deg, #7c3aed, #6d28d9);
  border: none;
  border-radius: 10px;
  padding: 12px;
  font-weight: 600;
  font-size: 15px;
  color: white;
  transition: all 0.3s ease;
  cursor: pointer;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(124, 58, 237, 0.3);
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .form-card {
    padding: 20px;
  }
}
</style>
