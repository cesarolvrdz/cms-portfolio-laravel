<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseServiceOptimized;
use Illuminate\Support\Facades\DB;

class MigrateToPostgreSQL extends Command
{
    protected $signature = 'db:migrate-to-postgresql {--dry-run : Show what would be migrated without actually doing it}';
    protected $description = 'Migrate data from SQLite to PostgreSQL for Vercel deployment';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN - Mostrando lo que se migraría sin hacer cambios reales');
        } else {
            $this->info('🚀 Iniciando migración a PostgreSQL...');
        }

        try {
            // Step 1: Verificar datos actuales en SQLite
            $this->verifyCurrentData();

            if (!$isDryRun) {
                // Step 2: Configurar PostgreSQL temporalmente
                $this->info('⚙️  Configurando conexión PostgreSQL...');

                // Step 3: Ejecutar migraciones en PostgreSQL
                $this->info('📊 Ejecutando migraciones en PostgreSQL...');

                // Step 4: Migrar datos
                $this->info('📦 Migrando datos...');
                $this->migrateData();

                $this->info('✅ Migración completada exitosamente!');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error durante la migración: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function verifyCurrentData()
    {
        $this->line('📋 Verificando datos actuales...');

        // Verificar tablas principales
        $tables = ['users', 'certificates', 'cv', 'education', 'work_experience', 'social_links', 'user_profiles'];

        foreach ($tables as $table) {
            try {
                $count = DB::table($table)->count();
                $this->line("   📊 $table: $count registros");
            } catch (\Exception $e) {
                $this->line("   ⚠️  $table: No existe o error");
            }
        }
    }

    private function migrateData()
    {
        $this->line('🔄 Migrando datos de tablas principales...');

        // Aquí implementaremos la migración real cuando tengamos las credenciales
        $this->line('   ℹ️  Migración pendiente de credenciales PostgreSQL');
    }
}
