<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use App\Services\SupabaseServiceOptimized;

class TestPdfUpload extends Command
{
    protected $signature = 'test:pdf-upload';
    protected $description = 'Test PDF upload to Supabase CV bucket';

    protected $supabaseService;

    public function __construct(SupabaseServiceOptimized $supabaseService)
    {
        parent::__construct();
        $this->supabaseService = $supabaseService;
    }

    public function handle()
    {
        $this->info('Testing PDF upload to CV bucket...');

        // Create a test PDF content
        $testContent = '%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj

2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj

3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
/Contents 4 0 R
>>
endobj

4 0 obj
<<
/Length 44
>>
stream
BT
/F1 12 Tf
72 720 Td
(Test PDF) Tj
ET
endstream
endobj

xref
0 5
0000000000 65535 f
0000000010 00000 n
0000000053 00000 n
0000000110 00000 n
0000000181 00000 n
trailer
<<
/Size 5
/Root 1 0 R
>>
startxref
265
%%EOF';

        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_cv_') . '.pdf';
        file_put_contents($tempFile, $testContent);

        try {
            // Create a test UploadedFile object
            $uploadedFile = new UploadedFile(
                $tempFile,
                'test_cv.pdf',
                'application/pdf',
                null,
                true
            );

            $filename = 'test_cv_' . time() . '.pdf';

            $this->line('Uploading test PDF: ' . $filename);

            // Try direct upload to Supabase
            $this->testDirectUpload($uploadedFile, $filename);

            // Try using service method
            $this->testServiceUpload($uploadedFile, $filename);

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        } finally {
            // Clean up
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    private function testDirectUpload($file, $filename)
    {
        $this->line('1. Testing direct upload to Supabase...');

        try {
            $bucket = 'cv';
            $fullPath = "cv/$filename";
            $url = config('supabase.url') . "/storage/v1/object/$bucket/$fullPath";

            $client = new \GuzzleHttp\Client([
                'timeout' => 60,
                'verify' => false,
            ]);

            $response = $client->post($url, [
                'headers' => [
                    'apikey' => config('supabase.key'),
                    'Authorization' => 'Bearer ' . config('supabase.key'),
                    'Content-Type' => 'application/pdf',
                ],
                'body' => $file->get(),
            ]);

            $statusCode = $response->getStatusCode();
            $this->line("   Status Code: $statusCode");

            if ($statusCode === 200 || $statusCode === 201) {
                $supabaseUrl = config('supabase.url') . "/storage/v1/object/public/$bucket/$fullPath";
                $this->line("   ✅ Upload successful!");
                $this->line("   URL: $supabaseUrl");

                // Test if file is accessible
                $this->testFileAccess($supabaseUrl);
            } else {
                $this->error("   ❌ Upload failed");
                $this->line("   Response: " . $response->getBody());
            }

        } catch (\Exception $e) {
            $this->error("   ❌ Exception: " . $e->getMessage());
        }
    }

    private function testServiceUpload($file, $filename)
    {
        $this->line('2. Testing service upload method...');

        try {
            $result = $this->supabaseService->uploadPdf($file, $filename, 'cv');

            if ($result) {
                $this->line("   ✅ Service upload successful!");
                $this->line("   URL: $result");

                // Test if file is accessible
                $this->testFileAccess($result);
            } else {
                $this->error("   ❌ Service upload failed");
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
                $this->line("   ✅ File is accessible");
                $contentType = $response->getHeader('content-type');
                if ($contentType) {
                    $this->line("   Content-Type: " . $contentType[0]);
                }
            } else {
                $this->error("   ❌ File not accessible: " . $response->getStatusCode());
            }

        } catch (\Exception $e) {
            $this->error("   ❌ File access test failed: " . $e->getMessage());
        }
    }
}
