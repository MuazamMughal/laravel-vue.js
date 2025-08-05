<template>
    <q-form @submit="onSubmit" class="q-gutter-md">
      <q-input
        filled
        v-model="title"
        label="Task Title *"
        lazy-rules
        :rules="[val => val && val.length > 0 || 'Please type a title']"
      />
      <q-input
        filled
        v-model="description"
        label="Description"
        type="textarea"
      />
      <div>
        <q-btn label="Add Task" type="submit" color="primary"/>
        <q-btn v-if="pendingActionsCount > 0" @click="syncPendingActions" label="Sync Now" color="secondary" class="q-ml-sm"/>
      </div>
    </q-form>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue';
  import { useTaskStore } from '../stores/taskStore';
  
  const taskStore = useTaskStore();
  const title = ref('');
  const description = ref('');
  
  const pendingActionsCount = computed(() => taskStore.pendingActions.length);
  
  const onSubmit = () => {
    if (!title.value) return;
    taskStore.addTask({
      title: title.value,
      description: description.value,
      is_completed: false,
    });
    title.value = '';
    description.value = '';
  };
  </script>