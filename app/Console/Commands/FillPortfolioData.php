<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FillPortfolioData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portfolio:fill-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Llena las tablas con los datos reales del portafolio de César';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== SQL PERSONALIZADO PARA TU PORTAFOLIO ===');
        $this->info('📋 Ejecuta este SQL en Supabase con TUS datos:');
        $this->info('');

        $sql = "-- ═══════════════════════════════════════════════════════════════
-- 👤 DATOS REALES - CÉSAR OLVERA RODRIGUEZ
-- ═══════════════════════════════════════════════════════════════

-- Limpiar datos existentes (opcional)
TRUNCATE TABLE public.profile CASCADE;
TRUNCATE TABLE public.social_links CASCADE;
TRUNCATE TABLE public.site_settings CASCADE;

-- ═══════════════════════════════════════════════════════════════
-- 👤 PERFIL PERSONAL - César Olvera Rodriguez
-- ═══════════════════════════════════════════════════════════════
INSERT INTO public.profile (name, title, bio, email, location, skills, is_active) VALUES
(
    'César Olvera Rodriguez',
    'Desarrollador Web Full Stack',
    'Desarrollador apasionado especializado en tecnologías modernas. Con experiencia en desarrollo web full stack, me enfoco en crear soluciones innovadoras y eficientes. Experto en Laravel, JavaScript y frameworks modernos para el desarrollo de aplicaciones web robustas.',
    'cesolvrdz@gmail.com',
    'México',
    '[\"Laravel\", \"PHP\", \"JavaScript\", \"Astro\", \"Tailwind CSS\", \"MySQL\", \"GitHub Actions\", \"Mine-Imator\", \"Desarrollo 3D\", \"Diseño Web\"]',
    true
);

-- ═══════════════════════════════════════════════════════════════
-- 🔗 ENLACES SOCIALES - Redes profesionales
-- ═══════════════════════════════════════════════════════════════
INSERT INTO public.social_links (platform, url, icon, color, \"order\", is_active) VALUES
('github', 'https://github.com/cesarolvrdz', 'bi-github', '#333333', 1, true),
('email', 'mailto:cesolvrdz@gmail.com', 'bi-envelope-fill', '#ea4335', 2, true),
('linkedin', 'https://linkedin.com/in/cesarolvrdz', 'bi-linkedin', '#0077b5', 3, true),
('portfolio', '#projects', 'bi-collection-fill', '#667eea', 4, true);

-- ═══════════════════════════════════════════════════════════════
-- ⚙️ CONFIGURACIONES DEL SITIO - César Portfolio
-- ═══════════════════════════════════════════════════════════════
INSERT INTO public.site_settings (key, value, type, \"group\", label, description, is_public) VALUES

-- Configuración General
('site_title', 'César Olvera Rodriguez - Desarrollador Full Stack', 'text', 'general', 'Título del Sitio', 'Título principal del portafolio', true),
('site_description', 'Portafolio profesional de César Olvera Rodriguez - Desarrollador Web Full Stack especializado en tecnologías modernas', 'text', 'seo', 'Descripción del Sitio', 'Meta descripción para SEO', true),
('site_keywords', 'desarrollador, full stack, Laravel, PHP, JavaScript, Astro, portafolio, césar olvera', 'text', 'seo', 'Palabras Clave', 'Keywords para SEO', true),

-- Información de Contacto
('contact_email', 'cesolvrdz@gmail.com', 'email', 'contact', 'Email Principal', 'Email de contacto principal', true),
('contact_phone', '', 'text', 'contact', 'Teléfono', 'Número de contacto', false),
('contact_location', 'México', 'text', 'contact', 'Ubicación', 'Ubicación geográfica', true),

-- Configuración de Apariencia
('theme_color', '#667eea', 'text', 'appearance', 'Color Principal', 'Color principal del sitio', true),
('accent_color', '#f093fb', 'text', 'appearance', 'Color Secundario', 'Color de acento', true),
('hero_title', 'Desarrollador Web Full Stack', 'text', 'content', 'Título Hero', 'Título principal de la sección hero', true),
('hero_subtitle', 'Especializado en tecnologías modernas', 'text', 'content', 'Subtítulo Hero', 'Subtítulo de la sección hero', true),

-- Configuración de Secciones
('show_about', 'true', 'boolean', 'sections', 'Mostrar About', 'Mostrar sección Acerca de', true),
('show_projects', 'true', 'boolean', 'sections', 'Mostrar Proyectos', 'Mostrar sección de proyectos', true),
('show_contact', 'true', 'boolean', 'sections', 'Mostrar Contacto', 'Mostrar sección de contacto', true),
('projects_per_page', '6', 'number', 'pagination', 'Proyectos por Página', 'Cantidad de proyectos por página', false),

-- Contenido About Section
('about_title', 'Sobre Mí', 'text', 'content', 'Título About', 'Título de la sección About', true),
('about_description', 'Desarrollador apasionado con experiencia en tecnologías modernas. Me especializo en crear soluciones web innovadoras y eficientes, con un enfoque particular en el desarrollo full stack utilizando Laravel, JavaScript y herramientas modernas.', 'textarea', 'content', 'Descripción About', 'Descripción de la sección About', true),

-- Configuración de Proyectos
('projects_title', 'Mis Proyectos', 'text', 'content', 'Título Proyectos', 'Título de la sección de proyectos', true),
('projects_description', 'Una selección de mis trabajos más recientes y destacados', 'text', 'content', 'Descripción Proyectos', 'Descripción de la sección de proyectos', true),

-- Tecnologías Destacadas
('featured_technologies', '[\"Laravel\", \"JavaScript\", \"Astro\", \"Tailwind CSS\", \"PHP\", \"MySQL\"]', 'json', 'content', 'Tecnologías Destacadas', 'Lista de tecnologías principales', true),

-- Configuración Analytics (privada)
('google_analytics_id', '', 'text', 'tracking', 'Google Analytics ID', 'ID de Google Analytics', false),
('google_tag_manager_id', '', 'text', 'tracking', 'GTM ID', 'ID de Google Tag Manager', false),

-- Meta configuración
('site_author', 'César Olvera Rodriguez', 'text', 'meta', 'Autor del Sitio', 'Nombre del autor', true),
('site_language', 'es', 'text', 'meta', 'Idioma', 'Idioma principal del sitio', true),
('last_updated', '" . now()->toDateString() . "', 'date', 'meta', 'Última Actualización', 'Fecha de última actualización', false);

-- ✅ DATOS PERSONALIZADOS INSERTADOS
-- ═══════════════════════════════════════════════════════════════";

        $this->line($sql);

        $this->info('');
        $this->info('🎯 DESPUÉS DE EJECUTAR EL SQL:');
        $this->info('1️⃣  Ejecuta: php artisan test:new-tables');
        $this->info('2️⃣  Los datos estarán listos para tu portafolio');
        $this->info('');
        $this->info('📊 DATOS INCLUIDOS:');
        $this->info('   👤 Perfil: César Olvera Rodriguez - Desarrollador Full Stack');
        $this->info('   🔗 Enlaces: GitHub, Email, LinkedIn, Portfolio interno');
        $this->info('   ⚙️ Configuraciones: 20+ configuraciones personalizadas');
        $this->info('   🎨 Temas: Colores, títulos, descripciones personalizadas');

        return 0;
    }
}
