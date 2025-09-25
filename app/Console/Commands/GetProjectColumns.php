<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetProjectColumns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:get-columns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtiene las columnas reales de la tabla projects desde Supabase';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Obteniendo columnas reales de la tabla projects...');
        $this->info('');

        $url = config('supabase.url') . '/rest/v1';
        $key = config('supabase.key');

        $headers = [
            'apikey' => $key,
            'Authorization' => 'Bearer ' . $key,
            'Content-Type' => 'application/json',
        ];

        try {
            // Obtener un proyecto para ver las columnas
            $response = Http::withHeaders($headers)
                ->get($url . '/projects?limit=1');

            if ($response->successful()) {
                $projects = $response->json();

                if (!empty($projects)) {
                    $this->info('✅ Columnas encontradas en la tabla projects:');
                    $project = $projects[0];
                    $columns = array_keys($project);

                    foreach ($columns as $column) {
                        $this->info("   • {$column}");
                    }

                    $this->info('');
                    $this->info('📋 SQL CORREGIDO basado en columnas reales:');
                    $this->generateSQL($columns);

                } else {
                    $this->warn('⚠️  La tabla existe pero no tiene datos para verificar columnas');
                }
            } else {
                $this->error('❌ Error al conectar con Supabase: ' . $response->body());
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }

        return 0;
    }

    private function generateSQL($columns)
    {
        // Mapear los datos a las columnas disponibles
        $columnList = implode(', ', array_filter($columns, function($col) {
            return !in_array($col, ['id', 'created_at', 'updated_at']);
        }));

        $sql = "-- SQL CORREGIDO basado en columnas reales
TRUNCATE TABLE public.projects CASCADE;

INSERT INTO public.projects ({$columnList}, created_at, updated_at) VALUES";

        // Generar los VALUES basado en las columnas disponibles
        $projects = [
            [
                'title' => 'Portafolio Personal',
                'description' => 'Sitio web profesional desarrollado con Astro y Tailwind CSS, diseñado para mostrar proyectos y habilidades de forma elegante.',
                'image' => '/favicon.ico',
                'status' => 'completed',
                'github' => 'https://github.com/DulceMar765/Discarr',
                'demo' => 'https://cesarolvrdz.dev',
                'featured' => true
            ],
            [
                'title' => 'Discarr - Empresa de Carrocería',
                'description' => 'Aplicación web desarrollada en Laravel para una empresa de carrocería.',
                'image' => '/favicon.ico',
                'status' => 'in_progress',
                'github' => 'https://github.com/cesarolvrdz/cms-laravel',
                'demo' => null,
                'featured' => true
            ],
            [
                'title' => 'Renders 3D con Mine-Imator',
                'description' => 'Galería de renders y animaciones 3D creadas con Mine-Imator.',
                'image' => '/favicon.ico',
                'status' => 'in_progress',
                'github' => null,
                'demo' => 'https://cesarolvrdz.dev/renders',
                'featured' => true
            ]
        ];

        foreach ($projects as $index => $project) {
            $values = [];
            foreach ($columns as $column) {
                if (in_array($column, ['id', 'created_at', 'updated_at'])) {
                    continue;
                }

                $value = match($column) {
                    'title' => "'{$project['title']}'",
                    'description' => "'{$project['description']}'",
                    'image' => "'{$project['image']}'",
                    'status' => "'{$project['status']}'",
                    'github', 'github_url' => $project['github'] ? "'{$project['github']}'" : 'null',
                    'demo', 'demo_url' => $project['demo'] ? "'{$project['demo']}'" : 'null',
                    'featured', 'is_featured' => $project['featured'] ? 'true' : 'false',
                    default => "''"
                };

                $values[] = $value;
            }

            $valueString = implode(', ', $values) . ', NOW(), NOW()';
            $sql .= "\n(" . $valueString . ")" . ($index < count($projects) - 1 ? ',' : ';');
        }

        $this->line($sql);
    }
}
