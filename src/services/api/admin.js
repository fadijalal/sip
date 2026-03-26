import api from './api'

export const adminAPI = {
  getDashboard: () => api.get('/admin/dashboard'),
  getUsers: (params) => api.get('/admin/users', { params }),
  getUser: (id) => api.get(`/admin/users/${id}`),
  createUser: (data) => api.post('/admin/users', data),
  updateUser: (id, data) => api.put(`/admin/users/${id}`, data),
  deleteUser: (id) => api.delete(`/admin/users/${id}`),
  toggleUserStatus: (id) => api.post(`/admin/users/${id}/toggle-status`),
  getCompanies: (params) => api.get('/admin/companies', { params }),
  getCompany: (id) => api.get(`/admin/companies/${id}`),
  createCompany: (data) => api.post('/admin/companies', data),
  updateCompany: (id, data) => api.put(`/admin/companies/${id}`, data),
  deleteCompany: (id) => api.delete(`/admin/companies/${id}`),
  verifyCompany: (id) => api.post(`/admin/companies/${id}/verify`),
  unverifyCompany: (id) => api.post(`/admin/companies/${id}/unverify`),
  suspendCompany: (id) => api.post(`/admin/companies/${id}/suspend`),
  activateCompany: (id) => api.post(`/admin/companies/${id}/activate`),
  getCompanyStats: () => api.get('/admin/companies/stats/all'),
  getReports: () => api.get('/admin/reports'),
  generateReport: (data) => api.post('/admin/reports', data),
  dismissAlert: (alertId) => api.post(`/admin/alerts/${alertId}/dismiss`),
}