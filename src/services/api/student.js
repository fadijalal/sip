import axios from 'axios'

const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''

const webApi = axios.create({
  baseURL: '',
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': csrf,
    Accept: 'application/json'
  },
  withCredentials: true
})

export const studentAPI = {
  getDashboard: () => webApi.get('/student/dashboard'),
  getPrograms: (params) => webApi.get('/student/programs', { params }),
  getProgram: (id) => webApi.get(`/student/programs/${id}`),
  applyToProgram: (data) => {
    const formData = new FormData()
    formData.append('opportunity_id', data.opportunity_id)
    formData.append('skills', data.skills || '')
    formData.append('motivation', data.motivation || '')
    if (data.cv) {
      formData.append('cv', data.cv)
    }
    return webApi.post('/student/applications/apply', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
  },
  getApplications: () => webApi.get('/student/applications'),
  getWorkspace: () => webApi.get('/student/workspace')
}
