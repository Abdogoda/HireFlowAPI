# HireFlow API

HireFlow API is a modern recruitment and talent acquisition platform backend built using Laravel. It provides comprehensive endpoints for candidate profiling, company administration, and role-based access control.

## 🚀 Key Features

- **Robust Authentication**: 
  - User registration and role assignment.
  - Secure API token authentication powered by **Laravel Sanctum**.
  - Password reset workflows.
  - Double-loop email verification (signed verification URLs).
  - Secure token refresh and token revocation (logout) capabilities.
- **Detailed Candidate Profiles**:
  - Personal details, bio, and contact info management.
  - Avatar image and profile/thumbnail image uploads.
  - Multi-CV/Resume PDF file management (allowing setting a primary CV).
  - Project portfolio showcase (with technologies, URLs, and date ranges).
  - Skills tracking with proficiency levels and years of experience.
  - Social profiles integrations (LinkedIn, GitHub, etc.).
- **Company Management**:
  - Creation and update of company records (slug, logo, size, industry, etc.).
  - Company member (people) management with granular role mapping (`Admin`, `Recruiter`, `Candidate`).
- **Interactive API Documentation**:
  - Live swagger documentation served directly from the application.

---

## 🛠️ Tech Stack

- **Framework**: Laravel 13.x
- **PHP Version**: PHP 8.3+
- **Authentication**: Laravel Sanctum
- **Asset Bundle & Styles**: Vite 8.0+ & TailwindCSS 4.0 (for Swagger frontend)
- **Database**: SQLite (default configuration, easily switchable to MySQL/Postgres)
- **Testing Suite**: Pest PHP 4.7+
- **Log Viewer**: Opcodes Log Viewer

---

## ⚙️ Getting Started

### Prerequisites

- PHP 8.3 or higher
- Composer
- Node.js & NPM
- SQLite (or another supported RDBMS)

### Quick Setup

Run the built-in composer setup script to automate package installations, environment variables provisioning, key generation, database migration, and asset building:

```bash
composer run setup
```

### Manual Installation Steps

If you prefer to set up the project manually step-by-step:

1. **Clone the Repository** and navigate to the project directory:
   ```bash
   git clone <repository-url>
   cd HireFlowAPI
   ```

2. **Install Composer Dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment Variables**:
   Copy the example environment file and generate a application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Prepare the Database**:
   Create the SQLite database file and run the migrations:
   ```bash
   # Create database file (if using SQLite)
   touch database/database.sqlite
   
   # Run migrations and seed roles/default users
   php artisan migrate --seed
   ```

5. **Link Public Storage**:
   Enable uploads for resumes and avatars:
   ```bash
   php artisan storage:link
   ```

6. **Install NPM Dependencies and Build Assets**:
   ```bash
   npm install
   npm run build
   ```

---

## 🏃 Running the Application

To run the application's dev server, Vite asset builder, and the database queue listener concurrently, execute:

```bash
composer run dev
```

The application will be served at `http://localhost:8000` (or the port indicated in your console output).

---

## 📝 API Documentation

An interactive API documentation console is built directly into the project using OpenAPI/Swagger.

- **Interactive Swagger UI**: `http://localhost:8000/api/documentation`
- **Raw OpenAPI JSON Spec**: `http://localhost:8000/api/documentation.json`

### Key API Endpoints Summary

All routes are prefixed with `/api`.

| Route | Method | Description | Authentication |
|---|---|---|---|
| `/auth/register` | `POST` | Register a new account | Public |
| `/auth/login` | `POST` | Log in and receive Sanctum token | Public |
| `/auth/forgot-password` | `POST` | Send password reset link | Public |
| `/auth/reset-password` | `POST` | Reset password using token | Public |
| `/auth/verify-email/{id}/{hash}`| `GET` | Verify email address (Signed URL) | Public (Signed) |
| `/auth/logout` | `POST` | Log out and revoke active token | Protected |
| `/auth/refresh-token` | `POST` | Revoke current and issue new token | Protected |
| `/profile` | `GET/PATCH` | Retrieve or update basic profile details | Protected |
| `/profile/avatar` | `POST/DELETE`| Store or delete profile avatar image | Protected |
| `/profile/cvs` | `GET/POST` | List or upload CV files | Protected |
| `/profile/projects` | `GET/POST` | Manage portfolio projects | Protected |
| `/profile/skills` | `GET/POST` | Manage profile skills | Protected |
| `/companies` | `GET/POST` | List or register new companies | Protected |
| `/companies/my` | `GET` | Get user's managed companies | Protected |
| `/companies/{company}/people` | `POST` | Add member to a company | Protected |

---

## 🧪 Testing

The project uses the **Pest PHP** testing framework. You can run all test suites (including feature tests for profiles, companies, and authentication) by running:

```bash
composer run test
```

Or manually using the Artisan runner:
```bash
php artisan test
```

---

## 📂 Project Structure

Here are the key directories containing the main domain logic:

- `app/Http/Controllers/` - API Controllers grouped by domains (`Auth`, `Profile`, `Company`).
- `app/Models/` - Eloquent models detailing relations and properties.
- `app/Services/` - Business logic layer (e.g. `AuthService`).
- `app/Support/OpenApiSpec.php` - Static definitions and builder for OpenAPI/Swagger documentation.
- `routes/api/` - Modular route definition files (`auth.php`, `company.php`, `profile.php`).
- `tests/` - Feature and Unit tests using Pest.
