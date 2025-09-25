<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseService;

class DiagnoseSupabase extends Command
{
    protected $signature = 'supabase:diagnose';
    protected $description = 'Diagnostica la conexión y configuración de Supabase';

    public function handle()
    {
        $this->info('=== DIAGNÓSTICO SUPABASE ===');

        // 1. Verificar configuración
        $this->info('1. Verificando configuración...');
        $url = config('supabase.url');
        $key = config('supabase.key');
        $bucket = config('supabase.bucket');

        $this->line("URL: " . ($url ? 'Configurada' : 'FALTANTE'));
        $this->line("Key: " . ($key ? 'Configurada' : 'FALTANTE'));
        $this->line("Bucket: " . ($bucket ? $bucket : 'FALTANTE'));

        if (!$url || !$key) {
            $this->error('Configuración incompleta en .env');
            return;
        }

        // 2. Probar conexión
        $this->info('2. Probando conexión...');
        $service = new SupabaseService();

        try {
            $projects = $service->getProjects();
            $this->info("Conexión exitosa. Respuesta recibida.");

            if (is_array($projects)) {
                $this->info("Proyectos encontrados: " . count($projects));
                if (count($projects) > 0) {
                    $this->info("Primer proyecto: " . json_encode($projects[0]));
                }
            } else {
                $this->warn("Respuesta no es array: " . json_encode($projects));
            }

        } catch (\Exception $e) {
            $this->error("Error de conexión: " . $e->getMessage());
        }

        // 3. Probar inserción de proyecto
        $this->info('3. Probando inserción de proyecto...');
        $testData = [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'title' => 'Proyecto de Prueba - ' . now(),
            'description' => 'Este es un proyecto de prueba para diagnosticar',
            'status' => 'completed',
            'tech' => ['Test'],
            'image' => 'https://via.placeholder.com/300x200.png?text=Test',
            'link' => null,
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
        ];

        try {
            $result = $service->createProject($testData);
            $this->info("Inserción de proyecto exitosa. Resultado: " . json_encode($result));
        } catch (\Exception $e) {
            $this->error("Error en inserción de proyecto: " . $e->getMessage());
        }

        // 4. Probar tech tags
        $this->info('4. Probando tech tags...');
        try {
            $techTags = $service->getTechTags();
            $this->info("Tech tags obtenidos exitosamente. Total: " . (is_array($techTags) ? count($techTags) : 'N/A'));

            if (is_array($techTags) && count($techTags) > 0) {
                $this->info("Primer tech tag: " . json_encode($techTags[0]));
            }
        } catch (\Exception $e) {
            $this->error("Error obteniendo tech tags: " . $e->getMessage());
        }

        // 5. Probar creación de tech tag
        $this->info('5. Probando creación de tech tag...');
        $testTechTag = [
            'name' => 'Test Framework - ' . now()->format('H:i:s'),
            'color' => '#FF5733'
        ];

        try {
            $result = $service->createTechTag($testTechTag);
            $this->info("Creación de tech tag exitosa. Resultado: " . json_encode($result));
        } catch (\Exception $e) {
            $this->error("Error creando tech tag: " . $e->getMessage());
        }

        // 6. Probar funcionalidad de storage (simulada)
        $this->info('6. Probando configuración de storage...');
        try {
            $storageUrl = config('supabase.url') . '/storage/v1/object/public/' . config('supabase.bucket');
            $this->info("URL de storage configurada: " . $storageUrl);

            // Test de conectividad básica al endpoint de storage
            $response = \Illuminate\Support\Facades\Http::get(config('supabase.url') . '/storage/v1/buckets');
            if ($response->successful()) {
                $this->info("Endpoint de storage accesible");
            } else {
                $this->warn("Endpoint de storage no accesible: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->error("Error probando storage: " . $e->getMessage());
        }
    }
}
