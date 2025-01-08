import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useQuasar } from 'quasar'; // Assuming you use Quasar for UI/notifications

export const useTaskStore = defineStore('task', () => {
  const $q = useQuasar();
  // State
  const tasks = ref([]);
  const isOnline = ref(navigator.onLine);
  const pendingActions = ref(JSON.parse(localStorage.getItem('pendingActions') || '[]'));

  // Getters
  const completedTasks = computed(() => tasks.value.filter(t => t.is_completed));
  const pendingTasks = computed(() => tasks.value.filter(t => !t.is_completed));

  // Actions - Main Logic
  const fetchTasks = async () => {
    try {
      const response = await fetch('/api/tasks');
      if (!response.ok) throw new Error('Network response was not ok');
      tasks.value = await response.json();
    } catch (error) {
      console.error('Failed to fetch tasks:', error);
      $q.notify({ type: 'negative', message: 'Could not fetch tasks. You are offline.' });
    }
  };