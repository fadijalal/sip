<template>
  <div class="profile-page">
    <div class="profile-header mb-4" data-aos="fade-down">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div><h4 class="fw-bold mb-1">{{ t('profile_settings') }}</h4><p class="text-muted mb-0">{{ t('manage_account') }}</p></div>
        <ThemeToggle />
      </div>
    </div>

    <div class="settings-card mb-4" data-aos="fade-up">
      <div class="profile-photo-wrapper">
        <img :src="profileImage" alt="Profile" class="avatar-img" ref="avatarImg" />
        <div>
          <h6 class="mb-1 fw-bold">{{ t('profile_photo') }}</h6>
          <p class="text-muted small mb-3">{{ t('upload_instructions') }}</p>
          <div class="d-flex align-items-center gap-3 flex-wrap">
            <button class="upload-btn" @click="triggerFileInput"><i class="bi bi-upload me-2"></i>{{ t('upload_new') }}</button>
            <input type="file" ref="fileInput" hidden accept="image/*" @change="previewImage" />
            <button class="remove-link" @click="removeImage">{{ t('remove') }}</button>
          </div>
        </div>
      </div>
    </div>

    <form @submit.prevent="saveProfile">
      <div class="settings-card mb-4" data-aos="fade-up" data-aos-delay="100">
        <h6 class="card-title"><i class="bi bi-person text-muted me-2"></i>{{ t('personal_information') }}</h6>
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">{{ t('first_name') }}</label><input type="text" class="form-control" v-model="profile.firstName" required /></div>
          <div class="col-md-6"><label class="form-label">{{ t('last_name') }}</label><input type="text" class="form-control" v-model="profile.lastName" required /></div>
          <div class="col-12"><label class="form-label">{{ t('email_address') }}</label><input type="email" class="form-control" v-model="profile.email" required /></div>
          <div class="col-md-6"><label class="form-label">{{ t('phone_number') }}</label><input type="text" class="form-control" v-model="profile.phone" /></div>
          <div class="col-md-6"><label class="form-label">{{ t('location') }}</label><input type="text" class="form-control" v-model="profile.location" /></div>
          <div class="col-12"><label class="form-label">{{ t('organization') }}</label><input type="text" class="form-control" v-model="profile.organization" /></div>
          <div class="col-12">
            <label class="form-label">{{ t('bio') }}</label>
            <textarea class="form-control" rows="4" v-model="profile.bio" maxlength="200"></textarea>
            <div class="text-muted small mt-2">{{ profile.bio.length }}/200 {{ t('characters') }}</div>
          </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
          <button type="button" class="btn-cancel" @click="resetProfile">{{ t('cancel') }}</button>
          <button type="submit" class="btn-save" :disabled="isSaving">
            <span v-if="!isSaving"><i class="bi bi-check-lg me-2"></i>{{ t('save_changes') }}</span>
            <span v-else><span class="spinner-border spinner-border-sm me-2"></span>{{ t('saving') }}</span>
          </button>
        </div>
      </div>
    </form>

    <div class="settings-card mb-4" data-aos="fade-up" data-aos-delay="200">
      <h6 class="card-title"><i class="bi bi-lock text-muted me-2"></i>{{ t('change_password') }}</h6>
      <div class="row g-3">
        <div class="col-12"><label class="form-label">{{ t('current_password') }}</label><input type="password" class="form-control" v-model="password.current" placeholder="••••••••" /></div>
        <div class="col-12"><label class="form-label">{{ t('new_password') }}</label><input type="password" class="form-control" v-model="password.new" placeholder="••••••••" /><div class="text-muted small mt-2">{{ t('password_requirements') }}</div></div>
        <div class="col-12"><label class="form-label">{{ t('confirm_new_password') }}</label><input type="password" class="form-control" v-model="password.confirm" placeholder="••••••••" /></div>
      </div>
      <div class="d-flex justify-content-end gap-2 mt-4">
        <button class="btn-cancel" @click="resetPassword">{{ t('cancel') }}</button>
        <button class="btn-save" style="background-color: #6366f1" @click="updatePassword">{{ t('update_password') }}</button>
      </div>
    </div>

    <div class="settings-card danger-zone" data-aos="fade-up" data-aos-delay="300">
      <h6 class="danger-title mb-2">{{ t('danger_zone') }}</h6>
      <p class="text-muted small mb-4">{{ t('delete_warning') }}</p>
      <button class="btn-delete" @click="confirmDelete">{{ t('delete_account') }}</button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from '@/composables/useI18n'
import { useToastStore } from '@/stores/toast'
import { useAuthStore } from '@/stores/auth'
import ThemeToggle from '@/components/layout/ThemeToggle.vue'
import AOS from 'aos'

const { t } = useI18n()
const router = useRouter()
const toastStore = useToastStore()
const authStore = useAuthStore()

const profile = reactive({
  firstName: authStore.user?.name?.split(' ')[0] || 'Sarah',
  lastName: authStore.user?.name?.split(' ')[1] || 'Johnson',
  email: authStore.user?.email || 'sarah.johnson@university.edu',
  phone: authStore.user?.phone || '+1 (555) 123-4567',
  location: authStore.user?.location || 'San Francisco, CA',
  organization: authStore.user?.organization || 'Stanford University',
  bio: authStore.user?.bio || 'Computer Science Professor specializing in Machine Learning and AI. Passionate about education and research.'
})

const originalProfile = { ...profile }

const password = reactive({ current: '', new: '', confirm: '' })

const profileImage = ref(authStore.user?.avatar || 'https://ui-avatars.com/api/?name=User&background=7c3aed&color=fff')
const fileInput = ref(null)
const isSaving = ref(false)

const triggerFileInput = () => fileInput.value.click()
const previewImage = (event) => {
  const file = event.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => { profileImage.value = e.target.result }
    reader.readAsDataURL(file)
  }
}
const removeImage = () => { profileImage.value = 'https://ui-avatars.com/api/?name=User&background=7c3aed&color=fff' }

const saveProfile = async () => {
  isSaving.value = true
  try {
    await new Promise(resolve => setTimeout(resolve, 1500))
    Object.assign(originalProfile, profile)
    toastStore.addToast({ type: 'success', title: t('success'), message: t('profile_updated') })
  } finally {
    isSaving.value = false
  }
}

const resetProfile = () => { Object.assign(profile, originalProfile) }
const resetPassword = () => { password.current = ''; password.new = ''; password.confirm = '' }

const updatePassword = () => {
  if (password.new !== password.confirm) {
    toastStore.addToast({ type: 'error', title: t('error'), message: t('passwords_do_not_match') })
    return
  }
  if (password.new.length < 8) {
    toastStore.addToast({ type: 'error', title: t('error'), message: t('password_too_short') })
    return
  }
  toastStore.addToast({ type: 'success', title: t('success'), message: t('password_updated') })
  resetPassword()
}

const confirmDelete = () => {
  if (confirm(t('confirm_delete_account'))) {
    toastStore.addToast({ type: 'success', title: t('success'), message: t('account_deleted') })
    authStore.logout()
    router.push('/login')
  }
}

onMounted(() => {
  AOS.init({ duration: 800, once: true, easing: 'ease-in-out-sine' })
})
</script>

<style scoped>
.profile-page { padding: 20px 0; max-width: 800px; margin: 0 auto; }
.settings-card { background: var(--card-bg); border-radius: 12px; border: 1px solid var(--border-color); padding: 24px; box-shadow: var(--card-shadow); transition: all 0.3s ease; }
.profile-photo-wrapper { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
.avatar-img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid white; box-shadow: 0 0 0 1px var(--border-color); flex-shrink: 0; }
.upload-btn { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; padding: 6px 14px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s ease; color: var(--text-dark); display: inline-flex; align-items: center; }
.upload-btn:hover { background: var(--accent-soft); border-color: var(--accent); color: var(--accent); }
.remove-link { color: #ef4444; text-decoration: none; font-size: 13px; font-weight: 500; background: none; border: none; padding: 0; cursor: pointer; }
.remove-link:hover { text-decoration: underline; }
.card-title { font-size: 16px; font-weight: 600; margin-bottom: 24px; display: flex; align-items: center; color: var(--text-dark); }
.form-label { font-size: 13px; font-weight: 500; color: var(--text-dark); margin-bottom: 6px; display: block; }
.form-control { background-color: var(--input-bg); border: 1px solid var(--border-color); border-radius: 8px; padding: 10px 14px; font-size: 14px; transition: all 0.2s ease; color: var(--text-dark); width: 100%; }
.form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1); outline: none; }
.btn-save { background-color: var(--accent); color: white; border: none; border-radius: 8px; padding: 10px 20px; font-size: 14px; font-weight: 600; transition: all 0.2s ease; cursor: pointer; min-width: 120px; }
.btn-save:hover:not(:disabled) { opacity: 0.9; transform: translateY(-1px); }
.btn-save:disabled { opacity: 0.7; cursor: not-allowed; }
.btn-cancel { background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; padding: 10px 20px; font-size: 14px; font-weight: 600; color: var(--text-dark); cursor: pointer; transition: all 0.2s ease; min-width: 120px; }
.btn-cancel:hover { background: var(--accent-soft); border-color: var(--accent); color: var(--accent); }
.danger-zone { border: 1px solid #fee2e2; background-color: #fef2f2; }
[data-theme="dark"] .danger-zone { background-color: #450a0a; border-color: #7f1d1d; }
.danger-title { color: #b91c1c; font-weight: 700; }
[data-theme="dark"] .danger-title { color: #fca5a5; }
.btn-delete { background: var(--card-bg); border: 1px solid #fca5a5; color: #b91c1c; width: 100%; padding: 12px; border-radius: 8px; font-weight: 600; transition: all 0.2s ease; cursor: pointer; }
.btn-delete:hover { background: #fee2e2; }
@media (max-width: 768px) { .profile-page { padding: 15px; } .settings-card { padding: 20px; } }
@media (max-width: 576px) { .profile-photo-wrapper { flex-direction: column; text-align: center; } .btn-save, .btn-cancel { width: 100%; } .d-flex.justify-content-end { flex-direction: column; gap: 10px; } }
</style>
