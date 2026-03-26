<template>
  <div class="admin-companies-page">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-bold mb-1">{{ t('manage_companies') }}</h2>
        <p class="text-muted mb-0">{{ t('view_manage_companies') }}</p>
      </div>
      <button class="btn btn-primary" @click="openAddCompanyModal">
        <i class="bi bi-building-add me-2"></i>
        {{ t('add_company') }}
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-row mb-5">
      <div class="row g-4">
        <div class="col-md-3" v-for="stat in companyStats" :key="stat.key">
          <div class="stat-card">
            <span class="stat-label">{{ t(stat.label) }}</span>
            <span class="stat-number">{{ stat.value }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Companies Table -->
    <div class="table-container">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>{{ t('company') }}</th>
              <th>{{ t('contact') }}</th>
              <th>{{ t('industry') }}</th>
              <th>{{ t('programs') }}</th>
              <th>{{ t('status') }}</th>
              <th>{{ t('actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="company in companies" :key="company.id">
              <td>
                <div class="company-cell">
                  <div class="company-icon">
                    <i class="bi bi-building"></i>
                  </div>
                  <span class="fw-bold">{{ company.name }}</span>
                </div>
              </td>
              <td class="small">
                <div>{{ company.email }}</div>
                <div class="text-muted">{{ company.phone }}</div>
              </td>
              <td>{{ company.industry }}</td>
              <td>
                <a href="#" class="text-decoration-none">{{ company.programs }}</a>
              </td>
              <td>
                <span class="status-badge" :class="company.status === 'Approved' ? 'badge-approved' : 'badge-pending'">
                  {{ t(company.status.toLowerCase()) }}
                </span>
              </td>
              <td>
                <div class="action-buttons">
                  <button class="btn-circle" @click="viewCompany(company)" title="View">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button 
                    v-if="company.status === 'Pending'" 
                    class="btn-circle btn-success-solid" 
                    @click="approveCompany(company)"
                    title="Approve"
                  >
                    <i class="bi bi-check-lg"></i>
                  </button>
                  <button 
                    class="btn-circle btn-danger-solid" 
                    @click="deleteCompany(company)"
                    title="Delete"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add Company Modal -->
    <Teleport to="body">
      <div v-if="showCompanyModal" class="modal-overlay" @click.self="closeCompanyModal">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="fw-bold mb-0">{{ editingCompany ? t('edit_company') : t('add_company') }}</h5>
            <button class="btn-close" @click="closeCompanyModal">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveCompany">
              <div class="mb-3">
                <label class="form-label fw-bold">{{ t('company_name') }}</label>
                <input type="text" class="form-control" v-model="companyForm.name" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">{{ t('contact_email') }}</label>
                <input type="email" class="form-control" v-model="companyForm.email" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">{{ t('phone') }}</label>
                <input type="text" class="form-control" v-model="companyForm.phone" required>
              </div>
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold">{{ t('industry') }}</label>
                  <input type="text" class="form-control" v-model="companyForm.industry" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">{{ t('programs') }}</label>
                  <input type="number" class="form-control" v-model="companyForm.programs" required>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">{{ t('status') }}</label>
                <select class="form-select" v-model="companyForm.status" required>
                  <option value="Approved">{{ t('approved') }}</option>
                  <option value="Pending">{{ t('pending') }}</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary w-100" :disabled="isSaving">
                <span v-if="!isSaving">{{ editingCompany ? t('update') : t('create') }}</span>
                <span v-else><span class="spinner-border spinner-border-sm me-2"></span>{{ t('saving') }}</span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from '@/composables/useI18n'

const { t } = useI18n()

// Stats
const companyStats = ref([
  { key: 'total', label: 'total_companies', value: '8' },
  { key: 'approved', label: 'approved', value: '5' },
  { key: 'pending', label: 'pending', value: '3' },
  { key: 'programs', label: 'total_programs', value: '24' }
])

// Companies data
const companies = ref([
  {
    id: 1,
    name: 'TechCorp Inc.',
    email: 'contact@techcorp.com',
    phone: '+1 (555) 123-4567',
    industry: 'Technology',
    programs: 5,
    status: 'Approved'
  },
  {
    id: 2,
    name: 'DataScience Pro',
    email: 'info@datascipro.com',
    phone: '+1 (555) 234-5678',
    industry: 'Data Analytics',
    programs: 3,
    status: 'Pending'
  },
  {
    id: 3,
    name: 'CloudScale Solutions',
    email: 'hello@cloudscale.io',
    phone: '+1 (555) 345-6789',
    industry: 'Cloud Computing',
    programs: 4,
    status: 'Approved'
  }
])

// Modal state
const showCompanyModal = ref(false)
const editingCompany = ref(null)
const isSaving = ref(false)
const companyForm = ref({
  name: '',
  email: '',
  phone: '',
  industry: '',
  programs: 0,
  status: 'Pending'
})

const openAddCompanyModal = () => {
  editingCompany.value = null
  companyForm.value = { name: '', email: '', phone: '', industry: '', programs: 0, status: 'Pending' }
  showCompanyModal.value = true
}

const editCompany = (company) => {
  editingCompany.value = company
  companyForm.value = { ...company }
  showCompanyModal.value = true
}

const closeCompanyModal = () => {
  showCompanyModal.value = false
  editingCompany.value = null
}

const saveCompany = async () => {
  isSaving.value = true
  try {
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    if (editingCompany.value) {
      // Update existing company
      const index = companies.value.findIndex(c => c.id === editingCompany.value.id)
      companies.value[index] = { ...editingCompany.value, ...companyForm.value }
    } else {
      // Add new company
      const newCompany = {
        id: companies.value.length + 1,
        ...companyForm.value
      }
      companies.value.push(newCompany)
    }
    
    closeCompanyModal()
  } finally {
    isSaving.value = false
  }
}

// Actions
const viewCompany = (company) => {
  alert(t('viewing_company', { name: company.name }))
}

const approveCompany = (company) => {
  if (confirm(t('confirm_approve_company'))) {
    company.status = 'Approved'
  }
}

const deleteCompany = (company) => {
  if (confirm(t('confirm_delete_company'))) {
    companies.value = companies.value.filter(c => c.id !== company.id)
  }
}
</script>

<style scoped>
.admin-companies-page {
  padding: 20px 0;
}

.stat-card {
  background: var(--card-bg);
  border-radius: 20px;
  padding: 25px;
  border: 1px solid var(--border-color);
}

.stat-label {
  font-size: 14px;
  color: var(--text-muted);
  display: block;
  margin-bottom: 5px;
}

.stat-number {
  font-size: 32px;
  font-weight: 600;
  display: block;
  color: var(--text-dark);
}

.table-container {
  background: var(--card-bg);
  border-radius: 20px;
  padding: 20px;
  border: 1px solid var(--border-color);
}

.company-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.company-icon {
  width: 38px;
  height: 38px;
  background: var(--accent);
  color: white;
  border-radius: 10px;
  display: grid;
  place-items: center;
}

.status-badge {
  padding: 6px 14px;
  border-radius: 30px;
  font-size: 12px;
  font-weight: 500;
}

.badge-approved {
  background: #f0fdf4;
  color: #166534;
}

.badge-pending {
  background: #fffbeb;
  color: #9a3412;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.btn-circle {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  border: 1px solid var(--border-color);
  background: var(--card-bg);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: var(--text-dark);
  transition: all 0.2s ease;
}

.btn-circle:hover {
  background: var(--accent-soft);
  color: var(--accent);
  border-color: var(--accent);
}

.btn-success-solid {
  background: #22c55e;
  color: white;
  border: none;
}

.btn-success-solid:hover {
  background: #16a34a;
  color: white;
}

.btn-danger-solid {
  background: #ef4444;
  color: white;
  border: none;
}

.btn-danger-solid:hover {
  background: #dc2626;
  color: white;
}

/* Modal */
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
  border-radius: 20px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
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
}
</style>