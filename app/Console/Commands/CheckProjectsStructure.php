<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseServiceOptimized;

class CheckProjectsStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:check-structure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica la estructura de la tabla projects en Supabase';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando estructura de la tabla projects...');
        $this->info('');

        $supabase = app(SupabaseServiceOptimized::class);

        try {
            // Intentar obtener proyectos para ver qué columnas hay
            $projects = $supabase->getProjects();

            if (!empty($projects)) {
                $this->info('✅ Tabla projects encontrada con datos:');
                $this->info('📊 Primer proyecto como ejemplo:');

                $firstProject = $projects[0];
                foreach ($firstProject as $column => $value) {
                    $displayValue = is_string($value) && strlen($value) > 50
                        ? substr($value, 0, 47) . '...'
                        : $value;
                    $this->info("   • {$column}: {$displayValue}");
                }
            } else {
                $this->warn('⚠️  No hay proyectos en la tabla, pero la tabla existe');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());

            $this->info('');
            $this->info('🔧 SQL para verificar estructura en Supabase:');
            $this->line("SELECT column_name, data_type, is_nullable");
            $this->line("FROM information_schema.columns");
            $this->line("WHERE table_schema = 'public' AND table_name = 'projects'");
            $this->line("ORDER BY ordinal_position;");
        }

        $this->info('');
        $this->info('📋 COLUMNAS ESPERADAS vs REALES:');

        $expectedColumns = [
            'id' => 'UUID PRIMARY KEY',
            'title' => 'VARCHAR/TEXT',
            'description' => 'TEXT',
            'image' => 'TEXT (URL)',
            'technologies' => 'JSONB (array de strings)', // ← Esta puede estar faltando
            'status' => 'VARCHAR',
            'github_url' => 'TEXT',
            'demo_url' => 'TEXT',
            'is_featured' => 'BOOLEAN',
            'created_at' => 'TIMESTAMP',
            'updated_at' => 'TIMESTAMP'
        ];

        foreach ($expectedColumns as $column => $type) {
            $this->info("   • {$column}: {$type}");
        }

        return 0;
    }
}
