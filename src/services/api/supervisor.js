import api from './api'

export const supervisorAPI = {
  getDashboard: () => api.get('/supervisor/dashboard'),
  getStudents: (params) => api.get('/supervisor/students', { params }),
  getStudent: (id) => api.get(`/supervisor/students/${id}`),
  addStudent: (data) => api.post('/supervisor/students', data),
  addNote: (id, note) => api.post(`/supervisor/students/${id}/notes`, { note }),
  getJisrProgress: (id) => api.get(`/supervisor/students/${id}/jisr-progress`),
  getWeeklyTasks: () => api.get('/supervisor/weekly-tasks'),
  getEvaluations: (params) => api.get('/supervisor/evaluations', { params }),
  getEvaluation: (id) => api.get(`/supervisor/evaluations/${id}`),
  createEvaluation: (data) => api.post('/supervisor/evaluations', data),
  updateEvaluation: (id, data) => api.put(`/supervisor/evaluations/${id}`, data),
  deleteEvaluation: (id) => api.delete(`/supervisor/evaluations/${id}`),
  getEvaluationStats: () => api.get('/supervisor/evaluations/stats/all'),
  getInternships: () => api.get('/supervisor/internships'),
  getReports: (params) => api.get('/supervisor/reports', { params }),
  generateReport: (data) => api.post('/supervisor/reports/generate', data),
  createTask: (data) => api.post('/supervisor/tasks', data),
}