<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseServiceOptimized;

class TestEditCommand extends Command
{
    protected $signature = 'test:edit {id}';
    protected $description = 'Probar el edit de un proyecto específico';

    public function handle()
    {
        $id = $this->argument('id');
        $supabase = app(SupabaseServiceOptimized::class);

        $this->info("=== PROBANDO EDIT PARA ID: {$id} ===");

        try {
            // Probar la consulta directa
            $projects = $supabase->getProjects(['id' => 'eq.' . $id]);
            $this->info("Proyectos encontrados: " . count($projects));

            if (empty($projects)) {
                $this->error("No se encontró el proyecto");
                return;
            }

            $project = $projects[0];
            $this->info("Proyecto encontrado:");
            $this->info("- ID: " . $project['id']);
            $this->info("- Título: " . $project['title']);
            $this->info("- Descripción: " . substr($project['description'], 0, 50) . '...');

            // Verificar todas las claves
            $this->info("Claves disponibles: " . implode(', ', array_keys($project)));

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
    }
}
