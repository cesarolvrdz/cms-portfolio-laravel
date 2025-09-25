<?php

namespace App\Console\Commands;

use App\Services\SupabaseServiceOptimized;
use App\Services\SupabaseStorageService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class TestAvatarUpload extends Command
{
    protected $signature = 'avatar:test-upload {image_path?}';
    protected $description = 'Test avatar upload functionality';

    public function handle()
    {
        $this->info('Testing avatar upload process...');

        // 1. Verificar configuración
        $this->info('1. Checking configuration...');
        $storageUrl = env('SUPABASE_URL');
        $serviceKey = env('SUPABASE_SERVICE_KEY');
        $bucket = env('SUPABASE_STORAGE_BUCKET');

        $this->line("Storage URL: $storageUrl");
        $this->line("Bucket: $bucket");
        $this->line("Service Key exists: " . ($serviceKey ? 'Yes' : 'No'));

        // 2. Verificar perfil actual
        $this->info("\n2. Checking current profile...");
        $supabase = new SupabaseServiceOptimized();
        $profile = $supabase->getProfile();

        if ($profile) {
            $this->line("Profile ID: " . ($profile['id'] ?? 'No ID'));
            $this->line("Current avatar_url: " . ($profile['avatar_url'] ?? 'NULL'));
            $this->line("Current avatar: " . ($profile['avatar'] ?? 'NULL'));
        } else {
            $this->error("No profile found");
            return Command::FAILURE;
        }

        // 3. Simular subida
        $this->info("\n3. Testing storage service...");
        $storage = new SupabaseStorageService();

        // Crear un archivo temporal de prueba
        $testImagePath = storage_path('app/test-avatar.png');
        if (!file_exists($testImagePath)) {
            $this->warn("No test image found. Create a test image at: $testImagePath");
            return Command::SUCCESS;
        }

        try {
            $testFile = new UploadedFile(
                $testImagePath,
                'test-avatar.png',
                'image/png',
                null,
                true // test mode
            );

            $this->info("Attempting to upload test image...");
            $avatarUrl = $storage->uploadProfileImage($testFile, $profile['id'] ?? 'test-user');

            if ($avatarUrl) {
                $this->info("✓ Upload successful! URL: $avatarUrl");

                // 4. Probar actualización del perfil
                $this->info("\n4. Testing profile update...");
                $updateData = ['avatar_url' => $avatarUrl];

                if ($supabase->updateProfile($updateData)) {
                    $this->info("✓ Profile updated successfully");
                } else {
                    $this->error("✗ Failed to update profile");
                }
            } else {
                $this->error("✗ Upload failed");
            }
        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . ":" . $e->getLine());
        }

        return Command::SUCCESS;
    }
}
