<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseServiceOptimized;

class TestWebDeleteCommand extends Command
{
    protected $signature = 'test:web-delete';
    protected $description = 'Probar el flujo completo de eliminación web';

    public function handle()
    {
        $supabase = app(SupabaseServiceOptimized::class);

        $this->info("=== PROBANDO FLUJO COMPLETO DE ELIMINACIÓN WEB ===");

        try {
            // 1. Crear un proyecto de prueba
            $this->info("1. Creando proyecto de prueba...");
            $data = [
                'title' => 'Test Delete Project',
                'description' => 'Proyecto para probar eliminación web',
                'status' => 'completed',
                'tech' => 'Test',
                'link' => null,
                'image' => null
            ];

            $result = $supabase->createProject($data);
            if (!$result) {
                $this->error("Error al crear proyecto de prueba");
                return;
            }
            $this->info("   ✓ Proyecto creado exitosamente");

            // 2. Obtener lista actual
            $this->info("2. Obteniendo lista de proyectos...");
            $projectsBefore = $supabase->getProjects();
            $this->info("   ✓ Proyectos encontrados: " . count($projectsBefore));

            // Buscar el proyecto recién creado
            $testProject = null;
            foreach ($projectsBefore as $project) {
                if ($project['title'] === 'Test Delete Project') {
                    $testProject = $project;
                    break;
                }
            }

            if (!$testProject) {
                $this->error("No se encontró el proyecto de prueba");
                return;
            }
            $this->info("   ✓ Proyecto de prueba encontrado: " . $testProject['id']);

            // 3. Simular eliminación con cache flush
            $this->info("3. Eliminando proyecto...");
            \Illuminate\Support\Facades\Cache::flush(); // Limpiar cache antes
            $deleteResult = $supabase->deleteProject($testProject['id']);
            \Illuminate\Support\Facades\Cache::flush(); // Limpiar cache después

            if (!$deleteResult) {
                $this->error("Error al eliminar proyecto");
                return;
            }
            $this->info("   ✓ Proyecto eliminado exitosamente");

            // 4. Verificar que se eliminó
            $this->info("4. Verificando eliminación...");
            $projectsAfter = $supabase->getProjects();
            $this->info("   ✓ Proyectos después de eliminación: " . count($projectsAfter));

            // Buscar si el proyecto sigue existiendo
            $stillExists = false;
            foreach ($projectsAfter as $project) {
                if ($project['id'] === $testProject['id']) {
                    $stillExists = true;
                    break;
                }
            }

            if ($stillExists) {
                $this->error("El proyecto no fue eliminado correctamente");
            } else {
                $this->info("   ✓ Proyecto eliminado correctamente de la base de datos");
            }

            $this->info("=== PRUEBA COMPLETADA ===");
            $this->info("Proyectos antes: " . count($projectsBefore));
            $this->info("Proyectos después: " . count($projectsAfter));
            $this->info("Diferencia: " . (count($projectsBefore) - count($projectsAfter)));

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
    }
}
