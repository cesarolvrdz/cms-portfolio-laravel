<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSupabasePoliciesFileCommand extends Command
{
    protected $signature = 'supabase:generate-policies-file';
    protected $description = 'Genera un archivo SQL con todas las políticas RLS';

    public function handle()
    {
        $sqlContent = "-- ═══════════════════════════════════════════════════════════════
-- 🚀 CONFIGURACIÓN COMPLETA RLS PARA CMS PORTFOLIO
-- ═══════════════════════════════════════════════════════════════
-- Generado automáticamente por Laravel CMS
-- Fecha: " . now()->format('Y-m-d H:i:s') . "
-- ═══════════════════════════════════════════════════════════════

-- 🗂️ PASO 1: CONFIGURACIÓN DE STORAGE (Imágenes)
-- ═══════════════════════════════════════════════════════════════

-- Crear bucket con configuración completa
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

-- Limpiar políticas existentes
DROP POLICY IF EXISTS \"Allow public insert\" ON storage.objects;
DROP POLICY IF EXISTS \"Allow public select\" ON storage.objects;
DROP POLICY IF EXISTS \"Allow public update\" ON storage.objects;
DROP POLICY IF EXISTS \"Allow public delete\" ON storage.objects;
DROP POLICY IF EXISTS \"portfolio_images_insert_policy\" ON storage.objects;
DROP POLICY IF EXISTS \"portfolio_images_select_policy\" ON storage.objects;
DROP POLICY IF EXISTS \"portfolio_images_update_policy\" ON storage.objects;
DROP POLICY IF EXISTS \"portfolio_images_delete_policy\" ON storage.objects;

-- Crear políticas permisivas para storage
CREATE POLICY \"portfolio_images_insert_policy\" ON storage.objects
FOR INSERT WITH CHECK (bucket_id = 'portfolio-images');

CREATE POLICY \"portfolio_images_select_policy\" ON storage.objects
FOR SELECT USING (bucket_id = 'portfolio-images');

CREATE POLICY \"portfolio_images_update_policy\" ON storage.objects
FOR UPDATE USING (bucket_id = 'portfolio-images');

CREATE POLICY \"portfolio_images_delete_policy\" ON storage.objects
FOR DELETE USING (bucket_id = 'portfolio-images');

-- 📋 PASO 2: CONFIGURACIÓN DE TABLA PROJECTS
-- ═══════════════════════════════════════════════════════════════

-- Desactivar RLS temporalmente para limpiar
ALTER TABLE public.projects DISABLE ROW LEVEL SECURITY;

-- Eliminar políticas existentes
DROP POLICY IF EXISTS \"Enable read access for all users\" ON public.projects;
DROP POLICY IF EXISTS \"Enable insert access for all users\" ON public.projects;
DROP POLICY IF EXISTS \"Enable update access for all users\" ON public.projects;
DROP POLICY IF EXISTS \"Enable delete access for all users\" ON public.projects;
DROP POLICY IF EXISTS \"projects_select_policy\" ON public.projects;
DROP POLICY IF EXISTS \"projects_insert_policy\" ON public.projects;
DROP POLICY IF EXISTS \"projects_update_policy\" ON public.projects;
DROP POLICY IF EXISTS \"projects_delete_policy\" ON public.projects;

-- Reactivar RLS
ALTER TABLE public.projects ENABLE ROW LEVEL SECURITY;

-- Crear políticas permisivas para projects
CREATE POLICY \"projects_select_policy\" ON public.projects
FOR SELECT USING (true);

CREATE POLICY \"projects_insert_policy\" ON public.projects
FOR INSERT WITH CHECK (true);

CREATE POLICY \"projects_update_policy\" ON public.projects
FOR UPDATE USING (true) WITH CHECK (true);

CREATE POLICY \"projects_delete_policy\" ON public.projects
FOR DELETE USING (true);

-- 🏷️ PASO 3: CONFIGURACIÓN DE TABLA TECH_TAGS
-- ═══════════════════════════════════════════════════════════════

-- Desactivar RLS temporalmente para limpiar
ALTER TABLE public.tech_tags DISABLE ROW LEVEL SECURITY;

-- Eliminar políticas existentes
DROP POLICY IF EXISTS \"Enable read access for all users\" ON public.tech_tags;
DROP POLICY IF EXISTS \"Enable insert access for all users\" ON public.tech_tags;
DROP POLICY IF EXISTS \"Enable update access for all users\" ON public.tech_tags;
DROP POLICY IF EXISTS \"Enable delete access for all users\" ON public.tech_tags;
DROP POLICY IF EXISTS \"tech_tags_select_policy\" ON public.tech_tags;
DROP POLICY IF EXISTS \"tech_tags_insert_policy\" ON public.tech_tags;
DROP POLICY IF EXISTS \"tech_tags_update_policy\" ON public.tech_tags;
DROP POLICY IF EXISTS \"tech_tags_delete_policy\" ON public.tech_tags;

-- Reactivar RLS
ALTER TABLE public.tech_tags ENABLE ROW LEVEL SECURITY;

-- Crear políticas permisivas para tech_tags
CREATE POLICY \"tech_tags_select_policy\" ON public.tech_tags
FOR SELECT USING (true);

CREATE POLICY \"tech_tags_insert_policy\" ON public.tech_tags
FOR INSERT WITH CHECK (true);

CREATE POLICY \"tech_tags_update_policy\" ON public.tech_tags
FOR UPDATE USING (true) WITH CHECK (true);

CREATE POLICY \"tech_tags_delete_policy\" ON public.tech_tags
FOR DELETE USING (true);

-- ✅ FINALIZADO
-- ═══════════════════════════════════════════════════════════════
-- Todas las políticas han sido configuradas
-- Tu CMS ahora tiene acceso completo a todas las operaciones
-- ═══════════════════════════════════════════════════════════════
";

        $filePath = storage_path('app/supabase_rls_policies.sql');

        File::put($filePath, $sqlContent);

        $this->info("=== ARCHIVO SQL GENERADO ===");
        $this->line("");
        $this->info("📄 Archivo creado: " . $filePath);
        $this->line("");

        $this->info("🚀 INSTRUCCIONES:");
        $this->info("1️⃣  Abre: https://supabase.com/dashboard/project/[TU_PROJECT_ID]/sql");
        $this->info("2️⃣  Copia y pega todo el contenido del archivo SQL");
        $this->info("3️⃣  Ejecuta el script completo");
        $this->info("4️⃣  Verifica con: php artisan test:crud && php artisan test:web-image-upload");

        $this->line("");
        $this->comment("💡 CONTENIDO DEL ARCHIVO:");
        $this->line("");
        $this->line($sqlContent);

        $this->line("");
        $this->warn("⚠️  RECUERDA: Guarda una copia de seguridad de tu base de datos antes de aplicar los cambios");
    }
}
