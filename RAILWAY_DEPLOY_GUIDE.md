# 🚀 RAILWAY DEPLOYMENT GUIDE

## ✅ ARCHIVOS PREPARADOS PARA RAILWAY

El proyecto ahora incluye:
- ✅ `Dockerfile` - Configuración de contenedor
- ✅ `railway.json` - Configuración de Railway
- ✅ `docker/nginx.conf` - Servidor web
- ✅ `docker/supervisord.conf` - Gestor de procesos
- ✅ `docker/start.sh` - Script de inicio

## 🛤️ PASOS PARA DEPLOYMENT EN RAILWAY

### 1. Crear cuenta en Railway
- Ve a: https://railway.app
- Registrate con GitHub

### 2. Crear nuevo proyecto
1. Click "New Project"
2. Selecciona "Deploy from GitHub repo"
3. Autoriza Railway a acceder a tu repositorio
4. Selecciona `cesarolvrdz/cms-ces`

### 3. Configurar variables de entorno
En Railway dashboard, agrega estas variables:

```bash
# Laravel Core
APP_NAME=Portfolio CMS
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:GENERAR_NUEVA_KEY

# Base de datos (Railway te dará estas automáticamente si agregas PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=containers-us-west-xxx.railway.app
DB_PORT=5432
DB_DATABASE=railway
DB_USERNAME=postgres
DB_PASSWORD=xxx

# Supabase (usar tus credenciales reales)
SUPABASE_URL=https://wlkjxdhjcbyimozkvuxb.supabase.co
SUPABASE_KEY=tu-anon-key
SUPABASE_SERVICE_KEY=tu-service-key
SUPABASE_BUCKET=portfolio-images

# Configuración de producción
SESSION_DRIVER=database
CACHE_STORE=database
LOG_CHANNEL=stderr
QUEUE_CONNECTION=database
```

### 4. Agregar base de datos PostgreSQL (Opcional)
- En Railway dashboard: "New" → "Database" → "PostgreSQL"
- Las variables de entorno se configurarán automáticamente

### 5. Deploy automático
- Railway detectará el `Dockerfile`
- El deploy se iniciará automáticamente
- Obtendrás una URL como: `https://tu-app.railway.app`

## 🔑 GENERAR APP_KEY

```bash
# En tu terminal local:
php artisan key:generate --show
```

Copia el resultado a Railway como `APP_KEY`

## 🎯 VENTAJAS DE RAILWAY SOBRE VERCEL

✅ **Soporte completo PHP 8.2**
✅ **Laravel 12 sin problemas**
✅ **Base de datos PostgreSQL incluida**
✅ **Variables de entorno fáciles**
✅ **Logs en tiempo real**
✅ **SSL automático**
✅ **Escalado automático**

---

¿Quieres que suba estos cambios a GitHub y procedamos con Railway?
