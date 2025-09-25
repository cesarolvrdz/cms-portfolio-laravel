<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseService;

class DebugCrud extends Command
{
    protected $signature = 'debug:crud';
    protected $description = 'Debug detallado de operaciones UPDATE y DELETE';

    public function handle()
    {
        $this->info('🔍 === DEBUG CRUD OPERATIONS ===');

        $service = new SupabaseService();

        // 1. Obtener un proyecto existente para probar
        $this->info('1. Obteniendo proyectos existentes...');
        $projects = $service->getProjects();

        if (empty($projects)) {
            $this->error('No hay proyectos para probar');
            return;
        }

        $testProject = $projects[0];
        $projectId = $testProject['id'];

        $this->info("   Proyecto seleccionado: {$testProject['title']} (ID: {$projectId})");
        $this->info("   Status actual: {$testProject['status']}");

        // 2. Probar UPDATE con detalles completos
        $this->info('2. Probando UPDATE detallado...');

        $originalTitle = $testProject['title'];
        $newTitle = "TITULO ACTUALIZADO - " . now()->format('H:i:s');

        $updateData = [
            'title' => $newTitle,
            'updated_at' => now()->toISOString(),
        ];

        $this->line("   Datos a actualizar: " . json_encode($updateData, JSON_PRETTY_PRINT));

        try {
            // Hacer el UPDATE
            $updateResult = $service->updateProject($projectId, $updateData);
            $this->line("   Respuesta UPDATE: " . json_encode($updateResult, JSON_PRETTY_PRINT));

            // Verificar inmediatamente
            sleep(2); // Esperar un poco por si hay demora
            $updatedProjects = $service->getProjects(['id' => 'eq.' . $projectId]);

            if (!empty($updatedProjects)) {
                $updatedProject = $updatedProjects[0];
                $this->line("   Título antes: {$originalTitle}");
                $this->line("   Título después: {$updatedProject['title']}");

                if ($updatedProject['title'] === $newTitle) {
                    $this->info("   ✅ UPDATE funcionó correctamente!");
                } else {
                    $this->error("   ❌ UPDATE no se aplicó. Título sigue igual.");
                    $this->line("   Esperado: {$newTitle}");
                    $this->line("   Actual: {$updatedProject['title']}");
                }
            } else {
                $this->error("   ❌ No se pudo verificar el proyecto después del UPDATE");
            }

        } catch (\Exception $e) {
            $this->error("   ERROR UPDATE: " . $e->getMessage());
        }

        // 3. Probar los headers HTTP
        $this->info('3. Verificando configuración HTTP...');
        $url = config('supabase.url');
        $key = config('supabase.key');
        $this->line("   URL: " . ($url ? 'Configurada' : 'FALTANTE'));
        $this->line("   Key: " . ($key ? 'Configurada (' . substr($key, 0, 10) . '...)' : 'FALTANTE'));

        // 4. Probar URL de construcción
        $this->info('4. Verificando URLs construidas...');
        $baseUrl = config('supabase.url') . '/rest/v1';
        $updateUrl = $baseUrl . "/projects?id=eq.{$projectId}";
        $this->line("   Base URL: {$baseUrl}");
        $this->line("   Update URL: {$updateUrl}");

        // 5. Test manual con HTTP facade
        $this->info('5. Test manual con HTTP facade...');
        try {
            $headers = [
                'apikey' => config('supabase.key'),
                'Authorization' => 'Bearer ' . config('supabase.key'),
                'Content-Type' => 'application/json',
            ];

            $response = \Illuminate\Support\Facades\Http::withHeaders($headers)
                ->patch($updateUrl, ['title' => 'TEST MANUAL - ' . now()->format('H:i:s')]);

            $this->line("   Status Code: " . $response->status());
            $this->line("   Response Body: " . $response->body());

        } catch (\Exception $e) {
            $this->error("   Error en test manual: " . $e->getMessage());
        }

        $this->info('=== FIN DEBUG ===');
    }
}
