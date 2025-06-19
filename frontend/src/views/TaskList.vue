<template>
  <div>
    <h2>Task List</h2>
    <ul>
      <li v-for="task in tasks" :key="task.id">
        {{ task.title }} ({{ task.completed ? 'Done' : 'Pending' }})
      </li>
    </ul>

    <input v-model="newTask" placeholder="New Task" />
    <button @click="addTask">Add</button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { getTasks, createTask } from '../services/ApiService';

const tasks = ref([]);
const newTask = ref('');
const token = localStorage.getItem('token');

const fetchTasks = async () => {
  try {
    const res = await getTasks(token);
    tasks.value = res.data;
  } catch (err) {
    alert('Unauthorized or failed to fetch tasks');
  }
};

const addTask = async () => {
  if (!newTask.value) return;
  await createTask(token, newTask.value);
  newTask.value = '';
  fetchTasks();
};

onMounted(fetchTasks);
</script>
