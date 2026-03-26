<template>
  <div class="admin-users-page">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-bold mb-1">{{ t('manage_users') }}</h2>
        <p class="text-muted mb-0">{{ t('manage_users_desc') }}</p>
      </div>
      <button class="btn btn-primary" @click="openAddUserModal">
        <i class="bi bi-person-plus me-2"></i>
        {{ t('add_user') }}
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-5">
      <div class="col-md-3" v-for="stat in userStats" :key="stat.key">
        <div class="stat-card">
          <h6 class="text-muted mb-2">{{ t(stat.label) }}</h6>
          <h2 class="fw-bold mb-0">{{ stat.value }}</h2>
        </div>
      </div>
    </div>

    <!-- Search and Filter -->
    <div class="search-section mb-4">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="search-wrapper">
            <i class="bi bi-search"></i>
            <input 
              type="text" 
              class="form-control" 
              :placeholder="t('search_users')"
              v-model="searchQuery"
            />
          </div>
        </div>
        <div class="col-md-3">
          <select class="form-select" v-model="roleFilter">
            <option value="all">{{ t('all_roles') }}</option>
            <option value="supervisor">{{ t('supervisor') }}</option>
            <option value="student">{{ t('student') }}</option>
          </select>
        </div>
        <div class="col-md-3">
          <select class="form-select" v-model="statusFilter">
            <option value="all">{{ t('all_status') }}</option>
            <option value="active">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="table-container">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>{{ t('user') }}</th>
              <th>{{ t('role') }}</th>
              <th>{{ t('email') }}</th>
              <th>{{ t('status') }}</th>
              <th>{{ t('joined') }}</th>
              <th>{{ t('actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in filteredUsers" :key="user.id">
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="user-avatar" :style="{ background: user.avatarColor }">
                    {{ user.initials }}
                  </div>
                  <span class="fw-bold">{{ user.name }}</span>
                </div>
              </td>
              <td>
                <span class="badge-role">{{ t(user.role) }}</span>
              </td>
              <td>{{ user.email }}</td>
              <td>
                <span class="badge-status" :class="user.status === 'active' ? 'bg-success' : 'bg-secondary'">
                  {{ t(user.status) }}
                </span>
              </td>
              <td>{{ user.joinedDate }}</td>
              <td>
                <div class="action-buttons">
                  <button class="btn-action" @click="editUser(user)" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-action" @click="toggleUserStatus(user)" :title="user.status === 'active' ? 'Deactivate' : 'Activate'">
                    <i :class="user.status === 'active' ? 'bi bi-pause-circle' : 'bi bi-play-circle'"></i>
                  </button>
                  <button class="btn-action text-danger" @click="deleteUser(user)" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add/Edit User Modal -->
    <Teleport to="body">
      <div v-if="showUserModal" class="modal-overlay" @click.self="closeUserModal">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="fw-bold mb-0">{{ editingUser ? t('edit_user') : t('add_user') }}</h5>
            <button class="btn-close" @click="closeUserModal">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveUser">
              <div class="mb-3">
                <label class="form-label fw-bold">{{ t('full_name') }}</label>
                <input type="text" class="form-control" v-model="userForm.name" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">{{ t('email') }}</label>
                <input type="email" class="form-control" v-model="userForm.email" required>
              </div>
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold">{{ t('role') }}</label>
                  <select class="form-select" v-model="userForm.role" required>
                    <option value="supervisor">{{ t('supervisor') }}</option>
                    <option value="student">{{ t('student') }}</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">{{ t('status') }}</label>
                  <select class="form-select" v-model="userForm.status" required>
                    <option value="active">{{ t('active') }}</option>
                    <option value="inactive">{{ t('inactive') }}</option>
                  </select>
                </div>
              </div>
              <div v-if="!editingUser" class="mb-3">
                <label class="form-label fw-bold">{{ t('password') }}</label>
                <input type="password" class="form-control" v-model="userForm.password" required>
              </div>
              <button type="submit" class="btn btn-primary w-100" :disabled="isSaving">
                <span v-if="!isSaving">{{ editingUser ? t('update') : t('create') }}</span>
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
import { ref, computed, onMounted } from 'vue'
import { useI18n } from '@/composables/useI18n'

const { t } = useI18n()

// Stats
const userStats = ref([
  { key: 'total', label: 'total_users', value: '5' },
  { key: 'supervisors', label: 'supervisors', value: '3' },
  { key: 'students', label: 'students', value: '2' },
  { key: 'active', label: 'active', value: '4' }
])

// Users data
const users = ref([
  {
    id: 1,
    name: 'Dr. Sarah Chen',
    initials: 'SC',
    email: 'sarah.chen@university.edu',
    role: 'supervisor',
    status: 'active',
    joinedDate: '2024-01-15',
    avatarColor: '#7c3aed'
  },
  {
    id: 2,
    name: 'Emma Thompson',
    initials: 'ET',
    email: 'emma.thompson@email.com',
    role: 'student',
    status: 'active',
    joinedDate: '2024-09-15',
    avatarColor: '#10b981'
  },
  {
    id: 3,
    name: 'Prof. Michael Chen',
    initials: 'MC',
    email: 'michael.chen@university.edu',
    role: 'supervisor',
    status: 'inactive',
    joinedDate: '2023-08-20',
    avatarColor: '#f59e0b'
  }
])

// Filters
const searchQuery = ref('')
const roleFilter = ref('all')
const statusFilter = ref('all')

const filteredUsers = computed(() => {
  return users.value.filter(user => {
    const matchesSearch = user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesRole = roleFilter.value === 'all' || user.role === roleFilter.value
    const matchesStatus = statusFilter.value === 'all' || user.status === statusFilter.value
    return matchesSearch && matchesRole && matchesStatus
  })
})

// Modal state
const showUserModal = ref(false)
const editingUser = ref(null)
const isSaving = ref(false)
const userForm = ref({
  name: '',
  email: '',
  role: 'student',
  status: 'active',
  password: ''
})

const openAddUserModal = () => {
  editingUser.value = null
  userForm.value = { name: '', email: '', role: 'student', status: 'active', password: '' }
  showUserModal.value = true
}

const editUser = (user) => {
  editingUser.value = user
  userForm.value = { ...user, password: '' }
  showUserModal.value = true
}

const closeUserModal = () => {
  showUserModal.value = false
  editingUser.value = null
}

const saveUser = async () => {
  isSaving.value = true
  try {
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    if (editingUser.value) {
      // Update existing user
      const index = users.value.findIndex(u => u.id === editingUser.value.id)
      users.value[index] = { ...editingUser.value, ...userForm.value }
    } else {
      // Add new user
      const newUser = {
        id: users.value.length + 1,
        ...userForm.value,
        initials: userForm.value.name.split(' ').map(n => n[0]).join('').toUpperCase(),
        avatarColor: ['#7c3aed', '#10b981', '#f59e0b', '#3b82f6', '#ef4444'][Math.floor(Math.random() * 5)],
        joinedDate: new Date().toISOString().split('T')[0]
      }
      users.value.push(newUser)
    }
    
    closeUserModal()
  } finally {
    isSaving.value = false
  }
}

const toggleUserStatus = (user) => {
  user.status = user.status === 'active' ? 'inactive' : 'active'
}

const deleteUser = (user) => {
  if (confirm(t('confirm_delete_user'))) {
    users.value = users.value.filter(u => u.id !== user.id)
  }
}
</script>

<style scoped>
.admin-users-page {
  padding: 20px 0;
}

.stat-card {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 20px;
  border: 1px solid var(--border-color);
}

.search-wrapper {
  position: relative;
}

.search-wrapper i {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
}

.search-wrapper input {
  padding-left: 45px;
}

.table-container {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 20px;
  border: 1px solid var(--border-color);
}

.user-avatar {
  width: 35px;
  height: 35px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 14px;
}

.badge-role {
  background: #eef2ff;
  color: #4f46e5;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
}

.badge-status {
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  color: white;
}

.action-buttons {
  display: flex;
  gap: 5px;
}

.btn-action {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: 1px solid var(--border-color);
  background: var(--card-bg);
  color: var(--text-muted);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-action:hover {
  background: var(--accent-soft);
  color: var(--accent);
  border-color: var(--accent);
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