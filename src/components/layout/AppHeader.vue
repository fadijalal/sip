<template>
  <header class="page-header">
    <div class="container-fluid px-3 px-md-4 px-lg-5">
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
          <!-- زر فتح القائمة للموبايل -->
          <button
            class="menu-toggle d-md-none"
            @click="$emit('toggle-sidebar')"
            :aria-label="t('toggle_menu')"
          >
            <i class="bi bi-list"></i>
          </button>

          <!-- شعار الصفحة (يختلف حسب المسار) -->
          <div class="d-flex align-items-center gap-2">
            <div
              class="icon-box"
              :style="{ background: pageIcon.background }"
            >
              <i :class="pageIcon.icon"></i>
            </div>
            <div>
              <h5 class="fw-bold mb-0" v-text="pageTitle"></h5>
              <small class="text-muted" v-if="pageSubtitle" v-text="pageSubtitle"></small>
            </div>
          </div>
        </div>

        <!-- أدوات التحكم -->
        <div class="d-flex align-items-center gap-2 gap-sm-3">
          <!-- البحث (اختياري) -->
          <div v-if="showSearch" class="search-wrapper d-none d-lg-block">
            <i class="bi bi-search"></i>
            <input
              type="text"
              class="search-input"
              :placeholder="t('search')"
              @input="$emit('search', $event.target.value)"
            />
          </div>

          <!-- الإشعارات -->
          <div class="dropdown">
            <button
              class="btn-notification"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <i class="bi bi-bell"></i>
              <span v-if="unreadCount > 0" class="notification-badge">
                {{ unreadCount > 9 ? '9+' : unreadCount }}
              </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end notification-dropdown">
              <div class="notification-header">
                <h6 class="fw-bold mb-0" v-text="t('notifications')"></h6>
                <router-link to="/notifications" class="small" v-text="t('view_all')"></router-link>
              </div>
              <div class="notification-list">
                <div v-for="notif in recentNotifications" :key="notif.id" class="notification-item">
                  <!-- محتوى الإشعار -->
                  <div class="d-flex gap-2 p-2">
                    <i :class="notif.icon" class="text-primary"></i>
                    <div>
                      <p class="mb-0 small fw-bold">{{ notif.title }}</p>
                      <small class="text-muted">{{ notif.time }}</small>
                    </div>
                  </div>
                </div>
                <div v-if="recentNotifications.length === 0" class="text-center p-3 text-muted small">
                  {{ t('no_notifications') }}
                </div>
              </div>
            </div>
          </div>

          <!-- تبديل اللغة -->
          <LanguageSwitcher />

          <!-- تبديل الثيم -->
          <ThemeToggle />

          <!-- صورة المستخدم -->
          <router-link to="/profile" class="user-avatar">
            <img
              :src="userAvatar || 'https://ui-avatars.com/api/?name=User&background=7c3aed&color=fff'"
              alt="Profile"
              class="rounded-circle border"
              width="40"
              height="40"
            />
          </router-link>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useNotificationsStore } from '@/stores/notifications'
import { useI18n } from '@/composables/useI18n'
import LanguageSwitcher from './LanguageSwitcher.vue'
import ThemeToggle from './ThemeToggle.vue'

const { t } = useI18n()
const route = useRoute()
const authStore = useAuthStore()
const notificationsStore = useNotificationsStore()

// تعريف emits
const emit = defineEmits(['toggle-sidebar', 'search'])

// تعريف خصائص المكون
defineProps({
  showSearch: {
    type: Boolean,
    default: false
  }
})

// عنوان الصفحة حسب المسار
const pageTitle = computed(() => {
  const titles = {
    '/admin/dashboard': t('admin_dashboard'),
    '/supervisor/dashboard': t('supervisor_dashboard'),
    '/student/dashboard': t('student_dashboard'),
    '/company/dashboard': t('company_dashboard')
  }
  return titles[route.path] || t('dashboard')
})

const pageSubtitle = computed(() => {
  const subtitles = {
    '/admin/dashboard': t('manage_text'),
    '/supervisor/dashboard': t('monitor_students'),
    '/student/dashboard': t('manage_training'),
    '/company/dashboard': t('manage_training')
  }
  return subtitles[route.path] || ''
})

// أيقونة الصفحة
const pageIcon = computed(() => {
  const icons = {
    '/admin/dashboard': { icon: 'bi bi-speedometer2', background: 'var(--accent)' },
    '/supervisor/dashboard': { icon: 'bi bi-grid', background: 'var(--primary-purple)' },
    '/student/dashboard': { icon: 'bi bi-mortarboard-fill', background: 'var(--accent)' },
    '/company/dashboard': { icon: 'bi bi-building', background: '#10b981' }
  }
  return icons[route.path] || { icon: 'bi bi-house', background: 'var(--accent)' }
})

// بيانات المستخدم
const userAvatar = computed(() => authStore.user?.avatar)
const unreadCount = computed(() => notificationsStore.unreadCount)
const recentNotifications = computed(() => notificationsStore.recentNotifications)
</script>

<style scoped>
.page-header {
  background: var(--header-bg);
  border-bottom: 1px solid var(--border-color);
  padding: 15px 0;
  position: sticky;
  top: 0;
  z-index: 1000;
  backdrop-filter: blur(10px);
  background: rgba(var(--header-bg-rgb, 255, 255, 255), 0.95);
}

.menu-toggle {
  width: 40px;
  height: 40px;
  background: var(--accent-soft);
  border: none;
  border-radius: 10px;
  color: var(--accent);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  cursor: pointer;
}

.icon-box {
  width: 45px;
  height: 45px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.2rem;
}

.search-wrapper {
  position: relative;
  width: 300px;
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
  border-radius: 12px;
  background: var(--input-bg);
  color: var(--text-dark);
}

.btn-notification {
  width: 40px;
  height: 40px;
  border: 1px solid var(--border-color);
  border-radius: 10px;
  background: var(--card-bg);
  color: var(--text-muted);
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.notification-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #ef4444;
  color: white;
  font-size: 10px;
  font-weight: 700;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.notification-dropdown {
  width: 350px;
  padding: 0;
  border: 1px solid var(--border-color);
  border-radius: 16px;
  background: var(--card-bg);
}

.notification-header {
  padding: 15px 20px;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.notification-list {
  max-height: 400px;
  overflow-y: auto;
}

.user-avatar {
  display: block;
  line-height: 0;
}

@media (max-width: 768px) {
  .page-header {
    padding: 10px 0;
  }

  .search-wrapper {
    display: none;
  }
}
</style>
