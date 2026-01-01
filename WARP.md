# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

DayMeter is a Laravel 12 web application built with a modern stack combining PHP/Laravel backend with a Node.js frontend toolchain. It uses Laravel Breeze for authentication scaffolding and Blade templates for server-side rendering with Tailwind CSS for styling.

## Core Technologies

- **Backend**: Laravel 12, PHP 8.2+
- **Database**: MySQL (via Docker/Sail)
- **Frontend**: Blade templates, Alpine.js, Tailwind CSS, Vite
- **Testing**: PHPUnit
- **Code Quality**: Laravel Pint
- **Containerization**: Docker (Laravel Sail)

## Development Commands

### Setup
```bash
composer run setup
```
Installs dependencies, sets up environment, generates app key, runs migrations, and builds frontend assets.

### Development Server
```bash
composer run dev
```
Runs a development environment with concurrent processes:
- Laravel server (`php artisan serve`)
- Queue worker (`php artisan queue:listen`)
- Log watcher (`php artisan pail`)
- Vite dev server (`npm run dev`)

Use Ctrl+C to stop all processes.

### Frontend Build
```bash
npm run build      # Production build
npm run dev        # Development with hot reload
```

### Testing
```bash
composer test              # Run all tests
php artisan test tests/Feature/ProfileTest.php  # Run single test file
```

Tests are organized in `tests/Feature/` (integration) and `tests/Unit/` (unit tests). PHPUnit configuration is in `phpunit.xml`.

### Code Quality
```bash
./vendor/bin/pint   # Format code with Laravel Pint
```

### Database
```bash
php artisan migrate              # Run migrations
php artisan migrate:refresh      # Reset and re-run migrations
php artisan tinker              # Interactive PHP shell for testing models
```

### Artisan Commands
General Laravel commands:
```bash
php artisan route:list          # View all routes
php artisan make:model ModelName # Generate model
php artisan make:controller ControllerName  # Generate controller
php artisan make:migration create_table_name  # Create migration
```

## Project Structure

### Key Directories
- **app/**: Application code
  - `Http/Controllers/`: Route handlers
  - `Models/`: Eloquent models
  - `Providers/`: Service providers
  - `View/`: View components
- **routes/**: Route definitions
  - `web.php`: Web routes
  - `auth.php`: Authentication routes
- **resources/**: Frontend assets and views
  - `views/`: Blade templates (organized by feature)
  - `css/`: Tailwind CSS entry point
  - `js/`: JavaScript entry point and Alpine.js
- **database/**: Migrations and seeds
- **tests/**: PHPUnit tests (Feature and Unit)
- **config/**: Application configuration files
- **public/**: Web root, compiled assets

### Key Files
- `vite.config.js`: Vite bundler configuration
- `tailwind.config.js`: Tailwind CSS configuration
- `composer.json`: PHP dependencies and scripts
- `package.json`: Node.js dependencies and scripts
- `phpunit.xml`: PHPUnit configuration

## Architecture Patterns

### Controllers
Controllers inherit from `App\Http\Controllers\Controller` and handle request/response flow. Use dependency injection for services.

### Models & Database
Models use Eloquent ORM with mass assignment (`$fillable`) and casting (`casts()` method). Database schema is managed via migrations in `database/migrations/`.

### Blade Templates
Server-side templated views in `resources/views/` use Blade syntax. Components are in `resources/views/components/`. Authentication views are scaffolded in `resources/views/auth/`.

### Frontend
- Vite bundles CSS and JS from `resources/` during build
- Alpine.js provides lightweight interactivity
- Tailwind CSS provides utility-first styling via `@tailwindcss/forms` plugin
- Compiled assets are served from `public/` in production

### Authentication
Handled by Laravel Breeze with routes defined in `routes/auth.php`. Protected routes use `auth` and `verified` middleware.

## Docker & Local Development

The project uses Laravel Sail (Docker wrapper). Configuration is in `compose.yml`. To use Sail:

```bash
./vendor/bin/sail up              # Start containers
./vendor/bin/sail down            # Stop containers
./vendor/bin/sail artisan <cmd>   # Run artisan in container
```

Default environment (`.env`):
- Database: MySQL on `mysql:3306`
- Cache/Queue: Redis on `redis:6379`
- App port: `80` (override with `APP_PORT`)
- Vite port: `5173` (override with `VITE_PORT`)

## Testing Strategy

Tests use PHPUnit. Feature tests verify request/response behavior. Unit tests verify business logic. Test database is configured in `phpunit.xml` to use a separate test database with SQLite or test MySQL database.

## Common Development Tasks

### Adding a New Route
1. Define route in `routes/web.php` or `routes/auth.php`
2. Create controller with `php artisan make:controller ControllerName`
3. Implement route handler method
4. Create Blade template in `resources/views/`

### Creating a Database Table
1. Run `php artisan make:migration create_table_name`
2. Define schema in generated migration file
3. Run `php artisan migrate`
4. Create corresponding model with `php artisan make:model ModelName`

### Adding Frontend Interactivity
Use Alpine.js for simple interactions (included in `resources/js/bootstrap.js`). For complex state, consider extracting components to `resources/views/components/`.

