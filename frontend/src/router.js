import { createRouter, createWebHistory } from 'vue-router';
import { isAuthenticated } from './services/ApiService';
import Login from './views/Login.vue';
import TaskList from './views/TaskList.vue';

const routes = [
  { 
    path: '/', 
    redirect: '/tasks' 
  },
  { 
    path: '/login', 
    component: Login,
    meta: { requiresGuest: true }
  },
  { 
    path: '/tasks', 
    component: TaskList,
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Navigation guards
router.beforeEach((to, from, next) => {
  const authenticated = isAuthenticated();
  
  // If route requires authentication and user is not authenticated
  if (to.meta.requiresAuth && !authenticated) {
    next('/login');
    return;
  }
  
  // If route requires guest (login/register) and user is authenticated
  if (to.meta.requiresGuest && authenticated) {
    next('/tasks');
    return;
  }
  
  next();
});

export default router;
