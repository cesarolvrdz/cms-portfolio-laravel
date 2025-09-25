<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateNewTablesCommand extends Command
{
    protected $signature = 'supabase:create-new-tables';
    protected $description = 'Generar SQL para crear las nuevas tablas en Supabase';

    public function handle()
    {
        $this->info("=== SQL PARA CREAR NUEVAS TABLAS EN SUPABASE ===");
        $this->line("");

        $this->info("📋 Ejecuta estos comandos SQL en tu dashboard de Supabase:");
        $this->warn("📍 Dashboard: https://supabase.com/dashboard/project/[TU_PROJECT_ID]/sql");
        $this->line("");

        $sql = "-- ═══════════════════════════════════════════════════════════════
-- 👤 TABLA PROFILE - Información personal del portfolio
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS public.profile (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    title VARCHAR(150) NOT NULL,
    bio TEXT NOT NULL,
    avatar TEXT NULL,
    resume_url TEXT NULL,
    location VARCHAR(100) NULL,
    phone VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    skills JSONB NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- ═══════════════════════════════════════════════════════════════
-- 🔗 TABLA SOCIAL_LINKS - Enlaces a redes sociales
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS public.social_links (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,
    url TEXT NOT NULL,
    icon VARCHAR(50) NULL,
    color VARCHAR(7) NULL,
    is_active BOOLEAN DEFAULT true,
    \"order\" INTEGER DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),

    CONSTRAINT unique_platform UNIQUE (platform)
);

-- Índices para optimización
CREATE INDEX IF NOT EXISTS idx_social_links_active_order ON public.social_links (is_active, \"order\");

-- ═══════════════════════════════════════════════════════════════
-- ⚙️ TABLA SITE_SETTINGS - Configuraciones globales
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS public.site_settings (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    key VARCHAR(100) NOT NULL,
    value TEXT NOT NULL,
    type VARCHAR(20) DEFAULT 'text',
    \"group\" VARCHAR(50) DEFAULT 'general',
    label VARCHAR(100) NOT NULL,
    description TEXT NULL,
    is_public BOOLEAN DEFAULT false,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),

    CONSTRAINT unique_setting_key UNIQUE (key)
);

-- Índices para optimización
CREATE INDEX IF NOT EXISTS idx_site_settings_group_key ON public.site_settings (\"group\", key);
CREATE INDEX IF NOT EXISTS idx_site_settings_public ON public.site_settings (is_public);

-- ═══════════════════════════════════════════════════════════════
-- 🔒 POLÍTICAS RLS - Permisos para las nuevas tablas
-- ═══════════════════════════════════════════════════════════════

-- Habilitar RLS en las nuevas tablas
ALTER TABLE public.profile ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.social_links ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.site_settings ENABLE ROW LEVEL SECURITY;

-- Políticas permisivas para PROFILE
CREATE POLICY \"profile_select_policy\" ON public.profile FOR SELECT USING (true);
CREATE POLICY \"profile_insert_policy\" ON public.profile FOR INSERT WITH CHECK (true);
CREATE POLICY \"profile_update_policy\" ON public.profile FOR UPDATE USING (true) WITH CHECK (true);
CREATE POLICY \"profile_delete_policy\" ON public.profile FOR DELETE USING (true);

-- Políticas permisivas para SOCIAL_LINKS
CREATE POLICY \"social_links_select_policy\" ON public.social_links FOR SELECT USING (true);
CREATE POLICY \"social_links_insert_policy\" ON public.social_links FOR INSERT WITH CHECK (true);
CREATE POLICY \"social_links_update_policy\" ON public.social_links FOR UPDATE USING (true) WITH CHECK (true);
CREATE POLICY \"social_links_delete_policy\" ON public.social_links FOR DELETE USING (true);

-- Políticas permisivas para SITE_SETTINGS
CREATE POLICY \"site_settings_select_policy\" ON public.site_settings FOR SELECT USING (true);
CREATE POLICY \"site_settings_insert_policy\" ON public.site_settings FOR INSERT WITH CHECK (true);
CREATE POLICY \"site_settings_update_policy\" ON public.site_settings FOR UPDATE USING (true) WITH CHECK (true);
CREATE POLICY \"site_settings_delete_policy\" ON public.site_settings FOR DELETE USING (true);

-- ═══════════════════════════════════════════════════════════════
-- 📊 DATOS INICIALES DE EJEMPLO
-- ═══════════════════════════════════════════════════════════════

-- Insertar perfil de ejemplo
INSERT INTO public.profile (name, title, bio, email, skills, is_active) VALUES
(
    'César Olivares',
    'Full Stack Developer',
    'Desarrollador apasionado con experiencia en tecnologías modernas. Especializado en crear soluciones web innovadoras.',
    'cesolvrdz@gmail.com',
    '[\"Laravel\", \"JavaScript\", \"React\", \"Vue.js\", \"PHP\", \"Python\"]',
    true
)
ON CONFLICT DO NOTHING;

-- Insertar enlaces sociales de ejemplo
INSERT INTO public.social_links (platform, url, icon, color, \"order\", is_active) VALUES
('github', 'https://github.com/cesarolvrdz', 'bi-github', '#333333', 1, true),
('linkedin', 'https://linkedin.com/in/cesarolvrdz', 'bi-linkedin', '#0077b5', 2, true),
('twitter', 'https://twitter.com/cesarolvrdz', 'bi-twitter', '#1da1f2', 3, true),
('email', 'mailto:cesolvrdz@gmail.com', 'bi-envelope', '#ea4335', 4, true)
ON CONFLICT (platform) DO NOTHING;

-- Insertar configuraciones iniciales
INSERT INTO public.site_settings (key, value, type, \"group\", label, description, is_public) VALUES
('site_title', 'César Olivares - Portfolio', 'text', 'general', 'Título del Sitio', 'Título principal que aparece en el navegador', true),
('site_description', 'Portfolio de César Olivares - Desarrollador Full Stack', 'text', 'seo', 'Descripción del Sitio', 'Descripción meta para SEO', true),
('contact_email', 'cesolvrdz@gmail.com', 'email', 'contact', 'Email de Contacto', 'Email principal para contacto', true),
('analytics_id', '', 'text', 'tracking', 'Google Analytics ID', 'ID de seguimiento de Google Analytics', false),
('theme_color', '#667eea', 'text', 'appearance', 'Color del Tema', 'Color principal del sitio', true),
('show_projects', 'true', 'boolean', 'visibility', 'Mostrar Proyectos', 'Si mostrar la sección de proyectos', true),
('projects_per_page', '6', 'number', 'pagination', 'Proyectos por Página', 'Cantidad de proyectos a mostrar por página', false)
ON CONFLICT (key) DO NOTHING;

-- ✅ FINALIZADO
-- ═══════════════════════════════════════════════════════════════
-- Las nuevas tablas están listas con datos de ejemplo
-- ═══════════════════════════════════════════════════════════════
";

        $this->line($sql);

        $this->line("");
        $this->info("🎯 DESPUÉS DE EJECUTAR EL SQL:");
        $this->info("1️⃣  Ejecuta: php artisan supabase:update-service");
        $this->info("2️⃣  Ejecuta: php artisan test:new-tables");
    }
}
