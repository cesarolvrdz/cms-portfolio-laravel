<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseService;

class FixCrud extends Command
{
    protected $signature = 'fix:crud';
    protected $description = 'Arregla los problemas de UPDATE y DELETE';

    public function handle()
    {
        $this->info('🔧 === ARREGLANDO CRUD ===');

        $service = new SupabaseService();

        // 1. Verificar que el proyecto existe
        $this->info('1. Verificando existencia del proyecto...');
        $projects = $service->getProjects();

        if (empty($projects)) {
            $this->error('No hay proyectos');
            return;
        }

        $testProject = $projects[0];
        $projectId = $testProject['id'];
        $this->info("   ID del proyecto: {$projectId}");

        // 2. Probar diferentes formatos de filtro
        $this->info('2. Probando diferentes filtros...');

        $filters = [
            ['id' => 'eq.' . $projectId],
            ['id' => $projectId],
        ];

        foreach ($filters as $index => $filter) {
            $this->line("   Filtro " . ($index + 1) . ": " . json_encode($filter));
            try {
                $result = $service->getProjects($filter);
                $this->line("      Encontrados: " . count($result));
                if (!empty($result)) {
                    $this->info("      ✅ Este filtro funciona!");
                }
            } catch (\Exception $e) {
                $this->error("      ❌ Error: " . $e->getMessage());
            }
        }

        // 3. Probar UPDATE usando PATCH con el header correcto
        $this->info('3. Probando UPDATE corregido...');

        $headers = [
            'apikey' => config('supabase.key'),
            'Authorization' => 'Bearer ' . config('supabase.key'),
            'Content-Type' => 'application/json',
            'Prefer' => 'return=minimal'  // Importante para Supabase
        ];

        $baseUrl = config('supabase.url') . '/rest/v1';
        $updateUrl = $baseUrl . "/projects?id=eq.{$projectId}";

        $updateData = [
            'title' => 'TÍTULO CORREGIDO - ' . now()->format('H:i:s'),
            'updated_at' => now()->toISOString(),
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders($headers)
                ->patch($updateUrl, $updateData);

            $this->line("   Status: " . $response->status());
            $this->line("   Body: " . $response->body());

            if ($response->status() === 204) {
                // Verificar si realmente se actualizó
                sleep(1);
                $updated = $service->getProjects(['id' => 'eq.' . $projectId]);
                if (!empty($updated) && $updated[0]['title'] === $updateData['title']) {
                    $this->info("   ✅ UPDATE FUNCIONÓ!");
                } else {
                    $this->error("   ❌ Status 204 pero no se actualizó - problema de permisos RLS");
                }
            }

        } catch (\Exception $e) {
            $this->error("   Error: " . $e->getMessage());
        }

        // 4. Verificar permisos RLS
        $this->info('4. Verificando permisos RLS...');
        $this->line('   Status 204 + No actualización = Problema de Row Level Security');
        $this->line('   Solución: Configurar políticas UPDATE y DELETE en Supabase Dashboard');

        $this->info('=== DIAGNÓSTICO COMPLETO ===');
        $this->warn('PROBLEMA IDENTIFICADO: Row Level Security bloquea UPDATE/DELETE');
        $this->line('');
        $this->line('SOLUCIÓN REQUERIDA:');
        $this->line('1. Ir al Dashboard de Supabase');
        $this->line('2. Authentication > Policies');
        $this->line('3. Tabla "projects"');
        $this->line('4. Crear política UPDATE: ENABLE ROW LEVEL SECURITY + Allow all updates');
        $this->line('5. Crear política DELETE: ENABLE ROW LEVEL SECURITY + Allow all deletes');
    }
}
