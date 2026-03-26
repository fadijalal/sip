import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useNotificationsStore = defineStore('notifications', () => {
  const notifications = ref([])
  const unreadCount = computed(() => notifications.value.filter(n => !n.read).length)
  const recentNotifications = computed(() => notifications.value.slice(0, 5))

  function addNotification(notification) {
    notifications.value.unshift({
      id: Date.now(),
      read: false,
      time: new Date(),
      ...notification
    })
  }

  function markAsRead(id) {
    const notif = notifications.value.find(n => n.id === id)
    if (notif) notif.read = true
  }

  function markAllAsRead() {
    notifications.value.forEach(n => n.read = true)
  }

  function deleteNotification(id) {
    notifications.value = notifications.value.filter(n => n.id !== id)
  }

  return {
    notifications,
    unreadCount,
    recentNotifications,
    addNotification,
    markAsRead,
    markAllAsRead,
    deleteNotification
  }
})