<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestSupabaseStorage extends Command
{
    protected $signature = 'supabase:test-storage';
    protected $description = 'Test Supabase Storage connection and bucket access';

    public function handle()
    {
        $this->info('Testing Supabase Storage connection...');

        // Test basic connection
        $storageUrl = env('SUPABASE_URL') . '/storage/v1';
        $serviceKey = env('SUPABASE_SERVICE_KEY');

        $this->info("Storage URL: {$storageUrl}");
        $this->info("Service Key exists: " . ($serviceKey ? 'Yes' : 'No'));

        // Test bucket listing
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $serviceKey,
                'Content-Type' => 'application/json',
            ])->get($storageUrl . '/bucket');

            if ($response->successful()) {
                $buckets = $response->json();
                $this->info('Available buckets:');
                foreach ($buckets as $bucket) {
                    $this->line('- ' . $bucket['name'] . ' (public: ' . ($bucket['public'] ? 'yes' : 'no') . ')');
                }

                // Check if portfolio-images bucket exists
                $portfolioBucket = collect($buckets)->firstWhere('name', 'portfolio-images');
                if ($portfolioBucket) {
                    $this->info('✓ portfolio-images bucket found and accessible');
                } else {
                    $this->error('✗ portfolio-images bucket not found');
                    $this->info('You need to create a "portfolio-images" bucket in Supabase Storage');
                }
            } else {
                $this->error('Failed to connect to Supabase Storage');
                $this->error('Response: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
