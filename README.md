# Task Manager API

A RESTful API for task management built with Laravel 12, featuring JWT-style authentication with Sanctum, role-based access control (RBAC), and comprehensive API documentation.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-blue.svg)

## Table of Contents

- [Features](#features)
- [Quick Start](#quick-start)
- [Installation](#installation)
  - [Local Setup](#local-setup)
  - [Docker Setup](#docker-setup)
- [API Documentation](#api-documentation)
- [Authentication Flow](#authentication-flow)
- [API Endpoints](#api-endpoints)
- [Example Requests](#example-requests)
- [Database Seeding](#database-seeding)
- [Testing](#testing)
- [Project Structure](#project-structure)
- [License](#license)

## Features

- **User Authentication** - Secure token-based auth with Laravel Sanctum
- **Role-Based Access Control** - Admin and User roles with protected routes
- **Task Management** - Full CRUD operations for tasks
- **API Documentation** - Auto-generated OpenAPI docs via Scramble
- **Postman Collection** - Ready-to-use API testing collection
- **Database Seeding** - Demo data for quick start
- **Docker Support** - Laravel Sail for containerized development
- **Pagination & Filtering** - Search, filter by status, due date, pagination
- **Form Request Validation** - Type-safe request validation
- **Service/Repository Pattern** - Clean, maintainable code architecture

---

## Quick Start

### Using Docker (Recommended)

```bash
git clone <repository-url>
cd task-manager
cp .env.example .env

# Start containers and setup
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed

# Access at http://localhost
```

### Using Makefile

```bash
make setup    # Full setup: install deps, migrate, seed
make start    # Start containers
make fresh    # Fresh migrate with seed
```

---

## Installation

### Local Setup

#### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL 8.0+ or MariaDB
- Redis (optional, for cache/queues)

#### Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd task-manager
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Copy and configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database** in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_manager_api
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

5. **Run migrations**
   ```bash
   php artisan migrate --seed
   ```

6. **Install frontend dependencies** (if needed)
   ```bash
   npm install && npm run build
   ```

7. **Start development server**
   ```bash
   php artisan serve
   ```

8. **Access the application**
   - API: http://localhost:8000
   - API Docs: http://localhost:8000/docs/api

---

### Docker Setup

#### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running
- Git

#### Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd task-manager
   ```

2. **Copy environment file**
   ```bash
   cp .env.example .env
   ```

3. **Start Docker containers**
   ```bash
   ./vendor/bin/sail up -d
   ```

4. **Install dependencies and run migrations**
   ```bash
   ./vendor/bin/sail composer install
   ./vendor/bin/sail artisan key:generate
   ./vendor/bin/sail artisan migrate --seed
   ```

5. **Access the application**
   - API: http://localhost
   - API Documentation: http://localhost/docs/api
   - phpMyAdmin: http://localhost:8080

#### Docker Services

| Service    | Container      | Port | Description              |
|------------|----------------|------|--------------------------|
| Laravel    | laravel.test   | 80   | PHP/Laravel application  |
| MySQL      | mysql          | 3306 | Database                 |
| Redis      | redis          | 6379 | Cache/Queue              |
| phpMyAdmin | phpmyadmin     | 8080 | Database management UI   |

#### Docker Database Credentials

```
Host: mysql
Database: task_manager_api
Username: sail
Password: secret
```

#### Makefile Commands

```bash
make start          # Start containers
make stop           # Stop containers
make restart        # Restart containers
make shell          # Open shell in container
make logs           # View logs
make logs-follow    # Follow logs (real-time)
make migrate        # Run migrations
make seed           # Run seeders
make fresh          # Fresh migrate + seed
make test           # Run PHPUnit tests
make setup          # Full setup (install + migrate + seed)
make queue          # Start queue worker
make npm-dev        # Run npm dev server
make npm-build      # Build npm assets
```

---

## API Documentation

### OpenAPI Documentation (Scramble)

The API includes auto-generated OpenAPI documentation powered by Scramble.

**URL:** `http://localhost/docs/api` (or `http://localhost:8000/docs/api` for local)

Features:
- Interactive API explorer
- Request/response schemas
- Authentication examples
- Try-it-out functionality

### Postman Collection

Import the Postman collection for easy API testing.

**Files:**
- Collection: `Task Management Api.postman_collection.json`
- Environment: Configure `base_url` and `beare_token` variables

**Setup:**
1. Open Postman
2. Import `Task Management Api.postman_collection.json`
3. Set environment variables:
   - `base_url`: `http://localhost:8000/api` (local) or `http://localhost/api` (Docker)
   - `beare_token`: Your auth token (obtained from login)

---

## Authentication Flow

The API uses Laravel Sanctum for token-based authentication.

### 1. Register a New User

```bash
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": { "id": 1, "name": "John Doe", "email": "john@example.com", "role": "user" },
    "token": "your-api-token"
  }
}
```

### 2. Login

```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": { "id": 1, "name": "John Doe", "email": "john@example.com", "role": "user" },
    "token": "your-api-token"
  }
}
```

### 3. Use Token for Authenticated Requests

Include the token in the `Authorization` header:

```
Authorization: Bearer your-api-token
```

### 4. Logout

```bash
POST /api/auth/logout
Authorization: Bearer your-api-token
```

### 5. Get Current User

```bash
GET /api/user
Authorization: Bearer your-api-token
```

---

## API Endpoints

### Authentication

| Method | Endpoint              | Description        | Auth Required |
|--------|-----------------------|--------------------|---------------|
| POST   | `/api/auth/register`  | Register new user  | No            |
| POST   | `/api/auth/login`     | Login user         | No            |
| POST   | `/api/auth/logout`    | Logout user        | Yes           |
| GET    | `/api/user`           | Get current user   | Yes           |

### Tasks (User)

| Method | Endpoint                  | Description         | Auth Required |
|--------|---------------------------|---------------------|---------------|
| GET    | `/api/tasks`              | List all tasks      | Yes           |
| POST   | `/api/tasks`              | Create task         | Yes           |
| GET    | `/api/tasks/{id}`         | Get task details    | Yes           |
| PUT    | `/api/tasks/{id}`         | Update task         | Yes           |
| PATCH  | `/api/tasks/{id}`         | Partial update      | Yes           |
| DELETE | `/api/tasks/{id}`         | Delete task         | Yes           |
| PATCH  | `/api/tasks/{id}/complete`| Mark task complete  | Yes           |

### Admin Endpoints

| Method | Endpoint                     | Description          | Auth Required | Role  |
|--------|------------------------------|----------------------|---------------|-------|
| GET    | `/api/admin/users`           | List all users       | Yes           | Admin |
| PATCH  | `/api/admin/users/{id}/role` | Update user role     | Yes           | Admin |
| GET    | `/api/admin/tasks`           | List all tasks       | Yes           | Admin |
| PATCH  | `/api/admin/tasks/{id}`      | Update any task      | Yes           | Admin |
| DELETE | `/api/admin/tasks/{id}`      | Delete any task      | Yes           | Admin |

### Query Parameters (Tasks)

| Parameter   | Type   | Description                    |
|-------------|--------|--------------------------------|
| `search`    | string | Search by title/description    |
| `status`    | string | Filter by status (pending, in_progress, completed) |
| `due_date`  | date   | Filter by due date (Y-m-d)     |
| `per_page`  | integer| Items per page (default: 15)   |
| `page`      | integer| Page number                    |

---

## Example Requests

### Create a Task

```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Complete project documentation",
    "description": "Write comprehensive README and API docs",
    "status": "pending",
    "due_date": "2026-04-25"
  }'
```

### List Tasks with Filters

```bash
curl -X GET "http://localhost:8000/api/tasks?status=pending&per_page=10&page=1" \
  -H "Authorization: Bearer your-token" \
  -H "Accept: application/json"
```

### Update a Task

```bash
curl -X PUT http://localhost:8000/api/tasks/1 \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Updated title",
    "status": "in_progress"
  }'
```

### Mark Task as Complete

```bash
curl -X PATCH http://localhost:8000/api/tasks/1/complete \
  -H "Authorization: Bearer your-token" \
  -H "Accept: application/json"
```

### Admin: Update User Role

```bash
curl -X PATCH "http://localhost:8000/api/admin/users/2/role?role=admin" \
  -H "Authorization: Bearer admin-token" \
  -H "Accept: application/json"
```

---

## Database Seeding

The seeder creates demo users and tasks for testing.

### Run Seeders

```bash
# With migrations
php artisan migrate --seed

# Or standalone
php artisan db:seed
```

### Demo Users

| Role  | Name         | Email              | Password |
|-------|--------------|--------------------|----------|
| Admin | Admin User   | admin@example.com  | password |
| User  | Test User    | test@example.com   | password |

### What Gets Seeded

1. **Admin User** - Full access to all endpoints
2. **Test User** - Standard user with 5 sample tasks
3. **Additional Users** - 5 users with 3-8 random tasks each (via TaskSeeder)

### Task Statuses

- `pending` - Task not yet started
- `in_progress` - Task being worked on
- `completed` - Task finished

---

## Testing

### Run Tests

```bash
# Using Sail (Docker)
./vendor/bin/sail artisan test

# Local
php artisan test
```

### Test Coverage

```bash
./vendor/bin/sail artisan test --coverage
```

---

## Project Structure

```
task-manager/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── TaskController.php
│   │   │       └── AdminController.php
│   │   └── Requests/          # Form Request Validation
│   └── Models/
│       ├── User.php
│       └── Task.php
├── database/
│   ├── factories/
│   │   ├── UserFactory.php
│   │   └── TaskFactory.php
│   ├── migrations/
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── TaskSeeder.php
├── routes/
│   └── api.php                # API route definitions
├── docker-compose.yml         # Docker configuration
├── Makefile                   # Helper commands
├── Task Management Api.postman_collection.json
└── README.md
```

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## Support

For issues or questions, please create an issue in the repository.
