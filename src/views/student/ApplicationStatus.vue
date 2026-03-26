<template>
  <div class="application-status-page">
    <!-- Header -->
    <header class="page-header">
      <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
          <div class="header-icon" style="background: var(--gradient)">
            <i class="bi bi-shield-check fs-4"></i>
          </div>
          <div>
            <h5 class="fw-bold mb-0">{{ t('application_status') }}</h5>
            <small class="text-muted">{{ t('track_progress') }}</small>
          </div>
        </div>

        <!-- Theme Toggle -->
        <button class="theme-toggle" @click="toggleTheme">
          <i class="bi" :class="isDark ? 'bi-sun-fill' : 'bi-moon-stars-fill'"></i>
        </button>
      </div>
    </header>

    <div class="container mt-5 pb-5">
      <!-- Status Tabs -->
      <div class="row g-4 mb-5">
        <div class="col-md-4" v-for="app in applications" :key="app.id">
          <div
            class="status-tab"
            :class="{ 'active-approved': app.status === 'approved' }"
            @click="selectedApp = app"
          >
            <div class="d-flex justify-content-between">
              <span class="fw-bold small">{{ app.title }}</span>
              <i :class="getStatusIcon(app.status)" :style="{ color: getStatusColor(app.status) }"></i>
            </div>
            <div class="mt-2">
              <small class="badge" :class="getStatusBadge(app.status)">{{ app.id }}</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Application Card -->
      <div v-if="selectedApp" class="main-app-card">
        <div class="d-flex justify-content-between align-items-start mb-4">
          <div>
            <h3 class="fw-bold mb-1">{{ selectedApp.title }}</h3>
            <span class="text-muted">{{ t('application_id') }}: <span class="text-dark fw-medium">{{ selectedApp.id }}</span></span>
          </div>
          <div class="status-badge-animated" :class="selectedApp.status">
            <i :class="getStatusIcon(selectedApp.status)"></i>
            {{ t(selectedApp.status) }}
          </div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid bg-light rounded-4 p-4 mb-5">
          <div class="row g-4 text-center">
            <div class="col-6 col-md-3" v-for="info in applicationInfo" :key="info.label">
              <i :class="info.icon" class="text-primary d-block mb-2 fs-5"></i>
              <small class="text-muted d-block">{{ t(info.label) }}</small>
              <span class="fw-bold small">{{ info.value }}</span>
            </div>
          </div>
        </div>

        <!-- Documents -->
        <h6 class="fw-bold mb-4">
          <i class="bi bi-file-earmark-arrow-up me-2"></i>{{ t('submitted_documents') }}
        </h6>
        <div class="row">
          <div class="col-12">
            <div v-for="doc in documents" :key="doc.name" class="doc-item">
              <div class="d-flex align-items-center gap-3">
                <i :class="doc.icon" class="fs-4" :style="{ color: doc.color }"></i>
                <div>
                  <p class="mb-0 fw-bold small">{{ t(doc.name) }}</p>
                  <small class="text-muted">{{ doc.format }} • {{ doc.size }}</small>
                </div>
              </div>
              <div class="d-flex align-items-center gap-3">
                <span class="text-success small fw-bold">
                  <i class="bi bi-check2-circle me-1"></i>{{ t('verified') }}
                </span>
                <button class="btn btn-sm btn-light border-0">
                  <i class="bi bi-download"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact Section -->
        <div class="contact-section mt-5">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-center text-md-start">
              <h6 class="fw-bold mb-1">{{ t('need_discuss') }}</h6>
              <p class="text-muted small mb-0">{{ t('review_committee') }}</p>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-white border rounded-3 px-3 small">
                <i class="bi bi-envelope-at me-2 text-primary"></i>{{ t('email_advisor') }}
              </button>
              <button class="btn btn-primary">
                <i class="bi bi-chat-dots-fill me-2"></i>{{ t('send_message') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'  // ✅ أضفنا computed هنا
import { useI18n } from '@/composables/useI18n'
import { useTheme } from '@/composables/useTheme'

const { t } = useI18n()
const { isDark, toggleTheme } = useTheme()

// Selected application
const selectedApp = ref(null)

// Applications data
const applications = ref([
  {
    id: 1,
    title: 'Advanced Web Development & Cloud Architecture',
    appId: 'APP-2024-1001',
    status: 'approved',
    submittedDate: 'Dec 15, 2024',
    reviewedBy: 'Dr. Sarah Johnson',
    decisionDate: 'Dec 20, 2024'
  },
  {
    id: 2,
    title: 'Data Science Fundamentals with Python',
    appId: 'APP-2024-1002',
    status: 'pending',
    submittedDate: 'Dec 18, 2024',
    reviewedBy: 'Pending',
    decisionDate: '-'
  },
  {
    id: 3,
    title: 'AI & Ethics in Modern Technology',
    appId: 'APP-2024-1003',
    status: 'rejected',
    submittedDate: 'Dec 10, 2024',
    reviewedBy: 'Dr. Michael Chen',
    decisionDate: 'Dec 15, 2024'
  }
])

// Application info for selected app
const applicationInfo = computed(() => {
  if (!selectedApp.value) return []
  return [
    { icon: 'bi bi-calendar4-week', label: 'submitted_on', value: selectedApp.value.submittedDate },
    { icon: 'bi bi-person-badge', label: 'reviewed_by', value: selectedApp.value.reviewedBy },
    { icon: 'bi bi-stars', label: 'decision_date', value: selectedApp.value.decisionDate },
    { icon: 'bi bi-shield-check', label: 'verification', value: t('completed') }
  ]
})

// Documents data
const documents = ref([
  { name: 'academic_transcripts', icon: 'bi bi-file-earmark-pdf', color: '#dc2626', format: 'PDF', size: '2.4 MB' },
  { name: 'resume_cv', icon: 'bi bi-person-vcard', color: '#2563eb', format: 'DOCX', size: '1.1 MB' }
])

// Helper functions
const getStatusIcon = (status) => {
  const icons = {
    approved: 'bi bi-check-circle-fill',
    pending: 'bi bi-clock-history',
    rejected: 'bi bi-x-circle'
  }
  return icons[status] || 'bi bi-circle'
}

const getStatusColor = (status) => {
  const colors = {
    approved: '#22c55e',
    pending: '#3b82f6',
    rejected: '#ef4444'
  }
  return colors[status] || '#6b7280'
}

const getStatusBadge = (status) => {
  const badges = {
    approved: 'bg-success text-white',
    pending: 'bg-primary text-white',
    rejected: 'bg-danger text-white'
  }
  return badges[status] || 'bg-secondary text-white'
}

// Set first application as selected on mount
onMounted(() => {
  if (applications.value.length > 0) {
    selectedApp.value = applications.value[0]
  }
})
</script>

<style scoped>
.application-status-page {
  min-height: 100vh;
  background: var(--main-bg);
}

.page-header {
  background: var(--sidebar-bg);
  border-bottom: 1px solid var(--border-color);
  padding: 15px 0;
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-icon {
  width: 45px;
  height: 45px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.theme-toggle {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  border: 1px solid var(--border-color);
  background: var(--card-bg);
  color: var(--text-dark);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.status-tab {
  background: var(--card-bg);
  border: 2px solid var(--border-color);
  border-radius: 20px;
  padding: 18px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.status-tab:hover {
  transform: translateY(-5px);
  border-color: var(--accent);
}

.status-tab.active-approved {
  border-color: #22c55e;
  background: rgba(34, 197, 94, 0.05);
}

.main-app-card {
  background: var(--card-bg);
  border-radius: 30px;
  padding: 35px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.03);
}

.status-badge-animated {
  padding: 10px 20px;
  border-radius: 50px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-weight: 700;
  font-size: 14px;
}

.status-badge-animated.approved {
  background: #dcfce7;
  color: #15803d;
  animation: pulse 2s infinite;
}

.status-badge-animated.pending {
  background: #dbeafe;
  color: #1e40af;
}

.status-badge-animated.rejected {
  background: #fee2e2;
  color: #991b1b;
}

@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
  70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
  100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

.info-grid {
  background: var(--main-bg);
}

.doc-item {
  background: var(--main-bg);
  border-radius: 18px;
  padding: 15px 25px;
  margin-bottom: 12px;
  border: 1px solid var(--border-color);
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: all 0.3s ease;
}

.doc-item:hover {
  background: var(--card-bg);
  border-color: var(--accent);
}

.contact-section {
  background: rgba(124, 58, 237, 0.05);
  border: 1px dashed var(--accent);
  border-radius: 20px;
  padding: 30px;
}

.btn-white {
  background: var(--card-bg);
  color: var(--text-dark);
}

/* Responsive */
@media (max-width: 768px) {
  .main-app-card {
    padding: 20px;
  }

  .doc-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .contact-section .d-flex {
    flex-direction: column;
  }
}
</style>
