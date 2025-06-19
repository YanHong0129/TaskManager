import { createRouter, createWebHistory } from 'vue-router';
import Login from './views/Login.vue';
import TaskList from './views/TaskList.vue';

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', component: Login },
  { path: '/tasks', component: TaskList }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
