import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000',
  headers: {
    'Content-Type': 'application/json'
  }
});

export function login(email, password) {
  return api.post('/login', { email, password });
}

export function getTasks(token) {
  return api.get('/api/tasks', {
    headers: { Authorization: `Bearer ${token}` }
  });
}

export function createTask(token, title) {
  return api.post('/api/tasks', { title }, {
    headers: { Authorization: `Bearer ${token}` }
  });
}
