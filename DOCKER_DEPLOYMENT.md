# E-form Docker Deployment Guide

## Overview
This guide explains how to deploy eForm using Docker, similar to the BOM setup.

## Architecture

```
MAP (Django) → eForm (Laravel)
     ↓              ↓
  Port 8000   Docker: 9001
                ↓
           nginx → eform_web:9000
```

## Prerequisites

1. Docker and Docker Compose installed
2. Shared Docker network `map_bom_rev_network` (same as BOM)
3. Environment variables configured

## Files Created

1. **[`docker-compose.yml`](file:///Users/alif/Desktop/Project/GCP/bm-gc-repo-map-stg/eForm/docker-compose.yml)** - Docker services configuration
2. **[`Dockerfile`](file:///Users/alif/Desktop/Project/GCP/bm-gc-repo-map-stg/eForm/Dockerfile)** - eForm container build instructions
3. **[`nginx/eform.conf`](file:///Users/alif/Desktop/Project/GCP/bm-gc-repo-map-stg/eForm/nginx/eform.conf)** - Nginx reverse proxy configuration

## Network Setup

eForm uses the same Docker network as BOM (`map_bom_rev_network`). This allows:
- Shared network communication between MAP, BOM, and eForm
- Centralized routing through main nginx
- Isolated service containers

### Create Network (if not exists)
```bash
docker network create map_bom_rev_network
```

## Environment Configuration

Create `.env` file in eForm root:

```env
APP_NAME=eForm
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://eform.muamalat.com.my

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

# MAP Integration
MAP_VERIFY_URL=https://map.muamalat.com.my/api/eform/verify/
MAP_REDIRECT_URL=https://map.muamalat.com.my/redirect/eform/
MAP_REQUEST_TIMEOUT=10

SESSION_DRIVER=file
SESSION_LIFETIME=120

# Database Configuration (IMPORTANT)
# Point to the mounted path inside the container
DB_DATABASE=/db/database.sqlite

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# Docker Volume Mount (Production: /opt/eform/eform_db, Local: ./database)
MAP_DB_PATH=/opt/eform/eform_db
```

## Build and Run

### Development
```bash
cd /path/to/eForm

# Build containers
docker-compose build

# Start services
docker-compose up -d

# Check logs
docker-compose logs -f
```

### Production Deployment

1. **Build production image:**
```bash
docker-compose -f docker-compose.yml build --no-cache
```

2. **Run migrations:**
```bash
docker-compose exec web php artisan migrate --force
```

3. **Optimize for production:**
```bash
docker-compose exec web php artisan config:cache
docker-compose exec web php artisan route:cache
docker-compose exec web php artisan view:cache
```

4. **Start services:**
```bash
docker-compose up -d
```

## Main Nginx Configuration

On the main server (where MAP nginx runs), add eForm location:

```nginx
# In main nginx.conf or sites-available/map.conf

# E-form upstream
upstream eform {
    server eform_nginx:9001;
}

# E-form location
location /eform/ {
    proxy_pass http://eform/;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    
    # Cookie handling for SSO
    proxy_cookie_path / /;
    proxy_cookie_domain eform.muamalat.com.my $host;
}
```

## Service Ports

- **eform_web**: 9000 (internal)
- **eform_nginx**: 9001 (exposed to network)
- **Main nginx**: Routes `/eform/` to `eform_nginx:9001`

## Health Checks

```bash
# Check if services are running
docker-compose ps

# Check eForm web service
docker-compose exec web php artisan --version

# Test nginx
curl http://localhost:9001
```

## Troubleshooting

### Container won't start
```bash
# Check logs
docker-compose logs web
docker-compose logs nginx

# Rebuild
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Database Mounts
The mount point for the database directory is controlled by the `MAP_DB_PATH` in your `.env` file.

**For Production (SIT/Staging/Prod):**
```env
MAP_DB_PATH=/opt/eform/eform_db
DB_DATABASE=/db/database.sqlite
```

**For Local Development:**
```env
MAP_DB_PATH=./database
# DB_DATABASE can be left default or set to /db/database.sqlite if running in Docker
```

If not specified, it defaults to `./database`.

### Permission issues
```bash
# Fix storage permissions
docker-compose exec web chown -R www-data:www-data storage bootstrap/cache
docker-compose exec web chmod -R 775 storage bootstrap/cache
```

### Database not found
```bash
# Create database and run migrations
docker-compose exec web touch database/database.sqlite
docker-compose exec web php artisan migrate --force
```



## Updating eForm

```bash
# Pull latest code
git pull origin main

# Rebuild containers
docker-compose build

# Restart services
docker-compose down
docker-compose up -d

# Run migrations
docker-compose exec web php artisan migrate --force

# Clear caches
docker-compose exec web php artisan cache:clear
docker-compose exec web php artisan config:cache
docker-compose exec web php artisan route:cache
```

## Logs

View logs:
```bash
# Application logs
docker-compose logs -f web

# Nginx logs
docker-compose logs -f nginx

# Access logs
tail -f nginx/logs/eform_access.log

# Error logs
tail -f nginx/logs/eform_error.log
```

## MAP Redirect Configuration

Make sure MAP's `website/views.py` has the correct production URL:

```python
eform_url = 'https://eform.muamalat.com.my/map/login'  # Production
# eform_url = 'http://localhost:9000/map/login'  # Local development
```

## Security Considerations

1. **APP_KEY**: Generate with `php artisan key:generate`
2. **APP_DEBUG**: Must be `false` in production
3. **File Permissions**: Storage and cache must be writable
4. **HTTPS**: Use SSL certificates in production
5. **Firewall**: Only expose necessary ports

## Production Checklist

- [ ] `.env` configured with production values
- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY` generated
- [ ] Database migrated
- [ ] Storage permissions set
- [ ] Config/route/view caches cleared
- [ ] Docker network created
- [ ] Services started
- [ ] Health checks passing
- [ ] SSL configured
- [ ] Logs monitoring setup

## Similar to BOM

eForm follows the same pattern as BOM:
- Uses `map_bom_rev_network` for inter-service communication
- Nginx reverse proxy on specific port
- Integrated with MAP's SSO system
- Containerized for easy deployment
