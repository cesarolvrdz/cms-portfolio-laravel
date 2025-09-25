<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseServiceOptimized;

class TestWebDelete extends Command
{
    protected $signature = 'test:web-delete';
    protected $description = 'Simula eliminación desde la interfaz web';

    public function handle()
    {
        $this->info('🌐 === PRUEBA DE ELIMINACIÓN WEB ===');
        $this->newLine();

        $service = new SupabaseServiceOptimized();
        $projects = $service->getProjects();

        if (empty($projects)) {
            $this->error('No hay proyectos para probar');
            return;
        }

        $testProject = $projects[0];
        $projectId = $testProject['id'];

        $this->info("Proyecto a eliminar: {$testProject['title']}");
        $this->info("ID: {$projectId}");
        $this->newLine();

        if (!$this->confirm('¿Simular eliminación desde controlador web?')) {
            return;
        }

        // Simular el proceso del controlador web
        $this->info('1. Simulando ProjectAdminController@destroy...');

        try {
            // Paso 1: Llamar deleteProject
            $this->line('   - Llamando deleteProject...');
            $result = $service->deleteProject($projectId);
            $this->line('   - Resultado: ' . json_encode($result));

            // Paso 2: Esperar (como en el controlador)
            $this->line('   - Esperando 1 segundo...');
            sleep(1);

            // Paso 3: Verificar
            $this->line('   - Verificando eliminación...');
            $check = $service->getProjects(['id' => 'eq.' . $projectId], false);

            if (empty($check)) {
                $this->info('   ✅ ELIMINACIÓN EXITOSA desde interfaz web simulada');
                $message = 'Proyecto eliminado exitosamente';
                $type = 'success';
            } else {
                $this->error('   ❌ ELIMINACIÓN FALLÓ - proyecto aún existe');
                $message = 'No se pudo eliminar el proyecto. Verifica los permisos.';
                $type = 'error';
            }

            $this->newLine();
            $this->info("Mensaje que verías en la interfaz:");
            if ($type === 'success') {
                $this->info("✅ $message");
            } else {
                $this->error("❌ $message");
            }

        } catch (\Exception $e) {
            $this->error('   ❌ EXCEPCIÓN: ' . $e->getMessage());
            $this->error("❌ Error al eliminar: " . $e->getMessage());
        }

        $this->newLine();
        $this->info('📊 Estado final:');
        $finalProjects = $service->getProjects();
        $this->line("Total proyectos: " . count($finalProjects));

        $this->newLine();
        $this->info('🎯 Próximos pasos:');
        $this->line('   1. Los cambios en el controlador ya están aplicados');
        $this->line('   2. Prueba eliminar desde: http://localhost:9000/admin/projects');
        $this->line('   3. Ahora deberías ver mensajes más específicos');
    }
}
