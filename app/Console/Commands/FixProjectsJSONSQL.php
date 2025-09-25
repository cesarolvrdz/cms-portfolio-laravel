<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixProjectsJSONSQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:fix-json-sql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera el SQL correcto con formato JSON para la columna tech';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== SQL CORREGIDO CON FORMATO JSON ===');
        $this->info('📋 La columna "tech" requiere formato JSON. Ejecuta este SQL:');
        $this->line('');

        $sql = "-- Limpiar proyectos existentes
TRUNCATE TABLE public.projects CASCADE;

-- Insertar tus 3 proyectos con formato JSON correcto para tech
INSERT INTO public.projects (title, description, tech, link, image, status, created_at, updated_at) VALUES

-- Proyecto 1: Portafolio Personal
(
    'Portafolio Personal',
    'Sitio web profesional desarrollado con Astro y Tailwind CSS, diseñado para mostrar proyectos y habilidades de forma elegante. Incluye secciones de presentación, proyectos destacados y información de contacto con un diseño moderno y responsivo.',
    '[\"Astro\", \"Tailwind CSS\", \"JavaScript\", \"HTML5\", \"CSS3\", \"GitHub Actions\", \"Responsive Design\"]',
    'https://cesarolvrdz.dev',
    '/favicon.ico',
    'completed',
    NOW(),
    NOW()
),

-- Proyecto 2: Discarr - Empresa de Carrocería
(
    'Discarr - Pagina Web para Empresa de Carrocería',
    'El proyecto Discarr es una aplicación web desarrollada en Laravel pensada para una empresa de carrocería. Su propósito es funcionar tanto como un sitio corporativo para los clientes (mostrar servicios, contacto y agendar citas), como una plataforma interna de gestión para la empresa.',
    '[\"Laravel\", \"PHP\", \"Composer\", \"MySQL\", \"Blade Templates\", \"Bootstrap\", \"JavaScript\", \"HTML5\", \"CSS3\"]',
    'https://github.com/DulceMar765/Discarr',
    '/favicon.ico',
    'in_progress',
    NOW(),
    NOW()
),

-- Proyecto 3: Renders 3D con Mine-Imator
(
    'Creación de Renders 3D para Contenido',
    'Galería de renders y animaciones creadas con Mine-Imator, mostrando proyectos creativos y habilidades de diseño 3D. Incluye escenas detalladas, efectos de iluminación avanzados y composiciones artísticas que demuestran experiencia en modelado y renderizado 3D.',
    '[\"Mine-Imator\", \"Diseño 3D\", \"Renderizado\", \"Animación\", \"Modelado 3D\", \"Efectos Visuales\", \"Composición Digital\"]',
    'https://cesarolvrdz.dev/renders',
    '/favicon.ico',
    'in_progress',
    NOW(),
    NOW()
);";

        $this->line($sql);

        $this->info('');
        $this->info('✅ CAMBIOS REALIZADOS:');
        $this->info('   • Columna "tech" ahora usa formato JSON: [\"tech1\", \"tech2\"]');
        $this->info('   • Todas las tecnologías están como array de strings');
        $this->info('   • Formato compatible con PostgreSQL/Supabase');
        $this->info('');
        $this->info('🎯 DESPUÉS DE EJECUTAR:');
        $this->info('1️⃣ Este SQL debería funcionar sin errores');
        $this->info('2️⃣ Ejecuta: php artisan test:new-tables');
        $this->info('3️⃣ Verifica que los proyectos se insertaron correctamente');

        return 0;
    }
}
