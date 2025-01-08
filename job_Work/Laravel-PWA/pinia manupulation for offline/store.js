import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useQuasar } from 'quasar'; // Assuming you use Quasar for UI/notifications

export const useTaskStore = defineStore('task', () => {
  const $q = useQuasar();
  // State
  const tasks = ref([]);
  const isOnline = ref(navigator.onLine);
  const pendingActions = ref(JSON.parse(localStorage.getItem('pendingActions') || '[]'));

