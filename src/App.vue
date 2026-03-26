<template>
  <div id="app" :class="{ 'dark-theme': isDark }">
    <!-- عرض الـ Sidebar فقط إذا كان المستخدم مسجل دخول -->
    <AppSidebar
      v-if="authStore.isAuthenticated"
      ref="sidebarRef"
    />

    <!-- زر القائمة للموبايل (يظهر فقط إذا كان الـ Sidebar مخفياً) -->
    <button
      v-if="authStore.isAuthenticated && windowWidth < 768"
      class="mobile-menu-toggle"
      @click="toggleSidebar"
    >
      <i class="bi bi-list"></i>
    </button>

    <div
      class="main-wrapper"
      :class="{ 'with-sidebar': authStore.isAuthenticated }"
    >
      <!-- الـ Header -->
      <AppHeader
        v-if="authStore.isAuthenticated"
        :showSearch="showSearch"
        @toggle-sidebar="toggleSidebar"
        @search="handleSearch"
      />

      <!-- المحتوى الرئيسي مع Router View -->
      <main class="main-content">
        <router-view v-slot="{ Component }">
          <transition
            name="fade"
            mode="out-in"
            @before-enter="beforeEnter"
            @enter="enter"
            @leave="leave"
          >
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>

    <!-- Toast notifications -->
    <ToastContainer />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useTheme } from '@/composables/useTheme'
import AppSidebar from '@/components/layout/AppSidebar.vue'
import AppHeader from '@/components/layout/AppHeader.vue'
import ToastContainer from '@/components/common/ToastContainer.vue'

const { isDark } = useTheme()
const authStore = useAuthStore()
const router = useRouter()

const sidebarRef = ref(null)
const windowWidth = ref(window.innerWidth)
const showSearch = ref(false)

// التحقق من عرض شريط البحث (يعتمد على المسار)
const handleSearch = (term) => {
  // إرسال حدث البحث إلى الصفحة النشطة
  const event = new CustomEvent('global-search', { detail: term })
  window.dispatchEvent(event)
}

const toggleSidebar = () => {
  if (sidebarRef.value) {
    sidebarRef.value.openSidebar()
  }
}

// تحديث عرض النافذة
const updateWindowWidth = () => {
  windowWidth.value = window.innerWidth
}

// أنيميشن الصفحات
const beforeEnter = (el) => {
  el.style.opacity = 0
  el.style.transform = 'translateY(20px)'
}

const enter = (el, done) => {
  setTimeout(() => {
    el.style.transition = 'all 0.3s ease'
    el.style.opacity = 1
    el.style.transform = 'translateY(0)'
    done()
  }, 50)
}

const leave = (el, done) => {
  el.style.transition = 'all 0.2s ease'
  el.style.opacity = 0
  el.style.transform = 'translateY(20px)'
  setTimeout(done, 200)
}

onMounted(() => {
  window.addEventListener('resize', updateWindowWidth)
})

onUnmounted(() => {
  window.removeEventListener('resize', updateWindowWidth)
})
</script>

<style>
/* استيراد الأنماط الأساسية */
@import '@/assets/styles/variables.css';
@import '@/assets/styles/dark-mode.css';
@import '@/assets/styles/main.css';

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', 'Cairo', sans-serif;
  overflow-x: hidden;
}

#app {
  min-height: 100vh;
  background-color: var(--main-bg);
  color: var(--text-dark);
  transition: all 0.3s ease;
}

.main-wrapper {
  min-height: 100vh;
  transition: margin-left 0.3s ease;
}

.main-wrapper.with-sidebar {
  margin-left: 280px;
}

[dir="rtl"] .main-wrapper.with-sidebar {
  margin-left: 0;
  margin-right: 280px;
}

.main-content {
  padding: 30px;
  min-height: calc(100vh - 80px);
}

.mobile-menu-toggle {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 56px;
  height: 56px;
  background: var(--accent);
  border: none;
  border-radius: 50%;
  color: white;
  font-size: 24px;
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 1060;
  box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4);
  cursor: pointer;
}

/* أنيميشن fade */
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

/* Responsive */
@media (max-width: 768px) {
  .main-wrapper.with-sidebar {
    margin-left: 0 !important;
    margin-right: 0 !important;
  }

  .main-content {
    padding: 20px;
  }

  .mobile-menu-toggle {
    display: flex;
  }
}

@media (max-width: 576px) {
  .main-content {
    padding: 15px;
  }
}
</style>
