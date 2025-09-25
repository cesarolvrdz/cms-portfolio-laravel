<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseServiceOptimized;
use Illuminate\Http\UploadedFile;

class TestWebImageUploadCommand extends Command
{
    protected $signature = 'test:web-image-upload';
    protected $description = 'Probar la subida de imágenes usando el servicio web optimizado';

    public function handle()
    {
        $supabase = app(SupabaseServiceOptimized::class);

        $this->info("=== PRUEBA DE SUBIDA WEB DE IMÁGENES ===");

        try {
            // Crear una imagen de prueba
            $this->info("1. Creando imagen de prueba...");
            $imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==');
            $tempFile = tempnam(sys_get_temp_dir(), 'web_test_image') . '.png';
            file_put_contents($tempFile, $imageContent);

            // Crear un mock UploadedFile
            $uploadedFile = new UploadedFile(
                $tempFile,
                'test_image.png',
                'image/png',
                null,
                true // test mode
            );

            $this->info("   ✓ Imagen de prueba creada");

            // 2. Probar subida usando el servicio
            $this->info("2. Probando subida con SupabaseServiceOptimized...");
            $filename = 'web_test_' . time() . '.png';

            $url = $supabase->uploadImage($uploadedFile, $filename);

            if ($url) {
                $this->info("   ✓ Subida exitosa!");
                $this->info("   ✓ URL: " . $url);

                // Verificar si es local o remoto
                if (str_contains($url, 'storage/images')) {
                    $this->warn("   → Imagen guardada LOCALMENTE (fallback)");
                } else {
                    $this->info("   → Imagen guardada en SUPABASE");
                }

            } else {
                $this->error("   ✗ Subida falló");
            }

            // Limpiar archivo temporal
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }

        $this->info("=== PRUEBA COMPLETADA ===");
    }
}
