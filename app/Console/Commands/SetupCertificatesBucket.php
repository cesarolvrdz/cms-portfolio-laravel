<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupCertificatesBucket extends Command
{
    protected $signature = 'certificates:setup-bucket';
    protected $description = 'Setup certificates bucket in Supabase';

    public function handle()
    {
        $this->info('🪣 Configurando bucket de certificados...');

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => false]);

            // Intentar crear bucket
            $response = $client->post(config('supabase.url') . '/storage/v1/bucket', [
                'headers' => [
                    'apikey' => config('supabase.service_key'),
                    'Authorization' => 'Bearer ' . config('supabase.service_key'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'name' => 'certificates',
                    'public' => true,
                    'file_size_limit' => 52428800, // 50MB
                    'allowed_mime_types' => ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml', 'image/webp']
                ]
            ]);

            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                $this->line('   ✅ Bucket "certificates" creado exitosamente');
            } else {
                $body = $response->getBody()->getContents();
                if (str_contains($body, 'already exists')) {
                    $this->line('   ✅ Bucket "certificates" ya existe');
                } else {
                    $this->error('   ❌ Error creando bucket: ' . $response->getStatusCode());
                    $this->line('   Response: ' . $body);
                }
            }

            // Probar subida de archivo de prueba
            $this->testCertificateUpload();

        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'already exists')) {
                $this->line('   ✅ Bucket "certificates" ya existe');
                $this->testCertificateUpload();
            } else {
                $this->error('   ❌ Error: ' . $e->getMessage());
                return 1;
            }
        }

        return 0;
    }

    private function testCertificateUpload()
    {
        $this->line('🧪 Probando subida de archivo...');

        $testContent = '%PDF-1.4
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj
3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 4 0 R>>endobj
4 0 obj<</Length 50>>stream
BT/F1 12 Tf 72 720 Td(Test Certificate PDF)Tj ET
endstream endobj
xref 0 5
0000000000 65535 f 0000000010 00000 n 0000000053 00000 n 0000000110 00000 n 0000000181 00000 n
trailer<</Size 5/Root 1 0 R>>startxref 275 %%EOF';

        try {
            $filename = 'test_certificate_' . time() . '.pdf';
            $bucket = 'certificates';
            $fullPath = "certificates/$filename";
            $url = config('supabase.url') . "/storage/v1/object/$bucket/$fullPath";

            $client = new \GuzzleHttp\Client(['timeout' => 60, 'verify' => false]);

            $response = $client->post($url, [
                'headers' => [
                    'apikey' => config('supabase.service_key'),
                    'Authorization' => 'Bearer ' . config('supabase.service_key'),
                    'Content-Type' => 'application/pdf',
                ],
                'body' => $testContent,
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode === 200 || $statusCode === 201) {
                $supabaseUrl = config('supabase.url') . "/storage/v1/object/public/$bucket/$fullPath";
                $this->line("   ✅ Subida de prueba exitosa");
                $this->line("   🔗 URL: $supabaseUrl");

                // Probar acceso
                $this->testFileAccess($supabaseUrl);

                // Limpiar archivo de prueba
                $this->cleanupTestFile($bucket, $fullPath);
            } else {
                $this->error("   ❌ Error en subida de prueba: $statusCode");
                $this->line("   Response: " . $response->getBody());
            }

        } catch (\Exception $e) {
            $this->error("   ❌ Error en prueba: " . $e->getMessage());
        }
    }

    private function testFileAccess($url)
    {
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => false]);
            $response = $client->head($url);

            if ($response->getStatusCode() === 200) {
                $this->line("   ✅ Archivo accesible públicamente");
            } else {
                $this->error("   ❌ Archivo no accesible: " . $response->getStatusCode());
            }

        } catch (\Exception $e) {
            $this->error("   ❌ Error probando acceso: " . $e->getMessage());
        }
    }

    private function cleanupTestFile($bucket, $fullPath)
    {
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => false]);
            $url = config('supabase.url') . "/storage/v1/object/$bucket/$fullPath";

            $response = $client->delete($url, [
                'headers' => [
                    'apikey' => config('supabase.service_key'),
                    'Authorization' => 'Bearer ' . config('supabase.service_key'),
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $this->line("   🗑️  Archivo de prueba eliminado");
            }

        } catch (\Exception $e) {
            $this->line("   ⚠️  No se pudo eliminar archivo de prueba: " . $e->getMessage());
        }
    }
}
