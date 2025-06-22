# TaskMate Backend API

A RESTful API built with Slim PHP framework featuring JWT authentication and comprehensive task management.

## Features

- **JWT Authentication**: Secure token-based authentication
- **User Management**: Registration, login, profile management
- **Task Management**: Full CRUD operations for tasks
- **Task Statistics**: Completion rates and task analytics
- **Database Integration**: Eloquent ORM with MySQL

## API Endpoints

### Public Endpoints (No Authentication Required)

1. **POST /register** - User registration
   - Body: `{ "name": "string", "email": "string", "password": "string" }`
   - Returns: User data and JWT token

2. **POST /login** - User login
   - Body: `{ "email": "string", "password": "string" }`
   - Returns: User data and JWT token

### Protected Endpoints (JWT Authentication Required)

3. **GET /api/profile** - Get user profile
   - Headers: `Authorization: Bearer <token>`
   - Returns: User profile data

4. **PUT /api/profile** - Update user profile
   - Headers: `Authorization: Bearer <token>`
   - Body: `{ "name": "string", "email": "string" }`
   - Returns: Updated user data

5. **PUT /api/change-password** - Change password
   - Headers: `Authorization: Bearer <token>`
   - Body: `{ "current_password": "string", "new_password": "string" }`
   - Returns: Success message

6. **GET /api/tasks** - Get all tasks for user
   - Headers: `Authorization: Bearer <token>`
   - Returns: Array of tasks

7. **GET /api/tasks/{id}** - Get specific task
   - Headers: `Authorization: Bearer <token>`
   - Returns: Task data

8. **POST /api/tasks** - Create new task
   - Headers: `Authorization: Bearer <token>`
   - Body: `{ "title": "string", "description": "string", "priority": "low|medium|high", "due_date": "datetime" }`
   - Returns: Created task data

9. **PUT /api/tasks/{id}** - Update task
   - Headers: `Authorization: Bearer <token>`
   - Body: `{ "title": "string", "description": "string", "completed": boolean, "priority": "string", "due_date": "datetime" }`
   - Returns: Updated task data

10. **DELETE /api/tasks/{id}** - Delete task
    - Headers: `Authorization: Bearer <token>`
    - Returns: Success message

11. **PATCH /api/tasks/{id}/toggle** - Toggle task completion
    - Headers: `Authorization: Bearer <token>`
    - Returns: Updated task data

12. **GET /api/tasks/stats** - Get task statistics
    - Headers: `Authorization: Bearer <token>`
    - Returns: Task statistics (total, completed, pending, completion rate)

## Setup Instructions

### Prerequisites
- PHP 8.0 or higher
- Composer
- MySQL/MariaDB
- Web server (Apache/Nginx)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd backend
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Create environment file**
   ```bash
   cp .env.example .env
   ```

4. **Configure environment variables**
   Edit `.env` file with your database and JWT settings:
   ```
   DB_DRIVER=mysql
   DB_HOST=localhost
   DB_DATABASE=taskmate
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   JWT_SECRET=your-super-secret-jwt-key
   ```

5. **Create database**
   ```sql
   CREATE DATABASE taskmate;
   ```

6. **Run migrations**
   ```bash
   mysql -u your_username -p taskmate < database/migrations/create_tables.sql
   ```

7. **Configure web server**
   Point your web server's document root to the `public` directory.

### Development Server

For development, you can use PHP's built-in server:
```bash
cd public
php -S localhost:8000
```

## Security Features

- **Password Hashing**: All passwords are hashed using PHP's `password_hash()`
- **JWT Tokens**: Secure token-based authentication with 24-hour expiration
- **Input Validation**: Comprehensive validation for all user inputs
- **SQL Injection Protection**: Using Eloquent ORM with parameterized queries
- **CORS Support**: Configured for cross-origin requests

## Response Format

All API responses follow a consistent format:

**Success Response:**
```json
{
  "success": true,
  "data": {...},
  "message": "Optional message"
}
```

**Error Response:**
```json
{
  "success": false,
  "error": "Error message"
}
```

## Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `404` - Not Found
- `409` - Conflict
- `500` - Internal Server Error

## Testing

You can test the API using tools like Postman, curl, or any HTTP client.

Example curl commands:

```bash
# Register a new user
curl -X POST http://localhost:8000/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123"}'

# Login
curl -X POST http://localhost:8000/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'

# Create a task (use token from login response)
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{"title":"Complete project","description":"Finish the task management app","priority":"high"}'
``` 
Backend
   cd backend
   composer install
   # Create .env file with database credentials
   # Run database migration
   cd public 
   php -S localhost:8000

Frontend
   cd frontend
   npm install
   npm run dev