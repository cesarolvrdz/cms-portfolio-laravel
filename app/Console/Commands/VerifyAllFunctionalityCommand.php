<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseServiceOptimized;

class VerifyAllFunctionalityCommand extends Command
{
    protected $signature = 'supabase:verify-all';
    protected $description = 'Verificar que todas las funcionalidades del CMS funcionen correctamente';

    public function handle()
    {
        $this->info("=== VERIFICACIÓN COMPLETA DEL CMS ===");
        $this->line("");

        $allPassed = true;

        // 1. Verificar conectividad básica
        $this->info("🔗 1. Verificando conectividad con Supabase...");
        try {
            $supabase = app(SupabaseServiceOptimized::class);
            $projects = $supabase->getProjects();
            $this->info("   ✅ Conexión exitosa - Proyectos encontrados: " . count($projects));
        } catch (\Exception $e) {
            $this->error("   ❌ Error de conexión: " . $e->getMessage());
            $allPassed = false;
        }

        // 2. Verificar CRUD de proyectos
        $this->info("📋 2. Verificando operaciones CRUD de proyectos...");
        try {
            $this->call('test:crud');
            $this->info("   ✅ CRUD de proyectos funcionando");
        } catch (\Exception $e) {
            $this->error("   ❌ Error en CRUD: " . $e->getMessage());
            $allPassed = false;
        }

        // 3. Verificar subida de imágenes
        $this->info("🖼️ 3. Verificando subida de imágenes...");
        try {
            $this->call('test:web-image-upload');
            $this->info("   ✅ Subida de imágenes funcionando");
        } catch (\Exception $e) {
            $this->error("   ❌ Error en imágenes: " . $e->getMessage());
            $allPassed = false;
        }

        // 4. Verificar tech tags
        $this->info("🏷️ 4. Verificando tech tags...");
        try {
            $techTags = $supabase->getTechTags();
            $this->info("   ✅ Tech tags funcionando - Tags encontrados: " . count($techTags));
        } catch (\Exception $e) {
            $this->error("   ❌ Error en tech tags: " . $e->getMessage());
            $allPassed = false;
        }

        // 5. Verificar cache
        $this->info("⚡ 5. Verificando sistema de cache...");
        try {
            $stats = $supabase->getStats();
            $this->info("   ✅ Cache funcionando - Estadísticas: " . json_encode($stats));
        } catch (\Exception $e) {
            $this->error("   ❌ Error en cache: " . $e->getMessage());
            $allPassed = false;
        }

        $this->line("");
        $this->comment("═══════════════════════════════════════════");

        if ($allPassed) {
            $this->info("🎉 TODAS LAS VERIFICACIONES PASARON");
            $this->info("✅ Tu CMS está completamente funcional");
            $this->line("");
            $this->info("🚀 URLs disponibles:");
            $this->info("   • Panel admin: http://localhost:9000/admin");
            $this->info("   • Proyectos: http://localhost:9000/admin/projects");
            $this->info("   • Crear proyecto: http://localhost:9000/admin/projects/create");
            $this->info("   • Tech tags: http://localhost:9000/admin/tags");

        } else {
            $this->error("❌ ALGUNAS VERIFICACIONES FALLARON");
            $this->warn("Revisa los errores anteriores y asegúrate de:");
            $this->warn("1. Haber aplicado todas las políticas RLS en Supabase");
            $this->warn("2. Verificar la configuración en el archivo .env");
            $this->warn("3. Comprobar la conectividad con Supabase");
        }

        $this->line("");
        $this->comment("═══════════════════════════════════════════");
    }
}
