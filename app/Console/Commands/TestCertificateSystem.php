<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseServiceOptimized;

class TestCertificateSystem extends Command
{
    protected $signature = 'certificates:test-system';
    protected $description = 'Test certificate system functionality';

    protected $supabaseService;

    public function __construct(SupabaseServiceOptimized $supabaseService)
    {
        parent::__construct();
        $this->supabaseService = $supabaseService;
    }

    public function handle()
    {
        $this->info('🧪 Probando sistema completo de certificados...');

        // Test 1: Listar certificados
        $this->testGetCertificates();

        // Test 2: Verificar URLs de PDFs
        $this->testPdfUrls();

        // Test 3: Verificar buckets
        $this->testBucketAccess();

        $this->info('✅ Pruebas completadas');
        return 0;
    }

    private function testGetCertificates()
    {
        $this->line('🔍 1. Probando obtención de certificados...');

        try {
            $certificates = $this->supabaseService->getCertificates();
            $this->line("   📊 Encontrados: " . count($certificates) . " certificados");

            $withPdf = 0;
            $withSupabasePdf = 0;

            foreach ($certificates as $cert) {
                if (!empty($cert['pdf_url'])) {
                    $withPdf++;
                    if (str_contains($cert['pdf_url'], 'supabase.co')) {
                        $withSupabasePdf++;
                    }
                }
            }

            $this->line("   📄 Con PDF: $withPdf");
            $this->line("   ☁️  En Supabase: $withSupabasePdf");

        } catch (\Exception $e) {
            $this->error("   ❌ Error: " . $e->getMessage());
        }
    }

    private function testPdfUrls()
    {
        $this->line('🔗 2. Verificando acceso a PDFs...');

        try {
            $certificates = $this->supabaseService->getCertificates();
            $tested = 0;
            $accessible = 0;

            foreach ($certificates as $cert) {
                if (!empty($cert['pdf_url']) && str_contains($cert['pdf_url'], 'supabase.co')) {
                    $tested++;

                    if ($this->checkUrlAccess($cert['pdf_url'])) {
                        $accessible++;
                    } else {
                        $this->line("   ⚠️  No accesible: {$cert['title']}");
                    }

                    if ($tested >= 3) break; // Solo probar algunos
                }
            }

            $this->line("   ✅ Accesibles: $accessible/$tested");

        } catch (\Exception $e) {
            $this->error("   ❌ Error: " . $e->getMessage());
        }
    }

    private function testBucketAccess()
    {
        $this->line('🪣 3. Verificando buckets...');

        $buckets = ['certificates', 'cv', 'portfolio-images'];

        foreach ($buckets as $bucket) {
            $this->line("   Probando bucket: $bucket");

            if ($this->testBucketUpload($bucket)) {
                $this->line("   ✅ $bucket: Funcional");
            } else {
                $this->line("   ❌ $bucket: Error");
            }
        }
    }

    private function checkUrlAccess($url)
    {
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 10, 'verify' => false]);
            $response = $client->head($url);
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function testBucketUpload($bucket)
    {
        try {
            $testContent = '%PDF-1.4
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj
3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]>>endobj
trailer<</Size 3/Root 1 0 R>>startxref 0 %%EOF';

            $filename = "test_$bucket" . '_' . time() . '.pdf';
            $folder = $bucket === 'portfolio-images' ? 'test' : $bucket;
            $fullPath = "$folder/$filename";
            $url = config('supabase.url') . "/storage/v1/object/$bucket/$fullPath";

            $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => false]);

            $response = $client->post($url, [
                'headers' => [
                    'apikey' => config('supabase.service_key'),
                    'Authorization' => 'Bearer ' . config('supabase.service_key'),
                    'Content-Type' => 'application/pdf',
                ],
                'body' => $testContent,
            ]);

            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                // Limpiar
                $client->delete($url, [
                    'headers' => [
                        'apikey' => config('supabase.service_key'),
                        'Authorization' => 'Bearer ' . config('supabase.service_key'),
                    ],
                ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            return false;
        }
    }
}
