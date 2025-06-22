# TaskMate Frontend

A modern, responsive Vue.js frontend for the TaskMate task management application with JWT authentication and real-time task management.

## Features

- **Modern UI/UX**: Beautiful, responsive design with smooth animations
- **JWT Authentication**: Secure login/registration with token-based authentication
- **Task Management**: Full CRUD operations for tasks
- **Task Statistics**: Real-time dashboard with completion rates and analytics
- **User Profile**: Profile management and settings
- **Task Filtering**: Filter tasks by status (All, Pending, Completed)
- **Priority Levels**: Set task priorities (Low, Medium, High)
- **Due Dates**: Set and track task due dates
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile devices

## Technologies Used

- **Vue 3**: Progressive JavaScript framework with Composition API
- **Vue Router**: Official router for Vue.js
- **Axios**: HTTP client for API communication
- **Vite**: Fast build tool and development server
- **CSS3**: Modern styling with Flexbox and Grid

## Screenshots

### Login/Register Page
- Clean authentication form with toggle between login and register
- Form validation and error handling
- Responsive design for all devices

### Task Dashboard
- Statistics cards showing total, completed, pending tasks and completion rate
- Add task form with title, description, priority, and due date
- Task filtering by status
- Modern task cards with priority badges and action buttons

### User Profile
- Modal-based profile management
- Update name and email
- Secure password change functionality

## Setup Instructions

### Prerequisites
- Node.js 16+ and npm
- Backend API running (see backend README)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd frontend
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Configure API endpoint**
   Edit the `baseURL` in `src/services/ApiService.js` if your backend is running on a different port:
   ```javascript
   const api = axios.create({
     baseURL: 'http://localhost:8000', // Change this if needed
     headers: {
       'Content-Type': 'application/json'
     }
   });
   ```

4. **Start development server**
   ```bash
   npm run dev
   ```

5. **Open in browser**
   Navigate to `http://localhost:5173` (or the URL shown in terminal)

### Build for Production

```bash
npm run build
```

The built files will be in the `dist` directory.

## Project Structure

```
frontend/
├── public/
│   └── vite.svg
├── src/
│   ├── assets/
│   │   └── vue.svg
│   ├── components/
│   │   └── HelloWorld.vue
│   ├── services/
│   │   └── ApiService.js          # API communication layer
│   ├── views/
│   │   ├── Login.vue              # Authentication page
│   │   └── TaskList.vue           # Main task dashboard
│   ├── App.vue                    # Root component
│   ├── main.js                    # Application entry point
│   ├── router.js                  # Vue Router configuration
│   └── style.css                  # Global styles
├── index.html                     # HTML template
├── package.json                   # Dependencies and scripts
├── vite.config.js                 # Vite configuration
└── README.md                      # This file
```

## API Integration

The frontend communicates with the backend through the `ApiService.js` module, which provides:

### Authentication
- `register(name, email, password)` - User registration
- `login(email, password)` - User login
- `logout()` - User logout

### User Management
- `getProfile()` - Get user profile
- `updateProfile(name, email)` - Update user profile
- `changePassword(currentPassword, newPassword)` - Change password

### Task Management
- `getTasks()` - Get all tasks for user
- `getTask(id)` - Get specific task
- `createTask(taskData)` - Create new task
- `updateTask(id, taskData)` - Update task
- `deleteTask(id)` - Delete task
- `toggleTask(id)` - Toggle task completion