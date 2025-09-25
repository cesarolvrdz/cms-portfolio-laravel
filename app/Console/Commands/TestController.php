<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\ProjectAdminController;
use Illuminate\Http\Request;

class TestController extends Command
{
    protected $signature = 'test:controller';
    protected $description = 'Prueba los controladores del CMS';

    public function handle()
    {
        $this->info('=== PRUEBA DE FUNCIONALIDADES CMS ===');

        // 1. Probar servicio de Supabase
        $this->info('1. Probando SupabaseService...');

        try {
            $service = new \App\Services\SupabaseService();

            // Probar obtener proyectos
            $this->info('   - Obteniendo proyectos...');
            $projects = $service->getProjects();

            if (is_array($projects)) {
                $this->info('   ✓ Proyectos obtenidos: ' . count($projects) . ' encontrados');
                if (count($projects) > 0) {
                    $this->info('   ✓ Primer proyecto: ' . $projects[0]['title']);
                }
            } else {
                $this->warn('   ⚠ Respuesta inesperada: ' . gettype($projects));
            }

            // Probar obtener tech tags
            $this->info('   - Obteniendo tech tags...');
            $techTags = $service->getTechTags();

            if (is_array($techTags)) {
                $this->info('   ✓ Tech tags obtenidos: ' . count($techTags) . ' encontrados');
            } else {
                $this->warn('   ⚠ Tech tags respuesta inesperada: ' . gettype($techTags));
            }

        } catch (\Exception $e) {
            $this->error("   ✗ Error en SupabaseService: " . $e->getMessage());
        }

        // 2. Probar que las vistas existen
        $this->info('2. Verificando archivos de vistas...');

        $views = [
            'resources/views/layouts/admin.blade.php' => 'Layout admin',
            'resources/views/admin/projects/index.blade.php' => 'Lista de proyectos',
            'resources/views/admin/projects/create.blade.php' => 'Crear proyecto',
            'resources/views/admin/projects/edit.blade.php' => 'Editar proyecto',
            'resources/views/admin/tags/index.blade.php' => 'Lista de tech tags'
        ];

        foreach ($views as $path => $description) {
            $fullPath = base_path($path);
            if (file_exists($fullPath)) {
                $this->info("   ✓ $description existe");
            } else {
                $this->error("   ✗ $description NO ENCONTRADA: $path");
            }
        }

        // 3. Probar configuración
        $this->info('3. Verificando configuración...');

        $configs = [
            'supabase.url' => config('supabase.url'),
            'supabase.key' => config('supabase.key'),
            'supabase.bucket' => config('supabase.bucket'),
        ];

        foreach ($configs as $key => $value) {
            if ($value) {
                $this->info("   ✓ $key configurado");
            } else {
                $this->error("   ✗ $key NO configurado");
            }
        }

        $this->info('=== PRUEBA COMPLETADA ===');
    }
}
