# SAGA Multi-Architecture - Quick Reference
# Data: 14/08/2025 | Status: ✅ CONCLUÍDO

## 🚀 Quick Commands

### Build & Deploy
```bash
# Development
docker compose up -d

# Staging  
cd deploy/staging && docker compose -f docker-compose.staging.yml up -d

# Production
./scripts/deployment/deploy-production.sh deploy

# Multi-arch build
./scripts/deployment/build-multiarch.sh --push --tag v1.0.0
```

### Maintenance
```bash
# Project cleanup
./scripts/deployment/cleanup-project.sh --force

# Container status
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

# Logs
docker logs saga_app_dev --tail 20
```

## 📊 Environment Status

| Environment | Port | Container | Status | Database |
|-------------|------|-----------|--------|----------|
| Development | 8000 | saga_app_dev | ✅ HEALTHY | saga (5432) |
| Staging | 8080 | saga_app_staging | ✅ HEALTHY | saga_staging (5433) |
| Production | 80 | saga_app_prod | 🔄 Ready | saga_prod |

## 🏗️ Architecture

### Multi-Stage Dockerfile
1. **frontend** (Node.js) → Vite build
2. **vendor** (Composer) → PHP dependencies  
3. **runtime** (PHP Apache) → Final image

### Key Features
- ✅ Multi-arch: linux/amd64, linux/arm64
- ✅ Cache optimization
- ✅ Health checks
- ✅ Volume mounts (dev/staging)
- ✅ Environment isolation

## 🔧 Troubleshooting

### Common Issues
1. **Container unhealthy**: Check .env configuration
2. **Build fails**: Verify platform requirements
3. **Database connection**: Check credentials and host

### Debug Commands
```bash
# Check container logs
docker logs <container_name>

# Execute commands in container
docker exec <container_name> php artisan migrate:status

# Test HTTP response
curl -s -o /dev/null -w "%{http_code}" http://localhost:<port>
```

## 📁 File Structure
```
saga/
├── deploy/production/          # Production configs
├── deploy/staging/             # Staging configs  
├── scripts/deployment/         # Automation scripts
├── Dockerfile                  # Multi-stage build
└── docker-compose.yml          # Development
```

**Last updated**: 14/08/2025 | **Build time**: ~51s | **Image size**: 697MB
