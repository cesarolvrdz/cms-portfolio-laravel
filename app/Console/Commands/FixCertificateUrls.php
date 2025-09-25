<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseServiceOptimized;

class FixCertificateUrls extends Command
{
    protected $signature = 'certificates:fix-urls';
    protected $description = 'Fix certificate URLs for proper access';

    protected $supabaseService;

    public function __construct(SupabaseServiceOptimized $supabaseService)
    {
        parent::__construct();
        $this->supabaseService = $supabaseService;
    }

    public function handle()
    {
        $this->info('🔧 Corrigiendo URLs de certificados...');

        try {
            $certificates = $this->supabaseService->getCertificates();
            $fixed = 0;
            $total = count($certificates);

            foreach ($certificates as $cert) {
                if (!empty($cert['pdf_url']) && str_contains($cert['pdf_url'], 'supabase.co')) {
                    $originalUrl = $cert['pdf_url'];

                    // Verificar si necesita corrección
                    $fixedUrl = $this->fixUrl($originalUrl);

                    if ($originalUrl !== $fixedUrl) {
                        $this->line("📝 Corrigiendo: {$cert['title']}");
                        $this->line("   Antes: $originalUrl");
                        $this->line("   Después: $fixedUrl");

                        if ($this->updateCertificateUrl($cert['id'], $fixedUrl)) {
                            $fixed++;
                            $this->line("   ✅ Actualizado");
                        } else {
                            $this->line("   ❌ Error al actualizar");
                        }
                        $this->line("");
                    } else {
                        $this->line("✅ OK: {$cert['title']}");
                    }
                }
            }

            $this->info("📊 Resumen:");
            $this->info("   Total certificados: $total");
            $this->info("   URLs corregidas: $fixed");

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }

        return 0;
    }

    private function fixUrl($url)
    {
        // Convertir URL de Supabase a formato público correcto
        $baseUrl = config('supabase.url');

        // Si ya es una URL pública, mantenerla
        if (str_contains($url, '/storage/v1/object/public/')) {
            return $url;
        }

        // Si es una URL de storage normal, convertirla a pública
        if (str_contains($url, '/storage/v1/object/')) {
            $path = explode('/storage/v1/object/', $url)[1];
            return $baseUrl . '/storage/v1/object/public/' . $path;
        }

        // Si es solo el path, construir URL completa
        if (!str_contains($url, 'http')) {
            return $baseUrl . '/storage/v1/object/public/certificates/' . $url;
        }

        return $url;
    }

    private function updateCertificateUrl($id, $newUrl)
    {
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => false]);

            $response = $client->patch(config('supabase.url') . '/rest/v1/certificates', [
                'headers' => [
                    'apikey' => config('supabase.service_key'),
                    'Authorization' => 'Bearer ' . config('supabase.service_key'),
                    'Content-Type' => 'application/json',
                    'Prefer' => 'return=minimal'
                ],
                'query' => ['id' => 'eq.' . $id],
                'json' => ['pdf_url' => $newUrl]
            ]);

            return $response->getStatusCode() === 204;

        } catch (\Exception $e) {
            $this->error("Error actualizando certificado $id: " . $e->getMessage());
            return false;
        }
    }
}
