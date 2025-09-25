<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseServiceOptimized;
use Illuminate\Http\UploadedFile;

class TestImageUploadCommand extends Command
{
    protected $signature = 'test:image-upload';
    protected $description = 'Probar la subida de imágenes a Supabase';

    public function handle()
    {
        $supabase = app(SupabaseServiceOptimized::class);

        $this->info("=== DIAGNÓSTICO DE SUBIDA DE IMÁGENES ===");

        // 1. Verificar configuración
        $this->info("1. Verificando configuración...");
        $this->info("   - URL: " . config('supabase.url'));
        $this->info("   - Bucket: " . config('supabase.bucket'));
        $this->info("   - Key disponible: " . (config('supabase.key') ? 'Sí' : 'No'));

        // 2. Crear una imagen de prueba simple
        $this->info("2. Creando imagen de prueba...");

        try {
            // Crear una imagen simple en memoria
            $imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==');
            $tempFile = tempnam(sys_get_temp_dir(), 'test_image') . '.png';
            file_put_contents($tempFile, $imageContent);

            $this->info("   ✓ Imagen de prueba creada: " . $tempFile);

            // 3. Probar subida directa con curl
            $this->info("3. Probando conectividad con Supabase Storage...");

            $bucket = config('supabase.bucket');
            $filename = 'test_' . time() . '.png';
            $url = config('supabase.url') . "/storage/v1/object/$bucket/$filename";

            $this->info("   - URL de subida: " . $url);

            $client = new \GuzzleHttp\Client([
                'timeout' => 30,
                'verify' => false // Desactivar verificación SSL para pruebas
            ]);

            $response = $client->post($url, [
                'headers' => [
                    'apikey' => config('supabase.key'),
                    'Authorization' => 'Bearer ' . config('supabase.key'),
                    'Content-Type' => 'image/png',
                ],
                'body' => file_get_contents($tempFile),
            ]);

            $this->info("   - Status Code: " . $response->getStatusCode());
            $this->info("   - Response: " . $response->getBody());

            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                $publicUrl = config('supabase.url') . "/storage/v1/object/public/$bucket/$filename";
                $this->info("   ✓ Imagen subida exitosamente");
                $this->info("   ✓ URL pública: " . $publicUrl);

                // Probar acceso a la URL pública
                $this->info("4. Verificando acceso público...");
                try {
                    $publicResponse = $client->get($publicUrl);
                    $this->info("   ✓ Imagen accesible públicamente (Status: " . $publicResponse->getStatusCode() . ")");
                } catch (\Exception $e) {
                    $this->error("   ✗ Error accediendo a la imagen pública: " . $e->getMessage());
                }

            } else {
                $this->error("   ✗ Error en la subida");
            }

            // Limpiar archivo temporal
            unlink($tempFile);

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("Detalles: " . $e->getTraceAsString());
        }
    }
}
