<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseService;

class TestCrud extends Command
{
    protected $signature = 'test:crud';
    protected $description = 'Prueba las operaciones CRUD completas';

    public function handle()
    {
        $this->info('=== PRUEBA CRUD COMPLETA ===');

        $service = new SupabaseService();

        // 1. CREATE - Crear un proyecto de prueba
        $this->info('1. Probando CREATE...');
        $testProject = [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'title' => 'Proyecto CRUD Test - ' . now()->format('H:i:s'),
            'description' => 'Proyecto creado para probar operaciones CRUD',
            'status' => 'in-progress',
            'tech' => ['Laravel', 'Test'],
            'image' => 'https://via.placeholder.com/300x200.png?text=CRUD+Test',
            'link' => 'https://example.com',
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
        ];

        try {
            $createResult = $service->createProject($testProject);
            $this->info('   ✓ CREATE exitoso');
            $projectId = $testProject['id'];
        } catch (\Exception $e) {
            $this->error('   ✗ CREATE falló: ' . $e->getMessage());
            return;
        }

        // 2. READ - Leer proyectos
        $this->info('2. Probando READ...');
        try {
            $projects = $service->getProjects();
            $this->info('   ✓ READ exitoso - Proyectos encontrados: ' . count($projects));

            // Buscar nuestro proyecto específico
            $foundProject = null;
            foreach ($projects as $project) {
                if ($project['id'] === $projectId) {
                    $foundProject = $project;
                    break;
                }
            }

            if ($foundProject) {
                $this->info('   ✓ Proyecto de prueba encontrado: ' . $foundProject['title']);
            } else {
                $this->warn('   ⚠ Proyecto de prueba no encontrado');
            }

        } catch (\Exception $e) {
            $this->error('   ✗ READ falló: ' . $e->getMessage());
            return;
        }

        // 3. UPDATE - Actualizar el proyecto
        $this->info('3. Probando UPDATE...');
        $updateData = [
            'title' => 'Proyecto CRUD Test ACTUALIZADO - ' . now()->format('H:i:s'),
            'description' => 'Proyecto actualizado exitosamente',
            'status' => 'completed',
            'updated_at' => now()->toISOString(),
        ];

        try {
            $updateResult = $service->updateProject($projectId, $updateData);
            $this->info('   ✓ UPDATE exitoso');

            // Verificar que se actualizó
            $updatedProjects = $service->getProjects(['id' => 'eq.' . $projectId]);
            if (!empty($updatedProjects) && $updatedProjects[0]['title'] === $updateData['title']) {
                $this->info('   ✓ Verificación UPDATE exitosa');
            } else {
                $this->warn('   ⚠ UPDATE no se reflejó correctamente');
            }

        } catch (\Exception $e) {
            $this->error('   ✗ UPDATE falló: ' . $e->getMessage());
        }

        // 4. DELETE - Eliminar el proyecto
        $this->info('4. Probando DELETE...');
        try {
            $deleteResult = $service->deleteProject($projectId);
            $this->info('   ✓ DELETE exitoso');

            // Verificar que se eliminó
            $deletedCheck = $service->getProjects(['id' => 'eq.' . $projectId]);
            if (empty($deletedCheck)) {
                $this->info('   ✓ Verificación DELETE exitosa - Proyecto eliminado');
            } else {
                $this->warn('   ⚠ DELETE no se reflejó correctamente');
            }

        } catch (\Exception $e) {
            $this->error('   ✗ DELETE falló: ' . $e->getMessage());
        }

        $this->info('=== PRUEBA CRUD COMPLETADA ===');
    }
}
