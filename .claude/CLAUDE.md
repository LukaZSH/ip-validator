# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

Always use context7 when I need code generation, setup or configuration steps, or
library/API documentation. This means you should automatically use the Context7 MCP
tools to resolve library id and get library docs without me having to explicitly ask.

## Project Overview

This is a PHP-based Attendance Validation System (Sistema de Validação de Presença) for academic events at UNESPAR - Apucarana campus. The application validates student presence through IP address verification, time validation, and anti-fraud mechanisms.

## Development Commands

### Local Development
```bash
# Install dependencies
composer install

# Run static analysis
composer validate --strict
vendor/bin/phpstan analyse

# Check syntax
find . -type f -name "*.php" -print0 | xargs -0 -n1 -P4 php -l

# Security audit
composer audit
```

### Docker Development
```bash
# Build and run full stack
docker-compose up -d

# View logs
docker-compose logs -f

# Stop services
docker-compose down

# Database setup (run once)
php scripts/setup.php
```

### Database Operations
```bash
# Initialize database schema
php scripts/setup.php

# Test database connection
php tests/DatabaseConnectionTest.php
```

## Architecture

### Core Structure
- **app/**: Main application code
  - `controllers/`: MVC controllers (HomeController, AdminController, AuthController)
  - `middleware/`: Authentication middleware
  - `views/`: PHP templates
  - `routes.php`: Route definitions using Pecee SimpleRouter
- **src/**: Additional application classes (Config, SessionHelper)
- **public/**: Web root with index.php entry point
- **vendor/**: Composer dependencies

### Key Dependencies
- `pecee/simple-router`: Routing framework
- `endroid/qr-code`: QR code generation
- PHP 8.1+ required

### Docker Services
- **web**: Apache + PHP 8.1 application container
- **db**: MySQL 8.0 database
- **loki/promtail/grafana**: PLG observability stack

### Authentication & Security
- Session-based authentication for admin panel
- IP validation for campus network access
- Time-based event validation
- Anti-fraud presence tracking (one registration per IP per day)

### Route Structure
- `/`: Public home page
- `/evento/{slug}`: Public event access page
- `/admin/*`: Protected admin panel (requires authentication)
- `/login`, `/logout`: Authentication endpoints

### Database Tables
- `events`: Event management (name, slug, iframe_code, start_time, end_time, status)
- `presences`: Anti-fraud tracking (user_ip, registration_date)

## Configuration

### Environment Variables (.env)
- Database credentials for MySQL container
- Grafana admin credentials
- Timezone: America/Sao_Paulo

### Static Analysis
- PHPStan configuration in `phpstan.neon`
- Level 5 analysis on app/ and src/ directories
- Views excluded from analysis

## CI/CD Pipeline

The GitHub Actions workflow (`.github/workflows/pipeline-CI-CD.yaml`) includes:
1. **Static Analysis**: PHP syntax check, PHPStan, Composer validation/audit
2. **Integration Tests**: Database connection testing with MySQL service
3. **Security**: Trivy container scanning
4. **Deployment**: Automatic image publishing to GHCR on master branch

## Monitoring

The application includes a complete observability stack:
- **Loki**: Log aggregation (port 3100)
- **Promtail**: Log collection from Docker containers
- **Grafana**: Visualization dashboard (port 3000)