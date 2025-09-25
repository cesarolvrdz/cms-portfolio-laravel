<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    protected $storageUrl;
    protected $bucketName;
    protected $headers;

    public function __construct()
    {
        $this->storageUrl = env('SUPABASE_URL') . '/storage/v1/object';
        $this->bucketName = env('SUPABASE_STORAGE_BUCKET', 'portfolio-images');
        $this->headers = [
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Subir archivo de imagen de perfil
     */
    public function uploadProfileImage(UploadedFile $file, $userId): ?string
    {
        try {
            // Generar nombre único para el archivo
            $extension = $file->getClientOriginalExtension();
            $filename = "profile_{$userId}_" . time() . ".{$extension}";
            $path = "profiles/{$filename}";

            // Leer el contenido del archivo
            $fileContent = file_get_contents($file->getRealPath());

            $uploadUrl = $this->storageUrl . "/{$this->bucketName}/{$path}";

            // Subir a Supabase Storage
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
                'Content-Type' => $file->getMimeType(),
            ])->withBody($fileContent, $file->getMimeType())
            ->put($uploadUrl);

            if ($response->successful()) {
                // Retornar URL pública del archivo
                return $this->getPublicUrl($path);
            } else {
                Log::error('Error al subir imagen de perfil', [
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Excepción al subir imagen de perfil', [
                'message' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            return null;
        }
    }

    /**
     * Obtener URL pública del archivo
     */
    public function getPublicUrl(string $path): string
    {
        return env('SUPABASE_URL') . "/storage/v1/object/public/{$this->bucketName}/{$path}";
    }

    /**
     * Eliminar archivo de imagen
     */
    public function deleteFile(string $path): bool
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->delete($this->storageUrl . "/{$this->bucketName}/{$path}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Error al eliminar archivo', [
                'path' => $path,
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Validar que el archivo sea una imagen válida
     */
    public static function validateImage(UploadedFile $file): array
    {
        $maxSize = 2048; // 2MB en KB
        $allowedTypes = ['jpeg', 'jpg', 'png', 'gif', 'webp'];

        $errors = [];

        // Validar tamaño
        if ($file->getSize() > $maxSize * 1024) {
            $errors[] = 'La imagen debe pesar menos de 2MB.';
        }

        // Validar tipo
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedTypes)) {
            $errors[] = 'Solo se permiten archivos: ' . implode(', ', $allowedTypes);
        }

        // Validar que sea realmente una imagen
        if (!getimagesize($file->getRealPath())) {
            $errors[] = 'El archivo debe ser una imagen válida.';
        }

        return $errors;
    }
}
