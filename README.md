# TaskMate - Full-Stack Task Management Application

A modern, full-stack web application for task management built with **Slim PHP** backend and **Vue.js** frontend, featuring JWT authentication and comprehensive task management capabilities.

## 🚀 Features

### Backend (Slim PHP)
- **JWT Authentication**: Secure token-based authentication
- **RESTful API**: 12+ endpoints for complete task management
- **Database Integration**: Eloquent ORM with MySQL
- **Input Validation**: Comprehensive validation and error handling
- **Security**: Password hashing, SQL injection protection

### Frontend (Vue.js)
- **Modern UI/UX**: Beautiful, responsive design
- **Real-time Dashboard**: Task statistics and analytics
- **Task Management**: Full CRUD operations with filtering
- **User Profile**: Profile management and settings
- **Mobile Responsive**: Works on all devices

## 📋 API Endpoints

### Public Endpoints
1. `POST /register` - User registration
2. `POST /login` - User login

### Protected Endpoints (JWT Required)
3. `GET /api/profile` - Get user profile
4. `PUT /api/profile` - Update user profile
5. `PUT /api/change-password` - Change password
6. `GET /api/tasks` - Get all tasks
7. `GET /api/tasks/{id}` - Get specific task
8. `POST /api/tasks` - Create new task
9. `PUT /api/tasks/{id}` - Update task
10. `DELETE /api/tasks/{id}` - Delete task
11. `PATCH /api/tasks/{id}/toggle` - Toggle task completion
12. `GET /api/tasks/stats` - Get task statistics

## 🛠️ Technology Stack

### Backend
- **PHP 8.0+** - Server-side language
- **Slim Framework 4** - Lightweight PHP framework
- **Eloquent ORM** - Database abstraction layer
- **MySQL/MariaDB** - Database
- **JWT** - JSON Web Token authentication
- **Composer** - Dependency management

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **Vue Router** - Client-side routing
- **Axios** - HTTP client
- **Vite** - Build tool and dev server
- **CSS3** - Modern styling

## 📦 Installation & Setup

### Prerequisites
- PHP 8.0 or higher
- Node.js 16+ and npm
- MySQL/MariaDB
- Composer
- Web server (Apache/Nginx) or PHP built-in server

### Quick Start

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd TaskMate_WT
   ```

2. **Backend Setup**
   ```bash
   cd backend
   composer install
   
   # Create .env file
   cp .env.example .env
   # Edit .env with your database credentials
   
   # Create database
   mysql -u your_username -p -e "CREATE DATABASE taskmate;"
   
   # Run migrations
   mysql -u your_username -p taskmate < database/migrations/create_tables.sql
   
   # Start backend server
   cd public
   php -S localhost:8000
   ```

3. **Frontend Setup**
   ```bash
   cd frontend
   npm install
   npm run dev
   ```

4. **Access the application**
   - Frontend: http://localhost:5173
   - Backend API: http://localhost:8000

## 🗄️ Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Tasks Table
```sql
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    completed BOOLEAN DEFAULT FALSE,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    due_date DATETIME NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## 🔧 Configuration

### Backend Environment Variables
Create a `.env` file in the `backend` directory:
```env
DB_DRIVER=mysql
DB_HOST=localhost
DB_DATABASE=taskmate
DB_USERNAME=your_username
DB_PASSWORD=your_password
JWT_SECRET=your-super-secret-jwt-key-change-this-in-production
APP_ENV=development
APP_DEBUG=true
```

### Frontend Configuration
Edit `frontend/src/services/ApiService.js` to change the API endpoint:
```javascript
const api = axios.create({
  baseURL: 'http://localhost:8000', // Change this if needed
  headers: {
    'Content-Type': 'application/json'
  }
});
```

## 🧪 Testing the API

### Using curl

1. **Register a new user**
   ```bash
   curl -X POST http://localhost:8000/register \
     -H "Content-Type: application/json" \
     -d '{"name":"John Doe","email":"john@example.com","password":"password123"}'
   ```

2. **Login**
   ```bash
   curl -X POST http://localhost:8000/login \
     -H "Content-Type: application/json" \
     -d '{"email":"john@example.com","password":"password123"}'
   ```

3. **Create a task (use token from login response)**
   ```bash
   curl -X POST http://localhost:8000/api/tasks \
     -H "Content-Type: application/json" \
     -H "Authorization: Bearer YOUR_JWT_TOKEN" \
     -d '{"title":"Complete project","description":"Finish the task management app","priority":"high"}'
   ```

### Using Postman
Import the following collection:
```json
{
  "info": {
    "name": "TaskMate API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Register",
      "request": {
        "method": "POST",
        "header": [{"key": "Content-Type", "value": "application/json"}],
        "url": "http://localhost:8000/register",
        "body": {
          "mode": "raw",
          "raw": "{\"name\":\"John Doe\",\"email\":\"john@example.com\",\"password\":\"password123\"}"
        }
      }
    }
  ]
}
```

## 📁 Project Structure

```
TaskMate_WT/
├── backend/
│   ├── public/
│   │   └── index.php              # Entry point
│   │   └── README.md              # Backend documentation
├── frontend/
│   ├── src/
│   │   ├── services/
│   │   │   └── ApiService.js      # API communication
│   │   ├── views/
│   │   │   ├── Login.vue          # Authentication page
│   │   │   └── TaskList.vue       # Task dashboard
│   │   ├── router.js              # Vue Router config
│   │   └── main.js                # Vue app entry
│   ├── package.json               # Node.js dependencies
│   └── README.md                  # Frontend documentation
└── README.md                      # This file
```

## 🔒 Security Features

- **JWT Authentication**: Secure token-based authentication
- **Password Hashing**: All passwords hashed with PHP's `password_hash()`
- **Input Validation**: Comprehensive validation on all inputs
- **SQL Injection Protection**: Using Eloquent ORM with parameterized queries
- **CORS Support**: Configured for cross-origin requests
- **Token Expiration**: JWT tokens expire after 24 hours

## 🚀 Deployment

### Backend Deployment
1. Upload backend files to your web server
2. Set document root to `backend/public`
3. Configure environment variables
4. Set up database and run migrations
5. Ensure PHP extensions are installed (pdo_mysql, json, etc.)

### Frontend Deployment
1. Build the frontend: `npm run build`
2. Upload `dist` folder to your web server
3. Configure API endpoint in production
4. Set up proper CORS headers if needed

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

If you encounter any issues or have questions:

1. Check the documentation in the `backend/README.md` and `frontend/README.md`
2. Review the API endpoints and their expected request/response formats
3. Ensure your environment meets all prerequisites
4. Check that database connection and JWT secret are properly configured

## 🎯 Learning Objectives

This project demonstrates:

- **Full-Stack Development**: Complete web application with backend and frontend
- **Modern PHP Development**: Using Slim framework and Eloquent ORM
- **Vue.js 3**: Modern JavaScript framework with Composition API
- **JWT Authentication**: Secure token-based authentication system
- **RESTful API Design**: Proper API structure and HTTP methods
- **Database Design**: Relational database with proper relationships
- **Security Best Practices**: Input validation, password hashing, SQL injection protection
- **Modern UI/UX**: Responsive design with modern CSS techniques
- **Error Handling**: Comprehensive error handling throughout the application
- **Code Organization**: Clean, maintainable code structure

This project provides hands-on experience with modern web development technologies and best practices, making it an excellent learning resource for full-stack development. 