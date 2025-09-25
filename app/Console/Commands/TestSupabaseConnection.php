<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseServiceOptimized;

class TestSupabaseConnection extends Command
{
    protected $signature = 'test:supabase';
    protected $description = 'Test Supabase connection and bucket access';

    protected $supabaseService;

    public function __construct(SupabaseServiceOptimized $supabaseService)
    {
        parent::__construct();
        $this->supabaseService = $supabaseService;
    }

    public function handle()
    {
        $this->info('Testing Supabase connection...');

        // Test 1: Check configuration
        $this->info('1. Checking configuration:');
        $this->line('   Supabase URL: ' . config('supabase.url'));
        $this->line('   Supabase Key: ' . (config('supabase.key') ? 'Set' : 'Not set'));
        $this->line('   Service Key: ' . (config('supabase.service_key') ? 'Set' : 'Not set'));
        $this->line('   Default Bucket: ' . config('supabase.bucket', 'Not set'));

        // Test 2: Try to list buckets
        $this->info('2. Testing bucket access:');
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => false]);

            // List all buckets
            $response = $client->get(config('supabase.url') . '/storage/v1/bucket', [
                'headers' => [
                    'apikey' => config('supabase.key'),
                    'Authorization' => 'Bearer ' . config('supabase.key'),
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $buckets = json_decode($response->getBody(), true);
                $this->line('   Available buckets:');
                foreach ($buckets as $bucket) {
                    $this->line('   - ' . $bucket['name'] . ' (public: ' . ($bucket['public'] ? 'yes' : 'no') . ')');
                }

                // Check if 'cv' bucket exists
                $cvBucketExists = collect($buckets)->contains('name', 'cv');
                if ($cvBucketExists) {
                    $this->line('   ✅ CV bucket exists');
                } else {
                    $this->error('   ❌ CV bucket does not exist');
                    $this->line('   Creating CV bucket...');
                    $this->createCvBucket();
                }
            } else {
                $this->error('   Failed to list buckets: ' . $response->getStatusCode());
            }
        } catch (\Exception $e) {
            $this->error('   Error: ' . $e->getMessage());
        }

        // Test 3: Test database connection
        $this->info('3. Testing database connection:');
        try {
            $cvs = $this->supabaseService->getCvDocuments();
            $this->line('   ✅ Database connection successful');
            $this->line('   Found ' . count($cvs) . ' CV documents');
        } catch (\Exception $e) {
            $this->error('   ❌ Database connection failed: ' . $e->getMessage());
        }

        $this->info('Test completed!');
    }

    private function createCvBucket()
    {
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => false]);

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
                $this->line('   ✅ CV bucket created successfully');
            } else {
                $this->error('   ❌ Failed to create CV bucket: ' . $response->getStatusCode());
                $this->line('   Response: ' . $response->getBody());
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Error creating CV bucket: ' . $e->getMessage());
        }
    }
}
