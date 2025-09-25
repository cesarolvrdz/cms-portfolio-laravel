<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixProjectsSQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:fix-sql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera SQL corregido para actualizar proyectos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== SQL CORREGIDO PARA PROYECTOS ===');
        $this->info('');
        $this->info('🔧 OPCIÓN 1: Agregar columna technologies y ejecutar el INSERT');
        $this->info('📋 Ejecuta primero este SQL para agregar la columna:');
        $this->line('');

        $alterTableSQL = "-- Agregar la columna technologies a la tabla projects
ALTER TABLE public.projects
ADD COLUMN IF NOT EXISTS technologies JSONB DEFAULT '[]';

-- Agregar índice para búsquedas
CREATE INDEX IF NOT EXISTS idx_projects_technologies
ON public.projects USING GIN (technologies);";

        $this->line($alterTableSQL);

        $this->info('');
        $this->info('🔧 OPCIÓN 2: SQL SIN la columna technologies (más simple)');
        $this->info('📋 Ejecuta este SQL directamente:');
        $this->line('');

        $insertSQL = "-- Limpiar proyectos existentes
TRUNCATE TABLE public.projects CASCADE;

-- Insertar proyectos SIN la columna technologies
INSERT INTO public.projects (title, description, image, status, github_url, demo_url, is_featured, created_at, updated_at) VALUES

-- Proyecto 1: Portafolio Personal
(
    'Portafolio Personal',
    'Sitio web profesional desarrollado con Astro y Tailwind CSS, diseñado para mostrar proyectos y habilidades de forma elegante. Incluye secciones de presentación, proyectos destacados y información de contacto con un diseño moderno y responsivo.',
    '/favicon.ico',
    'completed',
    'https://github.com/DulceMar765/Discarr',
    'https://cesarolvrdz.dev',
    true,
    NOW(),
    NOW()
),

-- Proyecto 2: Discarr
(
    'Discarr, Pagina Web para una empresa de carroceria',
    'El proyecto Discarr es una aplicación web desarrollada en Laravel pensada para una empresa de carrocería. Su propósito es funcionar tanto como un sitio corporativo para los clientes (mostrar servicios, contacto y agendar citas), como una plataforma interna de gestión para la empresa.',
    '/favicon.ico',
    'in_progress',
    'https://github.com/cesarolvrdz/cms-laravel',
    null,
    true,
    NOW(),
    NOW()
),

-- Proyecto 3: Renders 3D
(
    'Creacion de Renders 3D para creación de contenido',
    'Galería de renders y animaciones creadas con Mine-Imator, mostrando proyectos creativos y habilidades de diseño 3D. Incluye escenas detalladas, efectos de iluminación avanzados y composiciones artísticas que demuestran experiencia en modelado y renderizado 3D.',
    '/favicon.ico',
    'in_progress',
    null,
    'https://cesarolvrdz.dev/renders',
    true,
    NOW(),
    NOW()
);";

        $this->line($insertSQL);

        $this->info('');
        $this->info('🎯 RECOMENDACIÓN:');
        $this->info('✅ Usa la OPCIÓN 2 (más simple)');
        $this->info('✅ Las tecnologías las puedes agregar después desde el admin');
        $this->info('✅ O usar la OPCIÓN 1 si quieres la columna technologies');

        return 0;
    }
}
