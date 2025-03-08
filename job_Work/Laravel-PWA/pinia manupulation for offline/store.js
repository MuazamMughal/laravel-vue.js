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

  const addTask = async (taskData) => {
    const newTask = { ...taskData, id: Date.now(), _local: true }; // Temp local ID

    // Optimistic UI update
    tasks.value.unshift(newTask);

    if (isOnline.value) {
      try {
        await syncAddTask(taskData);
      } catch (error) {
        // If online but request fails, add to pending queue
        queueAction('add', taskData);
      }
    } else {
      // If offline, add to pending queue
      queueAction('add', taskData);
    }
  };

  const syncAddTask = async (taskData) => {
    const response = await fetch('/api/tasks', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(taskData),
    });
    const savedTask = await response.json();
    // Replace the local task with the server task
    const index = tasks.value.findIndex(t => t._local && t.title === taskData.title);
    if (index !== -1) {
      tasks.value.splice(index, 1, savedTask);
    }
  };

  const queueAction = (type, payload) => {
    const action = { type, payload, timestamp: new Date().toISOString() };
    pendingActions.value.push(action);
    localStorage.setItem('pendingActions', JSON.stringify(pendingActions.value));
    $q.notify({ type: 'info', message: 'Action queued for sync.', timeout: 1000 });
  };

  const syncPendingActions = async () => {
    const actionsToProcess = [...pendingActions.value];
    if (actionsToProcess.length === 0) return;

    $q.notify({ type: 'ongoing', message: 'Syncing offline actions...' });

    for (const action of actionsToProcess) {
      try {
        switch (action.type) {
          case 'add':
            await syncAddTask(action.payload);
            break;
          // Cases for 'update' and 'delete' would go here
        }
        // Remove the action from the queue on success
        const index = pendingActions.value.findIndex(a => a.timestamp === action.timestamp);
        if (index !== -1) {
          pendingActions.value.splice(index, 1);
        }
      } catch (error) {
        console.error(`Failed to sync action ${action.type}:`, error);
        break; // Stop the loop if one action fails
      }
    }
    localStorage.setItem('pendingActions', JSON.stringify(pendingActions.value));
    $q.notify({ type: 'positive', message: 'Offline sync complete!', timeout: 1000 });
  };
