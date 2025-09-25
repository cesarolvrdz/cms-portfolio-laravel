<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseService
{
    protected $url;
    protected $key;

    public function __construct()
    {
        $this->url = config('supabase.url') . '/rest/v1';
        $this->key = config('supabase.key');
    }

    protected function headers()
    {
        return [
            'apikey' => $this->key,
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type' => 'application/json',
        ];
    }

    public function getProjects($filters = [])
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->url . '/projects', $filters);
        return $response->json();
    }

    public function createProject(array $data)
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->url . '/projects', $data);
        return $response->json();
    }

    public function updateProject($id, array $data)
    {
        $response = Http::withHeaders($this->headers())
            ->patch($this->url . "/projects?id=eq.$id", $data);
        return $response->json();
    }

    public function deleteProject($id)
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->url . "/projects?id=eq.$id");
        return $response->json();
    }

    public function uploadImage($file, $filename)
    {
        $bucket = config('supabase.bucket');
        $url = config('supabase.url') . "/storage/v1/object/$bucket/$filename";

        // Usar Guzzle directamente para envío de archivos binarios
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post($url, [
                'headers' => [
                    'apikey' => $this->key,
                    'Authorization' => 'Bearer ' . $this->key,
                    'Content-Type' => $file->getMimeType(),
                ],
                'body' => $file->get(),
            ]);

            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                return config('supabase.url') . "/storage/v1/object/public/$bucket/$filename";
            }
        } catch (\Exception $e) {
            \Log::error('Error uploading image to Supabase: ' . $e->getMessage());
        }

        return null;
    }

    public function getTechTags($filters = [])
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->url . '/tech_tags', $filters);
        return $response->json();
    }

    public function createTechTag(array $data)
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->url . '/tech_tags', $data);
        return $response->json();
    }

    public function updateTechTag($id, array $data)
    {
        $response = Http::withHeaders($this->headers())
            ->patch($this->url . "/tech_tags?id=eq.$id", $data);
        return $response->json();
    }

    public function deleteTechTag($id)
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->url . "/tech_tags?id=eq.$id");
        return $response->json();
    }
}
