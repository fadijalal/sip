<template>
  <div class="register-page">
    <div class="register-container">
      <div class="register-card animate__animated animate__fadeIn">
        <div class="text-center">
          <div class="logo-box"><i class="bi bi-person-plus-fill fs-3"></i></div>
          <h3 class="fw-bold mb-1" v-text="t('create_account')"></h3>
          <p class="text-muted small mb-4" v-text="t('join_platform')"></p>
        </div>

        <div class="nav nav-pills nav-pills-custom" id="regTabs">
          <button v-for="tab in tabs" :key="tab.value" class="nav-link"
            :class="{ active: accountType === tab.value, individual: tab.value === 'individual', company: tab.value === 'company' }"
            @click="switchForm(tab.value)">
            {{ t(tab.label) }}
          </button>
        </div>

        <form @submit.prevent="handleSubmit">
          <div class="row g-3">
            <template v-for="(field, index) in currentFields" :key="index">
              <div :class="`col-md-${field.col} fade-animated`" :style="{ animationDelay: `${index * 0.05}s` }">
                <template v-if="!field.isAlert">
                  <label class="form-label fw-semibold">{{ t(field.label) }}<span class="text-danger">*</span></label>
                  <input :type="field.type" class="form-control" :placeholder="t(field.placeholder)" v-model="formData[field.model]" :required="field.required !== false" />
                </template>
                <div v-else class="info-alert"><i class="bi bi-shield-check me-2"></i>{{ t(field.text) }}</div>
              </div>
            </template>
          </div>

          <button type="submit" class="btn-register" :disabled="isLoading">
            <span v-if="!isLoading">{{ t('get_started') }} <i class="bi bi-arrow-right ms-2"></i></span>
            <span v-else><span class="spinner-border spinner-border-sm me-2"></span>{{ t('creating_account') }}</span>
          </button>

          <div class="text-center mt-4">
            <span class="small text-muted">{{ t('already_member') }}
              <a href="#" @click.prevent="goToLogin" class="login-link fw-bold">{{ t('log_in') }}</a>
            </span>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from '@/composables/useI18n'
import { useToastStore } from '@/stores/toast'
import { authAPI } from '@/services/api/auth'

const { t } = useI18n()
const router = useRouter()
const toastStore = useToastStore()

const accountType = ref('broker')

const tabs = [
  { value: 'broker', label: 'broker' },
  { value: 'individual', label: 'individual' },
  { value: 'company', label: 'company' }
]

const fieldsConfig = {
  broker: [
    { label: 'first_name', type: 'text', placeholder: 'first_name_ph', col: '6', model: 'brokerFirstName' },
    { label: 'username_id', type: 'text', placeholder: 'username_id_ph', col: '6', model: 'brokerUsername' },
    { label: 'email_address', type: 'email', placeholder: 'email_address_ph', col: '12', model: 'brokerEmail' },
    { label: 'password', type: 'password', placeholder: 'password_ph', col: '6', model: 'brokerPassword' },
    { label: 'confirm_password', type: 'password', placeholder: 'confirm_password_ph', col: '6', model: 'brokerConfirm' },
    { isAlert: true, text: 'verification_alert', col: '12' }
  ],
  individual: [
    { label: 'full_name', type: 'text', placeholder: 'full_name_ph', col: '12', model: 'individualName' },
    { label: 'id_number', type: 'text', placeholder: 'id_number_ph', col: '12', model: 'individualId' },
    { label: 'email', type: 'email', placeholder: 'email_ph', col: '12', model: 'individualEmail' },
    { label: 'password', type: 'password', placeholder: 'password_ph', col: '6', model: 'individualPassword' },
    { label: 'confirm_password', type: 'password', placeholder: 'confirm_password_ph', col: '6', model: 'individualConfirm' }
  ],
  company: [
    { label: 'legal_company_name', type: 'text', placeholder: 'legal_company_name_ph', col: '12', model: 'companyName' },
    { label: 'business_registration', type: 'text', placeholder: 'business_registration_ph', col: '12', model: 'companyReg' },
    { label: 'contact_email', type: 'email', placeholder: 'contact_email_ph', col: '12', model: 'companyEmail' },
    { label: 'password', type: 'password', placeholder: 'password_ph', col: '6', model: 'companyPassword' },
    { label: 'confirm_password', type: 'password', placeholder: 'confirm_password_ph', col: '6', model: 'companyConfirm' }
  ]
}

const formData = reactive({
  brokerFirstName: '', brokerUsername: '', brokerEmail: '', brokerPassword: '', brokerConfirm: '',
  individualName: '', individualId: '', individualEmail: '', individualPassword: '', individualConfirm: '',
  companyName: '', companyReg: '', companyEmail: '', companyPassword: '', companyConfirm: ''
})

const currentFields = computed(() => fieldsConfig[accountType.value] || [])

const isLoading = ref(false)

const switchForm = (type) => { accountType.value = type }

const handleSubmit = async () => {
  isLoading.value = true

  try {
    let password, confirmPassword, payload

    if (accountType.value === 'broker') {
      password = formData.brokerPassword
      confirmPassword = formData.brokerConfirm
      payload = {
        first_name: formData.brokerFirstName,
        username: formData.brokerUsername,
        email: formData.brokerEmail,
        password: password,
        role: 'broker'
      }
    } else if (accountType.value === 'individual') {
      password = formData.individualPassword
      confirmPassword = formData.individualConfirm
      payload = {
        name: formData.individualName,
        national_id: formData.individualId,
        email: formData.individualEmail,
        password: password,
        role: 'student'
      }
    } else {
      password = formData.companyPassword
      confirmPassword = formData.companyConfirm
      payload = {
        company_name: formData.companyName,
        registration_number: formData.companyReg,
        email: formData.companyEmail,
        password: password,
        role: 'company'
      }
    }

    if (password !== confirmPassword) {
      toastStore.addToast({ type: 'error', title: t('error'), message: t('passwords_do_not_match') })
      return
    }

    if (password.length < 8) {
      toastStore.addToast({ type: 'error', title: t('error'), message: t('password_too_short') })
      return
    }

    const response = await authAPI.register(payload)

    if (response.data.status === 'success') {
      toastStore.addToast({ type: 'success', title: t('success'), message: t('account_created_alert') })

      if (accountType.value === 'company') {
        router.push('/company/login')
      } else {
        router.push('/login')
      }
    }
  } catch (error) {
    toastStore.addToast({ type: 'error', title: t('error'), message: error.response?.data?.message || t('error_occurred') })
  } finally {
    isLoading.value = false
  }
}

const goToLogin = () => {
  if (accountType.value === 'company') {
    router.push('/company/login')
  } else {
    router.push('/login')
  }
}
</script>

<style scoped>
.register-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 30px 20px;
  background: linear-gradient(135deg, #f8f9fd 0%, #e0e7ff 100%);
}

.register-container { width: 100%; max-width: 650px; }
.register-card { background: white; border-radius: 24px; padding: 40px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05); border: 1px solid #e5e7eb; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.logo-box { width: 70px; height: 70px; background: #7c3aed; color: white; border-radius: 20px; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 8px 16px rgba(124, 58, 237, 0.2); }

.nav-pills-custom { background: #f1f5f9; padding: 6px; border-radius: 14px; margin-bottom: 30px; display: flex; gap: 5px; }
.nav-pills-custom .nav-link { flex: 1; border-radius: 10px; font-size: 14px; font-weight: 600; color: #64748b; padding: 12px; border: none; transition: all 0.3s ease; cursor: pointer; text-align: center; }
.nav-pills-custom .nav-link.active { background: #7c3aed; color: white; box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25); }
.nav-pills-custom .nav-link.active.individual { background: #f59e0b; }
.nav-pills-custom .nav-link.active.company { background: #10b981; }

@keyframes fadeInScale { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.fade-animated { animation: fadeInScale 0.4s ease forwards; }

.form-label { font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block; }
.form-control { border-radius: 12px; padding: 12px 16px; border: 1px solid #e2e8f0; transition: all 0.3s ease; width: 100%; font-size: 14px; }
.form-control:focus { border-color: #7c3aed; box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1); outline: none; }

.info-alert { background: #f3f0ff; border-radius: 12px; padding: 15px; border-left: 5px solid #7c3aed; font-size: 13px; color: #5845d4; margin-top: 10px; }

.btn-register { background: #7c3aed; color: white; border: none; border-radius: 12px; padding: 14px; font-weight: 700; width: 100%; transition: all 0.3s ease; cursor: pointer; margin-top: 20px; font-size: 16px; }
.btn-register:hover:not(:disabled) { background: #6d28d9; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(124, 58, 237, 0.3); }
.btn-register:disabled { opacity: 0.7; cursor: not-allowed; }

.login-link { color: #7c3aed; text-decoration: none; }
.login-link:hover { text-decoration: underline; }

[data-theme="dark"] .register-card { background: #1e293b; border-color: #334155; }
[data-theme="dark"] .form-label { color: #f3f4f6; }
[data-theme="dark"] .form-control { background: #374151; border-color: #4b5563; color: #f3f4f6; }
[data-theme="dark"] .form-control::placeholder { color: #9ca3af; }
[data-theme="dark"] .nav-pills-custom { background: #2d3748; }
[data-theme="dark"] .nav-pills-custom .nav-link { color: #a0aec0; }

@media (max-width: 768px) { .register-card { padding: 30px 20px; } .nav-pills-custom .nav-link { font-size: 13px; padding: 10px; } .btn-register { padding: 12px; font-size: 15px; } }
@media (max-width: 576px) { .register-container { max-width: 100%; } .col-md-6 { width: 100%; } .nav-pills-custom { flex-direction: column; } }
</style>
