<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseServiceOptimized;

class TestListProjectsCommand extends Command
{
    protected $signature = 'test:list-projects';
    protected $description = 'Listar todos los proyectos disponibles';

    public function handle()
    {
        $supabase = app(SupabaseServiceOptimized::class);

        $this->info("=== LISTANDO TODOS LOS PROYECTOS ===");

        try {
            $projects = $supabase->getProjects();
            $this->info("Proyectos encontrados: " . count($projects));

            foreach ($projects as $project) {
                $this->info("- ID: " . $project['id']);
                $this->info("  Título: " . $project['title']);
                $this->line(""); // Nueva línea
            }

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
