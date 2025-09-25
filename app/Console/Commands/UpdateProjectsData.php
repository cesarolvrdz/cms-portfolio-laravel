<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateProjectsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:update-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza la tabla projects con los proyectos reales de César';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== SQL PARA ACTUALIZAR PROYECTOS ===');
        $this->info('📋 Ejecuta este SQL en Supabase para actualizar tus proyectos:');
        $this->info('');

        $sql = "-- ═══════════════════════════════════════════════════════════════
-- 🚀 PROYECTOS REALES - CÉSAR OLVERA RODRIGUEZ
-- ═══════════════════════════════════════════════════════════════

-- Limpiar proyectos existentes
TRUNCATE TABLE public.projects CASCADE;

-- ═══════════════════════════════════════════════════════════════
-- 📂 PROYECTOS DEL PORTAFOLIO
-- ═══════════════════════════════════════════════════════════════

INSERT INTO public.projects (title, description, image, technologies, status, github_url, demo_url, is_featured, created_at, updated_at) VALUES

-- Proyecto 1: Portafolio Personal
(
    'Portafolio Personal',
    'Sitio web profesional desarrollado con Astro y Tailwind CSS, diseñado para mostrar proyectos y habilidades de forma elegante. Incluye secciones de presentación, proyectos destacados y información de contacto con un diseño moderno y responsivo.',
    '/favicon.ico',
    '[\"Astro\", \"Tailwind CSS\", \"JavaScript\", \"HTML5\", \"CSS3\", \"GitHub Actions\", \"Responsive Design\"]',
    'completed',
    'https://github.com/cesarolvrdz/portfolio',
    'https://cesarolvrdz.dev',
    true,
    NOW(),
    NOW()
),

-- Proyecto 2: CMS con Laravel
(
    'CMS con Laravel',
    'Sistema de gestión de contenidos personalizado desarrollado con Laravel 12 y Supabase. Incluye administración de proyectos, autenticación, carga de imágenes y optimizaciones de rendimiento. Diseñado específicamente para gestionar el contenido del portafolio.',
    '/favicon.ico',
    '[\"Laravel 12\", \"PHP 8.4\", \"Supabase\", \"PostgreSQL\", \"Bootstrap 5\", \"Blade Templates\", \"RESTful API\"]',
    'in_progress',
    'https://github.com/cesarolvrdz/cms-laravel',
    null,
    true,
    NOW(),
    NOW()
),

-- Proyecto 3: Colección de Renders 3D
(
    'Colección de Renders 3D',
    'Galería de renders y animaciones creadas con Mine-Imator, mostrando proyectos creativos y habilidades de diseño 3D. Incluye escenas detalladas, efectos de iluminación avanzados y composiciones artísticas que demuestran experiencia en modelado y renderizado 3D.',
    '/favicon.ico',
    '[\"Mine-Imator\", \"Diseño 3D\", \"Renderizado\", \"Animación\", \"Modelado 3D\", \"Efectos Visuales\", \"Composición Digital\"]',
    'in_progress',
    null,
    'https://cesarolvrdz.dev/renders',
    true,
    NOW(),
    NOW()
);

-- ═══════════════════════════════════════════════════════════════
-- 📊 RESUMEN DE PROYECTOS INSERTADOS
-- ═══════════════════════════════════════════════════════════════

-- Total: 3 proyectos
-- • 1 Completado (Portafolio Personal)
-- • 2 En Progreso (CMS Laravel, Renders 3D)
-- • 3 Proyectos destacados (featured)

-- ✅ PROYECTOS ACTUALIZADOS";

        $this->line($sql);

        $this->info('');
        $this->info('🎯 DESPUÉS DE EJECUTAR EL SQL:');
        $this->info('1️⃣  Los 3 proyectos estarán actualizados');
        $this->info('2️⃣  Podrás verlos en el admin del CMS');
        $this->info('3️⃣  Están listos para mostrar en tu portafolio');
        $this->info('');
        $this->info('📊 PROYECTOS INCLUIDOS:');
        $this->info('   🌐 Portafolio Personal (Astro + Tailwind) - Completado');
        $this->info('   📝 CMS con Laravel (Laravel 12 + Supabase) - En Progreso');
        $this->info('   🎨 Renders 3D (Mine-Imator) - En Progreso');

        return 0;
    }
}
