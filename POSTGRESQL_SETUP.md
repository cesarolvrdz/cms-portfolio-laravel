# 📋 CONFIGURACIÓN POSTGRESQL PARA VERCEL

## 🔍 CREDENCIALES DE SUPABASE NECESARIAS

Para completar la configuración, necesito que obtengas estas credenciales de tu **Panel de Supabase**:

### 📍 **Dónde encontrar las credenciales:**

1. **Ve a [supabase.com](https://supabase.com)**
2. **Abre tu proyecto:** `wlkjxdhjcbyimozkvuxb`
3. **Ve a Settings → Database**
4. **Busca "Connection String" o "Database URL"**

### 🔑 **Formato de las credenciales que necesitas:**

```
Host: db.wlkjxdhjcbyimozkvuxb.supabase.co
Database: postgres
Username: postgres
Password: [TU_DB_PASSWORD]
Port: 5432
```

**O la URL completa:**
```
postgresql://postgres:[TU_DB_PASSWORD]@db.wlkjxdhjcbyimozkvuxb.supabase.co:5432/postgres
```

## ⚡ **MIENTRAS TANTO...**

He preparado la configuración base y instalado las dependencias necesarias:

- ✅ **doctrine/dbal** instalado (soporte PostgreSQL)
- ✅ **vercel.json** configurado
- ✅ **api/index.php** listo

## 🔄 **PRÓXIMOS PASOS:**

1. **Obtén las credenciales** de Supabase
2. **Actualizaré la configuración** con tus datos
3. **Migraré los datos** de SQLite a PostgreSQL
4. **Haremos el deploy** en Vercel

---

## 🤔 **¿TIENES LAS CREDENCIALES?**

Una vez que las tengas, las configuraremos y continuaremos con el deployment.

**¡Dime cuando tengas las credenciales de PostgreSQL de Supabase!** 🎯
