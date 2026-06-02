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
    - Social profile integrations (LinkedIn, GitHub, Twitter, and more).
- **Company Management**:
    - Creation and management of company records (slug, logo, size, industry, etc.).
    - Company people management with granular role mapping (`Owner`, `Admin`, `Recruiter`, `Employee`).
    - Company social profile integrations (LinkedIn, GitHub, Twitter, and more) — mirroring the per-user social profile system.
- **Posts and Updates**:
    - Personal posts for all authenticated users.
    - Company posts for company owners, admins, and recruiters.
    - Rich text content with HTML support, tags, attachments, categories, publish status, and scheduled date/time fields.
- **Interactive API Documentation**:
    - Live Swagger UI documentation served directly from the application.

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
   Copy the example environment file and generate an application key:

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

#### Authentication

| Route                             | Method | Description                        | Auth            |
| --------------------------------- | ------ | ---------------------------------- | --------------- |
| `/auth/register`                  | `POST` | Register a new account             | Public          |
| `/auth/login`                     | `POST` | Log in and receive Sanctum token   | Public          |
| `/auth/forgot-password`           | `POST` | Send password reset link           | Public          |
| `/auth/reset-password`            | `POST` | Reset password using token         | Public          |
| `/auth/verify-email/{id}/{hash}`  | `GET`  | Verify email address (Signed URL)  | Public (Signed) |
| `/auth/logout`                    | `POST` | Log out and revoke active token    | Protected       |
| `/auth/refresh-token`             | `POST` | Revoke current and issue new token | Protected       |
| `/auth/resend-verification-email` | `POST` | Resend the email verification link | Protected       |

#### Profile

| Route                                      | Method                 | Description                                 | Auth      |
| ------------------------------------------ | ---------------------- | ------------------------------------------- | --------- |
| `/profile`                                 | `GET / PATCH`          | Retrieve or update profile details          | Protected |
| `/profile/avatar`                          | `POST / DELETE`        | Store or delete profile avatar              | Protected |
| `/profile/picture`                         | `POST / DELETE`        | Store or delete profile picture/thumbnail   | Protected |
| `/profile/cvs`                             | `GET / POST`           | List or upload CV files                     | Protected |
| `/profile/cvs/{cv}`                        | `GET / DELETE`         | Retrieve or delete a specific CV            | Protected |
| `/profile/projects`                        | `GET / POST`           | List or create portfolio projects           | Protected |
| `/profile/projects/{project}`              | `GET / PATCH / DELETE` | Retrieve, update or delete a project        | Protected |
| `/profile/skills`                          | `GET / POST`           | List or create skills                       | Protected |
| `/profile/skills/{skill}`                  | `GET / PATCH / DELETE` | Retrieve, update or delete a skill          | Protected |
| `/profile/social-profiles`                 | `GET / POST`           | List or create social profiles              | Protected |
| `/profile/social-profiles/{socialProfile}` | `GET / PATCH / DELETE` | Retrieve, update or delete a social profile | Protected |

#### Companies

| Route                                                  | Method   | Description                                | Auth                    |
| ------------------------------------------------------ | -------- | ------------------------------------------ | ----------------------- |
| `/companies`                                           | `GET`    | List all companies (with search & filters) | Protected               |
| `/companies`                                           | `POST`   | Create a new company                       | Protected               |
| `/companies/my`                                        | `GET`    | Get authenticated user's companies         | Protected               |
| `/companies/{company}`                                 | `GET`    | Retrieve a specific company                | Protected               |
| `/companies/{company}`                                 | `PATCH`  | Update a company                           | Protected (Owner/Admin) |
| `/companies/{company}`                                 | `DELETE` | Delete a company                           | Protected (Owner/Admin) |
| `/companies/{company}/people`                          | `GET`    | List all people in a company               | Protected               |
| `/companies/{company}/people`                          | `POST`   | Add a person to a company                  | Protected (Owner/Admin) |
| `/companies/{company}/people/{membership}`             | `PATCH`  | Update a member's membership record        | Protected (Owner/Admin) |
| `/companies/{company}/people/{membership}`             | `DELETE` | Remove a member from a company             | Protected (Owner/Admin) |
| `/companies/{company}/social-profiles`                 | `GET`    | List a company's social profiles           | Protected               |
| `/companies/{company}/social-profiles`                 | `POST`   | Add a social profile to a company          | Protected (Owner/Admin) |
| `/companies/{company}/social-profiles/{socialProfile}` | `GET`    | Retrieve a specific company social profile | Protected               |
| `/companies/{company}/social-profiles/{socialProfile}` | `PATCH`  | Update a company social profile            | Protected (Owner/Admin) |
| `/companies/{company}/social-profiles/{socialProfile}` | `DELETE` | Delete a company social profile            | Protected (Owner/Admin) |

#### Posts

| Route           | Method   | Description                                          | Auth & Authorization                                |
| --------------- | -------- | ---------------------------------------------------- | --------------------------------------------------- |
| `/posts`        | `GET`    | List posts with search, filters, sorting, and paging | Protected                                           |
| `/posts`        | `POST`   | Create a personal or company post                    | Protected                                           |
| `/posts/{post}` | `GET`    | Retrieve a specific post                             | Protected                                           |
| `/posts/{post}` | `PATCH`  | Update a post                                        | Protected (author or company owner/admin/recruiter) |
| `/posts/{post}` | `DELETE` | Delete a post                                        | Protected (author or company owner/admin/recruiter) |

Notes:

- Posts support HTML content, tags, attachments, category, status, and post date/time.
- Company post permissions follow current company membership roles: `owner`, `admin`, and `recruiter`.
- The Swagger JSON is served from `/api/documentation.json` and reflects the same post endpoints.

#### Vacancies (Jobs)

| Route                  | Method   | Description                                           | Auth & Authorization                                                                                           |
| ---------------------- | -------- | ----------------------------------------------------- | -------------------------------------------------------------------------------------------------------------- |
| `/vacancies`           | `GET`    | List vacancies (search, filters, pagination, sorting) | Protected (any authenticated user)                                                                             |
| `/vacancies`           | `POST`   | Create a vacancy                                      | Protected — only the company owner or a company member whose `company_role` is `recruiter`                     |
| `/vacancies/{vacancy}` | `GET`    | Retrieve a specific vacancy                           | Protected (any authenticated user)                                                                             |
| `/vacancies/{vacancy}` | `PATCH`  | Update a vacancy                                      | Protected — only the vacancy creator, the company owner, or a company member with `company_role` = `recruiter` |
| `/vacancies/{vacancy}` | `DELETE` | Delete a vacancy                                      | Protected — same rules as update                                                                               |

Notes:

- Company membership role is stored in the `company_memberships` table (`company_role` column). The system checks membership records when deciding recruiter permissions.
- Vacancy creation now defaults `status` to `draft` when omitted; publishing sets `published_at` automatically when `status` is `published`.
- Routes are defined in `routes/api/vacancies.php` and guarded by `auth:sanctum`.

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

The suite currently covers **169 tests** with **615 assertions** across Auth, Profile, Company, Post, and Vacancy feature areas.

---

## 📂 Project Structure

Here are the key directories containing the main domain logic:

- `app/Http/Controllers/` — API Controllers grouped by domains (`Auth`, `Profile`, `Company`, `Post`).
- `app/Models/` — Eloquent models detailing relations and properties.
- `app/Policies/` — Authorization policies (e.g. `CompanyPolicy`, `PostPolicy`).
- `app/Services/` — Business logic layer (e.g. `CompanyService`, `PostService`, `AuthService`).
- `app/Enums/` — Typed enums for roles (`CompanyRoles`), post categories/statuses, social profile types, etc.
- `app/Support/OpenApiSpec.php` — Static definitions and builder for OpenAPI/Swagger documentation.
- `database/migrations/` — Database schema migrations.
- `routes/api/` — Modular route definition files (`auth.php`, `company.php`, `profile.php`, `posts.php`).
- `tests/` — Feature and Unit tests using Pest.
