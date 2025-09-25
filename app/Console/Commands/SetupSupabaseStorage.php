<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetupSupabaseStorage extends Command
{
    protected $signature = 'supabase:setup-storage';
    protected $description = 'Setup Supabase storage policies for CV bucket';

    public function handle()
    {
        $this->info('Setting up Supabase storage policies...');

        // Test 1: Check if we can access Supabase
        $supabaseUrl = config('supabase.url');
        $serviceKey = config('supabase.service_key');

        if (!$supabaseUrl || !$serviceKey) {
            $this->error('Supabase URL or Service Key not configured');
            return 1;
        }

        // Test 2: Create/Update bucket
        $this->createCvBucket();

        // Test 3: Test upload with service key
        $this->testUploadWithServiceKey();

        return 0;
    }

    private function createCvBucket()
    {
        $this->line('1. Creating/updating CV bucket...');

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => false]);

            // Try to create bucket
            $response = $client->post(config('supabase.url') . '/storage/v1/bucket', [
                'headers' => [
                    'apikey' => config('supabase.service_key'),
                    'Authorization' => 'Bearer ' . config('supabase.service_key'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'name' => 'cv',
                    'public' => true,
                    'file_size_limit' => 52428800, // 50MB
                    'allowed_mime_types' => ['application/pdf']
                ]
            ]);

            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                $this->line('   ✅ CV bucket created/updated successfully');
            } else {
                $body = $response->getBody()->getContents();
                if (str_contains($body, 'already exists')) {
                    $this->line('   ✅ CV bucket already exists');
                } else {
                    $this->error('   ❌ Failed to create CV bucket: ' . $response->getStatusCode());
                    $this->line('   Response: ' . $body);
                }
            }
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'already exists')) {
                $this->line('   ✅ CV bucket already exists');
            } else {
                $this->error('   ❌ Error with CV bucket: ' . $e->getMessage());
            }
        }
    }

    private function testUploadWithServiceKey()
    {
        $this->line('2. Testing upload with service key...');

        // Create a test PDF content
        $testContent = '%PDF-1.4
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj
3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 4 0 R>>endobj
4 0 obj<</Length 44>>stream
BT/F1 12 Tf 72 720 Td(Test PDF)Tj ET
endstream endobj
xref 0 5
0000000000 65535 f 0000000010 00000 n 0000000053 00000 n 0000000110 00000 n 0000000181 00000 n
trailer<</Size 5/Root 1 0 R>>startxref 265 %%EOF';

        try {
            $filename = 'test_service_' . time() . '.pdf';
            $bucket = 'cv';
            $fullPath = "cv/$filename";
            $url = config('supabase.url') . "/storage/v1/object/$bucket/$fullPath";

            $client = new \GuzzleHttp\Client([
                'timeout' => 60,
                'verify' => false,
            ]);

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
                $this->line("   ✅ Upload with service key successful!");
                $this->line("   URL: $supabaseUrl");

                // Test accessibility
                $this->testFileAccess($supabaseUrl);
            } else {
                $this->error("   ❌ Upload failed with status: $statusCode");
                $this->line("   Response: " . $response->getBody());
            }

        } catch (\Exception $e) {
            $this->error("   ❌ Exception: " . $e->getMessage());
        }
    }

    private function testFileAccess($url)
    {
        $this->line('3. Testing file accessibility...');

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => false]);
            $response = $client->head($url);

            if ($response->getStatusCode() === 200) {
                $this->line("   ✅ File is publicly accessible");
            } else {
                $this->error("   ❌ File not accessible: " . $response->getStatusCode());
            }

        } catch (\Exception $e) {
            $this->error("   ❌ File access test failed: " . $e->getMessage());
        }
    }
}
