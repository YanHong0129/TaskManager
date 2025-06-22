<template>
  <div class="task-app">
    <!-- Header -->
    <header class="header">
      <div class="header-content">
        <h1>TaskMate</h1>
        <div class="user-menu">
          <span class="user-name">{{ user?.name || 'User' }}</span>
          <button @click="showProfile = true" class="profile-btn">Profile</button>
          <button @click="handleLogout" class="logout-btn">Logout</button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Statistics Dashboard -->
      <div class="stats-dashboard">
        <div class="stat-card">
          <h3>Total Tasks</h3>
          <p class="stat-number">{{ stats.total }}</p>
        </div>
        <div class="stat-card">
          <h3>Completed</h3>
          <p class="stat-number completed">{{ stats.completed }}</p>
        </div>
        <div class="stat-card">
          <h3>Pending</h3>
          <p class="stat-number pending">{{ stats.pending }}</p>
        </div>
        <div class="stat-card">
          <h3>Completion Rate</h3>
          <p class="stat-number">{{ stats.completion_rate }}%</p>
        </div>
      </div>

      <!-- Add Task Form -->
      <div class="add-task-section">
        <form @submit.prevent="addTask" class="add-task-form">
          <input 
            v-model="newTask.title" 
            placeholder="Task title" 
            required
            class="task-input"
          />
          <textarea 
            v-model="newTask.description" 
            placeholder="Task description (optional)"
            class="task-textarea"
          ></textarea>
          <div class="task-options">
            <select v-model="newTask.priority" class="priority-select">
              <option value="low">Low Priority</option>
              <option value="medium">Medium Priority</option>
              <option value="high">High Priority</option>
            </select>
            <input 
              v-model="newTask.due_date" 
              type="datetime-local" 
              class="due-date-input"
            />
          </div>
          <button type="submit" :disabled="loading" class="add-btn">
            {{ loading ? 'Adding...' : 'Add Task' }}
          </button>
        </form>
      </div>

      <!-- Task List -->
      <div class="task-list-section">
        <div class="task-filters">
          <button 
            @click="filter = 'all'" 
            :class="{ active: filter === 'all' }"
            class="filter-btn"
          >
            All
          </button>
          <button 
            @click="filter = 'pending'" 
            :class="{ active: filter === 'pending' }"
            class="filter-btn"
          >
            Pending
          </button>
          <button 
            @click="filter = 'completed'" 
            :class="{ active: filter === 'completed' }"
            class="filter-btn"
          >
            Completed
          </button>
        </div>

        <div class="task-list">
          <div 
            v-for="task in filteredTasks" 
            :key="task.id" 
            class="task-item"
            :class="{ completed: task.completed }"
          >
            <div class="task-content">
              <div class="task-header">
                <h3 class="task-title">{{ task.title }}</h3>
                <span :class="`priority-badge ${task.priority}`">
                  {{ task.priority }}
                </span>
              </div>
              <p v-if="task.description" class="task-description">
                {{ task.description }}
              </p>
              <div class="task-meta">
                <span v-if="task.due_date" class="due-date">
                  Due: {{ formatDate(task.due_date) }}
                </span>
                <span class="created-date">
                  Created: {{ formatDate(task.created_at) }}
                </span>
              </div>
            </div>
            <div class="task-actions">
              <button 
                @click="handleToggleTask(task.id)" 
                :class="`toggle-btn ${task.completed ? 'completed' : ''}`"
                :title="task.completed ? 'Mark as pending' : 'Mark as completed'"
              >
                {{ task.completed ? '✓' : '○' }}
              </button>
              <button 
                @click="openEditModal(task)" 
                class="edit-btn"
                title="Edit task"
              >
                ✎
              </button>
              <button 
                @click="handleDeleteTask(task.id)" 
                class="delete-btn"
                title="Delete task"
              >
                ×
              </button>
            </div>
          </div>
        </div>

        <div v-if="filteredTasks.length === 0" class="empty-state">
          <p>{{ filter === 'all' ? 'No tasks yet. Create your first task!' : `No ${filter} tasks.` }}</p>
        </div>
      </div>
    </main>

    <!-- Profile Modal -->
    <div v-if="showProfile" class="modal-overlay" @click="showProfile = false">
      <div class="modal" @click.stop>
        <h2>Profile</h2>
        <form @submit.prevent="updateProfile" class="profile-form">
          <div class="form-group">
            <label>Name</label>
            <input v-model="profileForm.name" type="text" required />
          </div>
          <div class="form-group">
            <label>Email</label>
            <input v-model="profileForm.email" type="email" required />
          </div>
          <div class="form-actions">
            <button type="submit" :disabled="profileLoading" class="save-btn">
              {{ profileLoading ? 'Saving...' : 'Save Changes' }}
            </button>
            <button type="button" @click="showProfile = false" class="cancel-btn">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Task Modal -->
    <div v-if="showEditModal" class="modal-overlay" @click="showEditModal = false">
      <div class="modal" @click.stop>
        <h2>Edit Task</h2>
        <form v-if="editingTask" @submit.prevent="handleUpdateTask" class="profile-form">
          <div class="form-group">
            <label>Title</label>
            <input v-model="editingTask.title" type="text" required />
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea v-model="editingTask.description" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Priority</label>
            <select v-model="editingTask.priority">
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
          </div>
          <div class="form-group">
            <label>Due Date</label>
            <input v-model="editingTask.due_date" type="datetime-local" />
          </div>
          <div class="form-actions">
            <button type="submit" :disabled="loading" class="save-btn">
              {{ loading ? 'Saving...' : 'Save Changes' }}
            </button>
            <button type="button" @click="showEditModal = false" class="cancel-btn">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Error Toast -->
    <div v-if="error" class="error-toast">
      {{ error }}
      <button @click="error = ''" class="close-btn">×</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { 
  getTasks, 
  createTask, 
  deleteTask as deleteTaskApi, 
  updateTask,
  toggleTask as toggleTaskApi, 
  getTaskStats,
  getProfile,
  updateProfile as updateProfileApi,
  logout
} from '../services/ApiService';

const router = useRouter();
const tasks = ref([]);
const stats = ref({ total: 0, completed: 0, pending: 0, completion_rate: 0 });
const user = ref(null);
const loading = ref(false);
const profileLoading = ref(false);
const error = ref('');
const showProfile = ref(false);
const showEditModal = ref(false);
const editingTask = ref(null);
const filter = ref('all');

const newTask = ref({
  title: '',
  description: '',
  priority: 'medium',
  due_date: ''
});

const profileForm = ref({
  name: '',
  email: ''
});

const filteredTasks = computed(() => {
  switch (filter.value) {
    case 'completed':
      return tasks.value.filter(task => task.completed);
    case 'pending':
      return tasks.value.filter(task => !task.completed);
    default:
      return tasks.value;
  }
});

const fetchTasks = async () => {
  try {
    const response = await getTasks();
    if (response.data.success) {
      tasks.value = response.data.data;
    }
  } catch (err) {
    error.value = 'Failed to fetch tasks';
  }
};

const fetchStats = async () => {
  try {
    const response = await getTaskStats();
    if (response.data.success) {
      stats.value = response.data.data;
    }
  } catch (err) {
    console.error('Failed to fetch stats:', err);
  }
};

const fetchProfile = async () => {
  try {
    const response = await getProfile();
    if (response.data.success) {
      user.value = response.data.data;
      profileForm.value = {
        name: user.value.name,
        email: user.value.email
      };
    }
  } catch (err) {
    error.value = 'Failed to fetch profile';
  }
};

const addTask = async () => {
  if (!newTask.value.title.trim()) return;
  
  loading.value = true;
  try {
    const response = await createTask(newTask.value);
    if (response.data.success) {
      newTask.value = { title: '', description: '', priority: 'medium', due_date: '' };
      await fetchTasks();
      await fetchStats();
    }
  } catch (err) {
    error.value = err.response?.data?.error || 'Failed to create task';
  } finally {
    loading.value = false;
  }
};

const handleDeleteTask = async (id) => {
  if (!confirm('Are you sure you want to delete this task?')) return;
  
  try {
    const response = await deleteTaskApi(id);
    if (response.data.success) {
      await fetchTasks();
      await fetchStats();
    }
  } catch (err) {
    error.value = 'Failed to delete task';
  }
};

const handleToggleTask = async (id) => {
  try {
    const response = await toggleTaskApi(id);
    if (response.data.success) {
      await fetchTasks();
      await fetchStats();
    }
  } catch (err) {
    error.value = 'Failed to toggle task';
  }
};

const openEditModal = (task) => {
  editingTask.value = { ...task };
  if (editingTask.value.due_date) {
    editingTask.value.due_date = new Date(editingTask.value.due_date).toISOString().slice(0, 16);
  }
  showEditModal.value = true;
};

const handleUpdateTask = async () => {
  if (!editingTask.value) return;

  loading.value = true;
  try {
    const response = await updateTask(editingTask.value.id, editingTask.value);
    if (response.data.success) {
      showEditModal.value = false;
      await fetchTasks();
    } else {
      error.value = response.data.error || 'Failed to update task.';
    }
  } catch (err) {
    error.value = err.response?.data?.error || 'Failed to update task.';
  } finally {
    loading.value = false;
  }
};

const updateProfile = async () => {
  profileLoading.value = true;
  try {
    const response = await updateProfileApi(profileForm.value.name, profileForm.value.email);
    if (response.data.success) {
      user.value = response.data.data;
      showProfile.value = false;
      error.value = '';
    }
  } catch (err) {
    error.value = err.response?.data?.error || 'Failed to update profile';
  } finally {
    profileLoading.value = false;
  }
};

const handleLogout = () => {
  logout();
  router.push('/login');
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

onMounted(async () => {
  await Promise.all([fetchTasks(), fetchStats(), fetchProfile()]);
});
</script>

<style scoped>
.task-app {
  min-height: 100vh;
  background: #f5f7fa;
}

.header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 1rem 0;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header h1 {
  margin: 0;
  font-size: 2rem;
  font-weight: 700;
}

.user-menu {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-name {
  font-weight: 500;
}

.profile-btn, .logout-btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s ease;
}

.profile-btn {
  background: rgba(255, 255, 255, 0.2);
  color: white;
}

.logout-btn {
  background: rgba(255, 255, 255, 0.1);
  color: white;
}

.profile-btn:hover, .logout-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

.main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.stats-dashboard {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  text-align: center;
}

.stat-card h3 {
  margin: 0 0 0.5rem 0;
  color: #666;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-number {
  margin: 0;
  font-size: 2rem;
  font-weight: 700;
  color: #333;
}

.stat-number.completed {
  color: #10b981;
}

.stat-number.pending {
  color: #f59e0b;
}

.add-task-section {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  margin-bottom: 2rem;
}

.add-task-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.task-input, .task-textarea {
  padding: 0.75rem;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s ease;
}

.task-input:focus, .task-textarea:focus {
  outline: none;
  border-color: #667eea;
}

.task-textarea {
  resize: vertical;
  min-height: 80px;
}

.task-options {
  display: flex;
  gap: 1rem;
}

.priority-select, .due-date-input {
  padding: 0.75rem;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 1rem;
}

.add-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s ease;
}

.add-btn:hover:not(:disabled) {
  transform: translateY(-2px);
}

.add-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.task-list-section {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.task-filters {
  display: flex;
  border-bottom: 1px solid #e1e5e9;
}

.filter-btn {
  flex: 1;
  padding: 1rem;
  border: none;
  background: none;
  cursor: pointer;
  font-weight: 500;
  color: #666;
  transition: all 0.2s ease;
}

.filter-btn.active {
  color: #667eea;
  border-bottom: 2px solid #667eea;
}

.task-list {
  padding: 1rem;
}

.task-item {
  display: flex;
  align-items: center;
  padding: 1rem;
  border: 1px solid #e1e5e9;
  border-radius: 8px;
  margin-bottom: 1rem;
  transition: all 0.2s ease;
}

.task-item:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.task-item.completed {
  opacity: 0.7;
  background: #f8f9fa;
}

.task-content {
  flex: 1;
}

.task-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 0.5rem;
}

.task-title {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
}

.task-item.completed .task-title {
  text-decoration: line-through;
  color: #666;
}

.priority-badge {
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.priority-badge.low {
  background: #d1fae5;
  color: #065f46;
}

.priority-badge.medium {
  background: #fef3c7;
  color: #92400e;
}

.priority-badge.high {
  background: #fee2e2;
  color: #991b1b;
}

.task-description {
  margin: 0.5rem 0;
  color: #666;
  line-height: 1.5;
}

.task-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.8rem;
  color: #999;
}

.task-actions {
  display: flex;
  gap: 0.5rem;
}

.toggle-btn, .edit-btn, .delete-btn {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.2s ease;
}

.toggle-btn {
  background: #e1e5e9;
  color: #666;
}

.toggle-btn.completed {
  background: #10b981;
  color: white;
}

.edit-btn {
  background: #3b82f6;
  color: white;
}

.delete-btn {
  background: #ef4444;
  color: white;
}

.toggle-btn:hover, .edit-btn:hover, .delete-btn:hover {
  transform: scale(1.1);
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #666;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
}

.modal h2 {
  margin: 0 0 1.5rem 0;
  color: #333;
}

.profile-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 500;
  color: #555;
}

.form-group input {
  padding: 0.75rem;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 1rem;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.save-btn, .cancel-btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
}

.save-btn {
  background: #667eea;
  color: white;
}

.cancel-btn {
  background: #e1e5e9;
  color: #666;
}

.error-toast {
  position: fixed;
  top: 2rem;
  right: 2rem;
  background: #ef4444;
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 1rem;
  z-index: 1001;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.close-btn {
  background: none;
  border: none;
  color: white;
  font-size: 1.2rem;
  cursor: pointer;
}

@media (max-width: 768px) {
  .main-content {
    padding: 1rem;
  }
  
  .stats-dashboard {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .task-options {
    flex-direction: column;
  }
  
  .task-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .task-meta {
    flex-direction: column;
    gap: 0.25rem;
  }
}
</style>
