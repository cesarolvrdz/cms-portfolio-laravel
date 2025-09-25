<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseService;
use App\Services\SupabaseServiceOptimized;

class TestSpeed extends Command
{
    protected $signature = 'test:speed';
    protected $description = 'Compara el rendimiento del servicio original vs optimizado';

    public function handle()
    {
        $this->info('⚡ === PRUEBA DE RENDIMIENTO ===');
        $this->newLine();

        // Test del servicio original
        $this->info('🐌 1. SERVICIO ORIGINAL:');
        $originalService = new SupabaseService();

        $start = microtime(true);
        $projects1 = $originalService->getProjects();
        $time1 = (microtime(true) - $start) * 1000;

        $start = microtime(true);
        $projects2 = $originalService->getProjects();
        $time2 = (microtime(true) - $start) * 1000;

        $this->line("   Primera carga: {$time1}ms");
        $this->line("   Segunda carga: {$time2}ms");
        $this->line("   Proyectos encontrados: " . count($projects1));

        $this->newLine();

        // Test del servicio optimizado
        $this->info('🚀 2. SERVICIO OPTIMIZADO:');
        $optimizedService = new SupabaseServiceOptimized();

        // Limpiar cache primero
        \Illuminate\Support\Facades\Cache::flush();

        $start = microtime(true);
        $projectsOpt1 = $optimizedService->getProjects();
        $timeOpt1 = (microtime(true) - $start) * 1000;

        $start = microtime(true);
        $projectsOpt2 = $optimizedService->getProjects(); // Esta debería usar cache
        $timeOpt2 = (microtime(true) - $start) * 1000;

        $this->line("   Primera carga: {$timeOpt1}ms");
        $this->line("   Segunda carga (cache): {$timeOpt2}ms");
        $this->line("   Proyectos encontrados: " . count($projectsOpt1));

        $this->newLine();

        // Comparación
        $this->info('📊 3. COMPARACIÓN:');
        $improvement1 = $time1 > 0 ? (($time1 - $timeOpt1) / $time1) * 100 : 0;
        $improvement2 = $time2 > 0 ? (($time2 - $timeOpt2) / $time2) * 100 : 0;

        $this->line("   Mejora primera carga: " . round($improvement1, 1) . "%");
        $this->line("   Mejora segunda carga: " . round($improvement2, 1) . "%");

        if ($timeOpt2 < 10) {
            $this->info("   ✅ Cache funcionando correctamente!");
        }

        $this->newLine();

        // Test de estadísticas optimizadas
        $this->info('📈 4. ESTADÍSTICAS OPTIMIZADAS:');
        $start = microtime(true);
        $stats = $optimizedService->getStats();
        $statsTime = (microtime(true) - $start) * 1000;

        $this->line("   Tiempo de carga: {$statsTime}ms");
        $this->line("   Total proyectos: {$stats['total_projects']}");
        $this->line("   Completados: {$stats['completed_projects']}");
        $this->line("   En progreso: {$stats['in_progress_projects']}");

        $this->newLine();
        $this->info('🎯 RECOMENDACIONES:');

        if ($timeOpt2 < $time2 * 0.5) {
            $this->info('   ✅ Cache está funcionando excelente');
        } else {
            $this->warn('   ⚠️ Considera configurar Redis para mejor cache');
        }

        if ($timeOpt1 < $time1 * 0.8) {
            $this->info('   ✅ Timeouts optimizados funcionando');
        } else {
            $this->warn('   ⚠️ Conexión a Supabase puede ser lenta');
        }

        $this->newLine();
        $this->info('💡 PRÓXIMOS PASOS PARA MEJORAR VELOCIDAD:');
        $this->line('   1. Usar el servicio optimizado en todos los controladores');
        $this->line('   2. Configurar Redis para cache persistente');
        $this->line('   3. Implementar lazy loading en imágenes');
        $this->line('   4. Activar compresión gzip en servidor');
        $this->line('   5. Considerar CDN para assets estáticos');
    }
}
