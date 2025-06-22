import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000',
  headers: {
    'Content-Type': 'application/json'
  }
});

// Add request interceptor to include token in all requests
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Add response interceptor to handle token expiration
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// Authentication endpoints
export function register(name, email, password) {
  return api.post('/register', { name, email, password });
}

export function login(email, password) {
  return api.post('/login', { email, password });
}

// User management endpoints
export function getProfile() {
  return api.get('/api/profile');
}

export function updateProfile(name, email) {
  return api.put('/api/profile', { name, email });
}

export function changePassword(currentPassword, newPassword) {
  return api.put('/api/change-password', { current_password: currentPassword, new_password: newPassword });
}

// Task management endpoints
export function getTasks() {
  return api.get('/api/tasks');
}

export function getTask(id) {
  return api.get(`/api/tasks/${id}`);
}

export function createTask(taskData) {
  return api.post('/api/tasks', taskData);
}

export function updateTask(id, taskData) {
  return api.put(`/api/tasks/${id}`, taskData);
}

export function deleteTask(id) {
  return api.delete(`/api/tasks/${id}`);
}

export function toggleTask(id) {
  return api.patch(`/api/tasks/${id}/toggle`);
}

export function getTaskStats() {
  return api.get('/api/tasks/stats');
}

// Utility functions
export function setAuthToken(token) {
  if (token) {
    localStorage.setItem('token', token);
  } else {
    localStorage.removeItem('token');
  }
}

export function getAuthToken() {
  return localStorage.getItem('token');
}

export function isAuthenticated() {
  return !!localStorage.getItem('token');
}

export function logout() {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
}
