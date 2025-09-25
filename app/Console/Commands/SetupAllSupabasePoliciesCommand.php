<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupAllSupabasePoliciesCommand extends Command
{
    protected $signature = 'supabase:setup-all-policies';
    protected $description = 'Configurar TODAS las políticas RLS necesarias para el CMS';

    public function handle()
    {
        $this->info("=== CONFIGURACIÓN COMPLETA DE POLÍTICAS RLS SUPABASE ===");
        $this->line("");

        $this->info("Ejecuta estos comandos SQL en tu dashboard de Supabase:");
        $this->warn("📍 Dashboard: https://supabase.com/dashboard/project/[TU_PROJECT_ID]/sql");
        $this->line("");

        $this->comment("═══════════════════════════════════════════════════════════════");
        $this->comment("🗂️  PASO 1: CONFIGURACIÓN DE STORAGE (Imágenes)");
        $this->comment("═══════════════════════════════════════════════════════════════");

        $storageSql = "
-- 🪣 Crear bucket si no existe
INSERT INTO storage.buckets (id, name, public, file_size_limit, allowed_mime_types)
VALUES (
    'portfolio-images',
    'portfolio-images',
    true,
    10485760, -- 10MB limit
    ARRAY['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif']
)
ON CONFLICT (id) DO UPDATE SET
    public = true,
    file_size_limit = 10485760,
    allowed_mime_types = ARRAY['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];

-- 🔓 Eliminar políticas existentes si existen
DROP POLICY IF EXISTS \"Allow public insert\" ON storage.objects;
DROP POLICY IF EXISTS \"Allow public select\" ON storage.objects;
DROP POLICY IF EXISTS \"Allow public update\" ON storage.objects;
DROP POLICY IF EXISTS \"Allow public delete\" ON storage.objects;
DROP POLICY IF EXISTS \"portfolio_images_insert_policy\" ON storage.objects;
DROP POLICY IF EXISTS \"portfolio_images_select_policy\" ON storage.objects;
DROP POLICY IF EXISTS \"portfolio_images_update_policy\" ON storage.objects;
DROP POLICY IF EXISTS \"portfolio_images_delete_policy\" ON storage.objects;

-- ✅ Crear políticas permisivas para storage
CREATE POLICY \"portfolio_images_insert_policy\" ON storage.objects
FOR INSERT WITH CHECK (bucket_id = 'portfolio-images');

CREATE POLICY \"portfolio_images_select_policy\" ON storage.objects
FOR SELECT USING (bucket_id = 'portfolio-images');

CREATE POLICY \"portfolio_images_update_policy\" ON storage.objects
FOR UPDATE USING (bucket_id = 'portfolio-images');

CREATE POLICY \"portfolio_images_delete_policy\" ON storage.objects
FOR DELETE USING (bucket_id = 'portfolio-images');
";

        $this->line($storageSql);

        $this->comment("═══════════════════════════════════════════════════════════════");
        $this->comment("📋 PASO 2: CONFIGURACIÓN DE TABLA PROJECTS");
        $this->comment("═══════════════════════════════════════════════════════════════");

        $projectsSql = "
-- 🔓 Desactivar RLS temporalmente para limpiar
ALTER TABLE public.projects DISABLE ROW LEVEL SECURITY;

-- 🗑️ Eliminar políticas existentes
DROP POLICY IF EXISTS \"Enable read access for all users\" ON public.projects;
DROP POLICY IF EXISTS \"Enable insert access for all users\" ON public.projects;
DROP POLICY IF EXISTS \"Enable update access for all users\" ON public.projects;
DROP POLICY IF EXISTS \"Enable delete access for all users\" ON public.projects;
DROP POLICY IF EXISTS \"projects_select_policy\" ON public.projects;
DROP POLICY IF EXISTS \"projects_insert_policy\" ON public.projects;
DROP POLICY IF EXISTS \"projects_update_policy\" ON public.projects;
DROP POLICY IF EXISTS \"projects_delete_policy\" ON public.projects;

-- 🔄 Reactivar RLS
ALTER TABLE public.projects ENABLE ROW LEVEL SECURITY;

-- ✅ Crear políticas permisivas para projects
CREATE POLICY \"projects_select_policy\" ON public.projects
FOR SELECT USING (true);

CREATE POLICY \"projects_insert_policy\" ON public.projects
FOR INSERT WITH CHECK (true);

CREATE POLICY \"projects_update_policy\" ON public.projects
FOR UPDATE USING (true) WITH CHECK (true);

CREATE POLICY \"projects_delete_policy\" ON public.projects
FOR DELETE USING (true);
";

        $this->line($projectsSql);

        $this->comment("═══════════════════════════════════════════════════════════════");
        $this->comment("🏷️  PASO 3: CONFIGURACIÓN DE TABLA TECH_TAGS");
        $this->comment("═══════════════════════════════════════════════════════════════");

        $techTagsSql = "
-- 🔓 Desactivar RLS temporalmente para limpiar
ALTER TABLE public.tech_tags DISABLE ROW LEVEL SECURITY;

-- 🗑️ Eliminar políticas existentes
DROP POLICY IF EXISTS \"Enable read access for all users\" ON public.tech_tags;
DROP POLICY IF EXISTS \"Enable insert access for all users\" ON public.tech_tags;
DROP POLICY IF EXISTS \"Enable update access for all users\" ON public.tech_tags;
DROP POLICY IF EXISTS \"Enable delete access for all users\" ON public.tech_tags;
DROP POLICY IF EXISTS \"tech_tags_select_policy\" ON public.tech_tags;
DROP POLICY IF EXISTS \"tech_tags_insert_policy\" ON public.tech_tags;
DROP POLICY IF EXISTS \"tech_tags_update_policy\" ON public.tech_tags;
DROP POLICY IF EXISTS \"tech_tags_delete_policy\" ON public.tech_tags;

-- 🔄 Reactivar RLS
ALTER TABLE public.tech_tags ENABLE ROW LEVEL SECURITY;

-- ✅ Crear políticas permisivas para tech_tags
CREATE POLICY \"tech_tags_select_policy\" ON public.tech_tags
FOR SELECT USING (true);

CREATE POLICY \"tech_tags_insert_policy\" ON public.tech_tags
FOR INSERT WITH CHECK (true);

CREATE POLICY \"tech_tags_update_policy\" ON public.tech_tags
FOR UPDATE USING (true) WITH CHECK (true);

CREATE POLICY \"tech_tags_delete_policy\" ON public.tech_tags
FOR DELETE USING (true);
";

        $this->line($techTagsSql);

        $this->comment("═══════════════════════════════════════════════════════════════");
        $this->comment("🔧 PASO 4: CONFIGURACIONES ADICIONALES (OPCIONAL)");
        $this->comment("═══════════════════════════════════════════════════════════════");

        $additionalSql = "
-- 📊 Si tienes tabla de estadísticas o logs
-- ALTER TABLE public.analytics DISABLE ROW LEVEL SECURITY;
-- ALTER TABLE public.analytics ENABLE ROW LEVEL SECURITY;
-- CREATE POLICY \"analytics_full_access\" ON public.analytics FOR ALL USING (true) WITH CHECK (true);

-- 👤 Si tienes tabla de usuarios personalizada
-- ALTER TABLE public.users DISABLE ROW LEVEL SECURITY;
-- ALTER TABLE public.users ENABLE ROW LEVEL SECURITY;
-- CREATE POLICY \"users_full_access\" ON public.users FOR ALL USING (true) WITH CHECK (true);

-- 🗂️ Si tienes más tablas, usa este patrón:
-- ALTER TABLE public.tu_tabla DISABLE ROW LEVEL SECURITY;
-- ALTER TABLE public.tu_tabla ENABLE ROW LEVEL SECURITY;
-- CREATE POLICY \"tu_tabla_full_access\" ON public.tu_tabla FOR ALL USING (true) WITH CHECK (true);
";

        $this->line($additionalSql);

        $this->comment("═══════════════════════════════════════════════════════════════");
        $this->comment("✅ VERIFICACIÓN");
        $this->comment("═══════════════════════════════════════════════════════════════");

        $this->line("");
        $this->info("Después de ejecutar todos los comandos SQL, ejecuta estos comandos para verificar:");
        $this->info("1️⃣  php artisan test:crud");
        $this->info("2️⃣  php artisan test:web-image-upload");
        $this->info("3️⃣  php artisan supabase:diagnose");

        $this->line("");
        $this->warn("⚠️  IMPORTANTE:");
        $this->warn("- Estas políticas permiten acceso público total");
        $this->warn("- Para producción, considera políticas más restrictivas");
        $this->warn("- Guarda una copia de seguridad antes de aplicar cambios");

        $this->line("");
        $this->comment("💡 NOTAS:");
        $this->comment("- Las políticas 'FOR ALL' combinan SELECT, INSERT, UPDATE, DELETE");
        $this->comment("- USING (true) permite la operación sin restricciones");
        $this->comment("- WITH CHECK (true) permite insertar/actualizar sin validaciones");

        $this->line("");
        $this->info("🎉 Una vez aplicadas las políticas, tu CMS tendrá acceso completo a:");
        $this->info("   ✓ Crear, leer, actualizar y eliminar proyectos");
        $this->info("   ✓ Gestionar tech tags");
        $this->info("   ✓ Subir, ver y eliminar imágenes");
        $this->info("   ✓ Sin restricciones RLS");
    }
}
