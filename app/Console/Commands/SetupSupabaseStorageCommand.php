<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupSupabaseStorageCommand extends Command
{
    protected $signature = 'supabase:setup-storage';
    protected $description = 'Configurar políticas RLS para Supabase Storage';

    public function handle()
    {
        $this->info("=== CONFIGURACIÓN DE SUPABASE STORAGE ===");
        $this->line("");

        $this->info("Para configurar el storage correctamente, necesitas ejecutar estos comandos SQL en tu dashboard de Supabase:");
        $this->line("");

        $this->warn("1. Ve a: https://supabase.com/dashboard/project/[TU_PROJECT_ID]/sql");
        $this->line("");

        $this->info("2. Ejecuta estos comandos SQL:");
        $this->line("");

        $sql = '
-- Habilitar RLS en el bucket si no está habilitado
UPDATE storage.buckets
SET public = true
WHERE id = \'portfolio-images\';

-- Política para permitir INSERT (subida de archivos)
CREATE POLICY "Allow public insert" ON storage.objects
FOR INSERT WITH CHECK (bucket_id = \'portfolio-images\');

-- Política para permitir SELECT (lectura de archivos)
CREATE POLICY "Allow public select" ON storage.objects
FOR SELECT USING (bucket_id = \'portfolio-images\');

-- Política para permitir UPDATE (actualización de archivos)
CREATE POLICY "Allow public update" ON storage.objects
FOR UPDATE USING (bucket_id = \'portfolio-images\');

-- Política para permitir DELETE (eliminación de archivos)
CREATE POLICY "Allow public delete" ON storage.objects
FOR DELETE USING (bucket_id = \'portfolio-images\');';

        $this->line($sql);

        $this->line("");
        $this->info("3. También puedes crear el bucket si no existe:");
        $this->line("");

        $createBucket = '
-- Crear el bucket si no existe
INSERT INTO storage.buckets (id, name, public)
VALUES (\'portfolio-images\', \'portfolio-images\', true)
ON CONFLICT (id) DO NOTHING;';

        $this->line($createBucket);

        $this->line("");
        $this->warn("IMPORTANTE: Estas políticas permiten acceso público total al bucket.");
        $this->warn("Para producción, considera políticas más restrictivas basadas en autenticación.");

        $this->line("");
        $this->info("Una vez ejecutados estos comandos, ejecuta:");
        $this->info("php artisan test:image-upload");
    }
}
