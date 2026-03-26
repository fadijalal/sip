import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api/api'
import { useToastStore } from './toast'

export const useAuthStore = defineStore('auth', () => {
  // ========== الحالة (State) ==========
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)
  const userType = ref(localStorage.getItem('userType') || null)

  // ========== دوال مساعدة (Getters) ==========
  const isAuthenticated = computed(() => !!token.value)

  // ========== الإجراءات (Actions) ==========

  // تعيين بيانات المستخدم
  function setUser(userData) {
    user.value = userData
    userType.value = userData?.role || userData?.type || null
    if (userType.value) {
      localStorage.setItem('userType', userType.value)
    }
  }

  // تعيين التوكن
  function setToken(newToken) {
    token.value = newToken
    if (newToken) {
      localStorage.setItem('token', newToken)
    } else {
      localStorage.removeItem('token')
    }
  }

  // تسجيل الدخول
  async function login(credentials, role = null) {
    try {
      // إضافة role إذا كان موجوداً
      const payload = role ? { ...credentials, role } : credentials
      const response = await api.post('/login', payload)

      if (response.data.status === 'success') {
        const { token, user: userData } = response.data.data
        setToken(token)
        setUser({ ...userData, type: role || userData.role })
        return { success: true, user: userData }
      }
      return { success: false, error: response.data.message }
    } catch (error) {
      console.error('Login error:', error)
      return {
        success: false,
        error: error.response?.data?.message || 'فشل تسجيل الدخول. يرجى المحاولة مرة أخرى.'
      }
    }
  }

  // تسجيل الخروج
  async function logout() {
    try {
      // محاولة إرسال طلب logout للخادم
      await api.post('/logout')
    } catch (error) {
      console.error('Logout API error:', error)
    } finally {
      // تنظيف البيانات المحلية
      user.value = null
      token.value = null
      userType.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('userType')
      localStorage.removeItem('user')
    }
  }

  // تسجيل مستخدم جديد
  async function register(userData) {
    try {
      const response = await api.post('/register', userData)
      if (response.data.status === 'success') {
        return { success: true, data: response.data.data }
      }
      return { success: false, error: response.data.message }
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.message || 'فشل إنشاء الحساب. يرجى المحاولة مرة أخرى.'
      }
    }
  }

  // تحديث الملف الشخصي
  async function updateProfile(data) {
    try {
      const response = await api.put('/profile', data)
      if (response.data.status === 'success') {
        setUser(response.data.data.user)
        return { success: true, user: response.data.data.user }
      }
      return { success: false, error: response.data.message }
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.message || 'فشل تحديث الملف الشخصي.'
      }
    }
  }

  // تغيير كلمة المرور
  async function changePassword(data) {
    try {
      const response = await api.post('/change-password', data)
      if (response.data.status === 'success') {
        return { success: true, message: response.data.message }
      }
      return { success: false, error: response.data.message }
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.message || 'فشل تغيير كلمة المرور.'
      }
    }
  }

  // التحقق من صلاحية التوكن
  async function verifyToken() {
    if (!token.value) return false
    try {
      const response = await api.get('/verify-token')
      return response.data.status === 'success'
    } catch (error) {
      // التوكن غير صالح
      logout()
      return false
    }
  }

  // ========== إرجاع الدوال والمتغيرات ==========
  return {
    // State
    user,
    token,
    userType,
    // Getters
    isAuthenticated,
    // Actions
    setUser,
    setToken,
    login,
    logout,
    register,
    updateProfile,
    changePassword,
    verifyToken
  }
})
