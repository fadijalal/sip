<template>
  <div class="login-page">
    <div class="login-container">
      <div class="login-card animate__animated animate__fadeIn">
        <div class="text-center mb-4">
          <div class="logo-box">
            <i class="bi bi-mortarboard-fill"></i>
          </div>
          <h3 class="fw-bold mb-1">SIP</h3>
          <p class="text-muted small">Sign in to access your training dashboard</p>
        </div>

        <form @submit.prevent="handleLogin">
          <div class="mb-3">
            <label class="form-label fw-bold small">
              <button type="button" class="switch-link" :class="{ active: identifierMode === 'id' }" @click="setIdentifierMode('id')">University ID</button>
              /
              <button type="button" class="switch-link" :class="{ active: identifierMode === 'email' }" @click="setIdentifierMode('email')">Email</button>
            </label>

            <div class="input-group">
              <span class="input-group-text">
                <i :class="identifierMode === 'email' ? 'fas fa-envelope' : 'fas fa-hashtag'"></i>
              </span>
              <input
                :type="identifierMode === 'email' ? 'email' : 'text'"
                class="form-control"
                :placeholder="identifierMode === 'email' ? 'student@example.com' : 'STU-2024-001'"
                v-model="form.identifier"
                required
                :disabled="isLoading"
              />
            </div>
          </div>

          <div class="mb-2">
            <label class="form-label fw-bold small">Password</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-lock"></i>
              </span>
              <input
                :type="showPassword ? 'text' : 'password'"
                class="form-control"
                placeholder="********"
                v-model="form.password"
                required
                :disabled="isLoading"
              />
              <button class="input-group-text password-toggle" type="button" @click="showPassword = !showPassword">
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center my-3">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="rememberMe" v-model="form.remember" />
              <label class="form-check-label small" for="rememberMe">Remember me</label>
            </div>
            <a href="#" class="forgot-link" @click.prevent="handleForgot">Forgot password?</a>
          </div>

          <button type="submit" class="btn-login" :disabled="isLoading">
            <span v-if="!isLoading">
              <i class="fas fa-right-to-bracket me-2"></i>
              Sign In
            </span>
            <span v-else>
              <span class="spinner-border spinner-border-sm me-2"></span>
              Signing in...
            </span>
          </button>

          <div class="text-center mt-4">
            <small class="text-muted">
              Don't have an account?
              <a href="#" @click.prevent="goToRegister" class="register-link fw-bold">Register now</a>
            </small>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useToastStore } from '@/stores/toast'

const toastStore = useToastStore()

const identifierMode = ref('id')
const isLoading = ref(false)
const showPassword = ref(false)

const form = reactive({
  identifier: '',
  password: '',
  remember: false,
})

const setIdentifierMode = (mode) => {
  identifierMode.value = mode
}

const handleLogin = () => {
  isLoading.value = true

  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  if (!token) {
    toastStore.addToast({ type: 'error', title: 'Error', message: 'CSRF token not found.' })
    isLoading.value = false
    return
  }

  const htmlForm = document.createElement('form')
  htmlForm.method = 'POST'
  htmlForm.action = '/login'

  const fields = {
    _token: token,
    identifier: form.identifier,
    password: form.password,
  }

  if (form.remember) {
    fields.remember = 'on'
  }

  Object.entries(fields).forEach(([name, value]) => {
    const input = document.createElement('input')
    input.type = 'hidden'
    input.name = name
    input.value = value
    htmlForm.appendChild(input)
  })

  document.body.appendChild(htmlForm)
  htmlForm.submit()
}

const handleForgot = () => {
  toastStore.addToast({ type: 'info', title: 'Info', message: 'Forgot password flow will be added next.' })
}

const goToRegister = () => {
  window.location.href = '/register'
}
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
}

.switch-link {
  border: 0;
  background: transparent;
  color: #64748b;
  font-weight: 600;
}

.switch-link.active {
  color: #374151;
}

.form-label {
  color: #1e293b;
}

.input-group {
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid #e2e8f0;
}

.input-group:focus-within {
  border-color: #7c3aed;
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

.btn-login {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 15px;
  color: #fff;
  background: linear-gradient(135deg, #7c3aed, #6d28d9);
}

.register-link {
  color: #7c3aed;
  text-decoration: none;
}

.forgot-link {
  color: #7c3aed;
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
}
</style>
