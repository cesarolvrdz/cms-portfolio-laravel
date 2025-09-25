# 🚀 DEPLOYMENT EN VERCEL - GUÍA COMPLETA

## 📋 CONFIGURACIÓN COMPLETADA

### ✅ **Archivos de Configuración Creados:**
- `vercel.json` - Configuración principal de Vercel
- `.vercelignore` - Archivos a ignorar en el deploy
- `api/index.php` - Entry point para Vercel PHP Runtime

## ⚠️ **PROBLEMA IMPORTANTE: BASE DE DATOS**

Tu proyecto usa **SQLite** que **NO ES COMPATIBLE** con Vercel (entorno serverless).

### 🔧 **SOLUCIONES DISPONIBLES:**

#### **Opción 1: PlanetScale (Recomendado)**
```env
DB_CONNECTION=mysql
DB_HOST=tu-host.planetscale.net
DB_PORT=3306
DB_DATABASE=tu-database
DB_USERNAME=tu-username
DB_PASSWORD=tu-password
```

#### **Opción 2: Supabase PostgreSQL (Ya tienes cuenta)**
```env
DB_CONNECTION=pgsql
DB_HOST=db.wlkjxdhjcbyimozkvuxb.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=tu-db-password
```

#### **Opción 3: Railway/Render PostgreSQL**
Similar a Supabase pero en otras plataformas.

## 🔑 **VARIABLES DE ENTORNO PARA VERCEL**

### **Variables Requeridas:**
```env
APP_NAME=Laravel CMS
APP_ENV=production
APP_KEY=base64:tu-app-key-generada
APP_DEBUG=false
APP_URL=https://tu-subdominio.vercel.app

# Base de datos (elegir una opción)
DB_CONNECTION=pgsql
DB_HOST=tu-host
DB_PORT=5432
DB_DATABASE=tu-database
DB_USERNAME=tu-username
DB_PASSWORD=tu-password

# Supabase (ya tienes estos)
SUPABASE_URL=https://wlkjxdhjcbyimozkvuxb.supabase.co
SUPABASE_KEY=tu-anon-key
SUPABASE_SERVICE_KEY=tu-service-key
SUPABASE_STORAGE_BUCKET=portfolio-images
SUPABASE_BUCKET=portfolio-images
PORTFOLIO_DOMAIN=https://cesarolvrdz.github.io/Portafolio-Nuevo/

# Logs y sesiones
LOG_CHANNEL=stderr
LOG_LEVEL=error
SESSION_DRIVER=cookie
```

## 📝 **PASOS PARA EL DEPLOYMENT**

### **1. Instalar Vercel CLI**
```bash
npm install -g vercel
```

### **2. Login en Vercel**
```bash
vercel login
```

### **3. Configurar Base de Datos**
- Ve a tu cuenta de Supabase
- Activa la base de datos PostgreSQL
- Obtén las credenciales de conexión

### **4. Actualizar Configuración Laravel**
```bash
composer require doctrine/dbal
```

### **5. Deploy**
```bash
vercel --prod
```

## ❓ **¿QUÉ PREFIERES HACER?**

1. **Usar Supabase PostgreSQL** (más fácil, ya tienes cuenta)
2. **Configurar PlanetScale** (MySQL optimizado para serverless)
3. **Otro servicio de base de datos**

**¡Dime qué opción prefieres y continuamos con la configuración!** 🎯
