<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SupabaseServiceOptimized
{
    protected $url;
    protected $key;
    protected $serviceKey;
    protected $cacheTime = 60; // Cache por 60 segundos

    public function __construct()
    {
        $this->url = config('supabase.url') . '/rest/v1';
        $this->key = config('supabase.key');
        $this->serviceKey = config('supabase.service_key');
    }

    protected function headers($useServiceKey = false)
    {
        $apiKey = $useServiceKey ? $this->serviceKey : $this->key;

        return [
            'apikey' => $apiKey,
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
            'Prefer' => 'return=minimal'
        ];
    }

    // ===== MÉTODOS PARA PROYECTOS =====

    // ===== MÉTODOS PARA PROYECTOS =====

    // OPTIMIZACIÓN 1: Cache para proyectos
    public function getProjects($filters = [], $useCache = true)
    {
        $cacheKey = 'supabase.projects.' . md5(json_encode($filters));

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = Http::timeout(10) // Timeout más corto
            ->withHeaders($this->headers())
            ->get($this->url . '/projects', $filters);

        $data = $response->json();

        if ($useCache && $response->successful()) {
            Cache::put($cacheKey, $data, 30); // Cache por 30 segundos
        }

        return $data;
    }

    // OPTIMIZACIÓN 2: Invalidar cache al crear
    public function createProject(array $data)
    {
        $response = Http::timeout(15)
            ->withHeaders($this->headers(true)) // Usar service key
            ->post($this->url . '/projects', $data);

        // Limpiar cache después de crear
        $this->clearProjectsCache();

        return $response->json();
    }

    // OPTIMIZACIÓN 3: Update con respuesta inmediata
    public function updateProject($id, array $data)
    {
        $headers = $this->headers();
        $headers['Prefer'] = 'return=representation'; // Devuelve el objeto actualizado

        $response = Http::timeout(15)
            ->withHeaders($headers)
            ->patch($this->url . "/projects?id=eq.$id", $data);

        // Limpiar cache después de actualizar
        $this->clearProjectsCache();

        return $response->json();
    }

    // OPTIMIZACIÓN 4: Delete con confirmación
    public function deleteProject($id)
    {
        $headers = $this->headers();
        $headers['Prefer'] = 'return=representation'; // Devuelve el objeto eliminado

        $response = Http::timeout(10)
            ->withHeaders($headers)
            ->delete($this->url . "/projects?id=eq.$id");

        // Limpiar cache después de eliminar
        $this->clearProjectsCache();

        return $response->json();
    }

    // OPTIMIZACIÓN 5: Cache para tech tags
    public function getTechTags($filters = [], $useCache = true)
    {
        $cacheKey = 'supabase.tech_tags.' . md5(json_encode($filters));

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = Http::timeout(8)
            ->withHeaders($this->headers())
            ->get($this->url . '/tech_tags', $filters);

        $data = $response->json();

        if ($useCache && $response->successful()) {
            Cache::put($cacheKey, $data, 60); // Cache más largo para tech tags
        }

        return $data;
    }

    public function createTechTag(array $data)
    {
        $response = Http::timeout(10)
            ->withHeaders($this->headers(true)) // Usar service key
            ->post($this->url . '/tech_tags', $data);

        $this->clearTechTagsCache();
        return $response->json();
    }

    public function updateTechTag($id, array $data)
    {
        $headers = $this->headers(true); // Usar service key
        $headers['Prefer'] = 'return=representation';

        $response = Http::timeout(10)
            ->withHeaders($headers)
            ->patch($this->url . "/tech_tags?id=eq.$id", $data);

        $this->clearTechTagsCache();
        return $response->json();
    }

    public function deleteTechTag($id)
    {
        $response = Http::timeout(8)
            ->withHeaders($this->headers(true)) // Usar service key
            ->delete($this->url . "/tech_tags?id=eq.$id");

        $this->clearTechTagsCache();
        return $response->json();
    }

    // OPTIMIZACIÓN 6: Upload de imágenes más eficiente
    public function uploadImage($file, $filename)
    {
        $bucket = config('supabase.bucket');
        $url = config('supabase.url') . "/storage/v1/object/$bucket/$filename";

        $client = new \GuzzleHttp\Client([
            'timeout' => 30, // Timeout más largo para uploads
            'verify' => false, // Para evitar problemas SSL en desarrollo
        ]);

        try {
            // Método 1: Intentar con multipart/form-data (más compatible con RLS)
            $response = $client->post($url, [
                'headers' => [
                    'apikey' => $this->key,
                    'Authorization' => 'Bearer ' . $this->key,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => $file->get(),
                        'filename' => $filename,
                        'headers' => [
                            'Content-Type' => $file->getMimeType(),
                        ]
                    ]
                ]
            ]);

            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                return config('supabase.url') . "/storage/v1/object/public/$bucket/$filename";
            }
        } catch (\Exception $e) {
            Log::warning('Método multipart falló: ' . $e->getMessage());

            // Método 2: Fallback al método original
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
            } catch (\Exception $e2) {
                Log::error('Error uploading image to Supabase (ambos métodos): ' . $e2->getMessage());

                // Método 3: Guardado local como último recurso
                return $this->saveImageLocally($file, $filename);
            }
        }

        return null;
    }

    // Método de respaldo: guardar imágenes localmente
    private function saveImageLocally($file, $filename)
    {
        try {
            $publicPath = public_path('storage/images');

            // Crear directorio si no existe
            if (!is_dir($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $localPath = $publicPath . '/' . $filename;
            $file->move($publicPath, $filename);

            Log::info("Imagen guardada localmente: $filename");
            return asset('storage/images/' . $filename);

        } catch (\Exception $e) {
            Log::error('Error guardando imagen localmente: ' . $e->getMessage());
            return null;
        }
    }

    // OPTIMIZACIÓN 7: Métodos para limpiar cache mejorados
    protected function clearProjectsCache()
    {
        // Limpiar con pattern matching para ser más efectivo
        $patterns = [
            'supabase.projects.*',
            'supabase.stats'
        ];

        // Método más directo: limpiar por patrones conocidos
        Cache::forget('supabase.projects.' . md5('[]')); // Cache sin filtros
        Cache::forget('supabase.stats');

        // Limpiar todos los posibles filtros comunes
        $commonFilters = [
            [],
            ['status' => 'eq.completed'],
            ['status' => 'eq.in-progress'],
            ['status' => 'eq.planned']
        ];

        foreach ($commonFilters as $filter) {
            $cacheKey = 'supabase.projects.' . md5(json_encode($filter));
            Cache::forget($cacheKey);
        }
    }

    protected function clearTechTagsCache()
    {
        Cache::forget('supabase.tech_tags.' . md5('[]'));

        // Limpiar filtros comunes de tech tags
        $commonFilters = [
            [],
            ['active' => 'eq.true']
        ];

        foreach ($commonFilters as $filter) {
            $cacheKey = 'supabase.tech_tags.' . md5(json_encode($filter));
            Cache::forget($cacheKey);
        }
    }

    // OPTIMIZACIÓN 8: Método para estadísticas rápidas
    public function getStats()
    {
        $cacheKey = 'supabase.stats';

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $projects = $this->getProjects([], false); // Sin cache para stats
        $techTags = $this->getTechTags([], false);

        $stats = [
            'total_projects' => count($projects ?: []),
            'completed_projects' => count(array_filter($projects ?: [], fn($p) => $p['status'] === 'completed')),
            'in_progress_projects' => count(array_filter($projects ?: [], fn($p) => $p['status'] === 'in-progress')),
            'total_tech_tags' => count($techTags ?: []),
        ];

        Cache::put($cacheKey, $stats, 60); // Cache por 1 minuto
        return $stats;
    }

    /**
     * 👤 MÉTODOS PARA PROFILE
     */
    public function getProfile()
    {
        $cacheKey = "profile_data";

        return Cache::remember($cacheKey, $this->cacheTime, function () {
            $response = Http::withHeaders($this->headers())
                ->get($this->url . "/profile?is_active=eq.true&limit=1");

            if (!$response->successful()) {
                Log::error("Error al obtener perfil", ["response" => $response->body()]);
                return null;
            }

            $data = $response->json();
            return !empty($data) ? $data[0] : null;
        });
    }

    public function updateProfile($data)
    {
        Cache::forget("profile_data");

        $data["updated_at"] = now()->toISOString();

        $response = Http::withHeaders($this->headers())
            ->patch($this->url . "/profile?is_active=eq.true", $data);

        if (!$response->successful()) {
            Log::error("Error al actualizar perfil", [
                "data" => $data,
                "response" => $response->body()
            ]);
            return false;
        }

        return true;
    }

    public function createProfile($data)
    {
        Cache::forget("profile_data");

        $data = array_merge($data, [
            "created_at" => now()->toISOString(),
            "updated_at" => now()->toISOString(),
            "is_active" => true
        ]);

        $response = Http::withHeaders($this->headers())
            ->post($this->url . "/profile", $data);

        if (!$response->successful()) {
            Log::error("Error al crear perfil", [
                "data" => $data,
                "response" => $response->body()
            ]);
            return false;
        }

        $responseData = $response->json();
        return !empty($responseData) ? $responseData[0] : true;
    }
    /**
     * 🔗 MÉTODOS PARA SOCIAL LINKS
     */
    public function getSocialLinks($activeOnly = true)
    {
        $cacheKey = $activeOnly ? "social_links_active" : "social_links_all";

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($activeOnly) {
            $url = $this->url . "/social_links?order=order.asc";

            if ($activeOnly) {
                $url .= "&is_active=eq.true";
            }

            $response = Http::withHeaders($this->headers())->get($url);

            if (!$response->successful()) {
                Log::error("Error al obtener enlaces sociales", ["response" => $response->body()]);
                return [];
            }

            return $response->json();
        });
    }

    public function getSocialLink($id)
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->url . "/social_links?id=eq.{$id}");

        if (!$response->successful()) {
            Log::error("Error al obtener enlace social", [
                "id" => $id,
                "response" => $response->body()
            ]);
            return null;
        }

        $data = $response->json();
        return !empty($data) ? $data[0] : null;
    }

    public function createSocialLink($data)
    {
        Cache::forget("social_links_active");
        Cache::forget("social_links_all");

        $data = array_merge($data, [
            "created_at" => now()->toISOString(),
            "updated_at" => now()->toISOString(),
            "is_active" => true
        ]);

        $response = Http::withHeaders($this->headers())
            ->post($this->url . "/social_links", $data);

        if (!$response->successful()) {
            Log::error("Error al crear enlace social", [
                "data" => $data,
                "response" => $response->body()
            ]);
            return false;
        }

        $responseData = $response->json();
        return !empty($responseData) ? $responseData[0] : true;
    }

    public function updateSocialLink($id, $data)
    {
        Cache::forget("social_links_active");
        Cache::forget("social_links_all");

        $data["updated_at"] = now()->toISOString();

        $response = Http::withHeaders($this->headers())
            ->patch($this->url . "/social_links?id=eq.{$id}", $data);

        if (!$response->successful()) {
            Log::error("Error al actualizar enlace social", [
                "id" => $id,
                "data" => $data,
                "response" => $response->body()
            ]);
            return false;
        }

        return true;
    }

    public function deleteSocialLink($id)
    {
        Cache::forget("social_links_active");
        Cache::forget("social_links_all");

        $response = Http::withHeaders($this->headers())
            ->delete($this->url . "/social_links?id=eq.{$id}");

        if (!$response->successful()) {
            Log::error("Error al eliminar enlace social", [
                "id" => $id,
                "response" => $response->body()
            ]);
            return false;
        }

        return true;
    }
    /**
     * ⚙️ MÉTODOS PARA SITE SETTINGS
     */
    public function getSiteSettings($group = null, $publicOnly = false)
    {
        $cacheKey = "site_settings_" . ($group ?? "all") . "_" . ($publicOnly ? "public" : "all");

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($group, $publicOnly) {
            $url = $this->url . "/site_settings?order=group.asc,key.asc";

            if ($group) {
                $url .= "&group=eq.{$group}";
            }

            if ($publicOnly) {
                $url .= "&is_public=eq.true";
            }

            $response = Http::withHeaders($this->headers())->get($url);

            if (!$response->successful()) {
                Log::error("Error al obtener configuraciones", ["response" => $response->body()]);
                return [];
            }

            return $response->json();
        });
    }

    public function getSiteSetting($key, $default = null)
    {
        $cacheKey = "site_setting_" . $key;

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($key, $default) {
            $response = Http::withHeaders($this->headers())
                ->get($this->url . "/site_settings?key=eq.{$key}&limit=1");

            if (!$response->successful()) {
                return $default;
            }

            $data = $response->json();
            return !empty($data) ? $data[0]["value"] : $default;
        });
    }

    public function updateSiteSetting($key, $value)
    {
        Cache::forget("site_setting_" . $key);
        Cache::forget("site_settings_*");

        $data = [
            "value" => $value,
            "updated_at" => now()->toISOString()
        ];

        $response = Http::withHeaders($this->headers())
            ->patch($this->url . "/site_settings?key=eq.{$key}", $data);

        if (!$response->successful()) {
            Log::error("Error al actualizar configuración", [
                "key" => $key,
                "value" => $value,
                "response" => $response->body()
            ]);
            return false;
        }

        return true;
    }

    public function createSiteSetting($data)
    {
        Cache::forget("site_settings_*");

        $data = array_merge($data, [
            "created_at" => now()->toISOString(),
            "updated_at" => now()->toISOString()
        ]);

        $response = Http::withHeaders($this->headers())
            ->post($this->url . "/site_settings", $data);

        if (!$response->successful()) {
            Log::error("Error al crear configuración", [
                "data" => $data,
                "response" => $response->body()
            ]);
            return false;
        }

        $responseData = $response->json();
        return !empty($responseData) ? $responseData[0] : true;
    }

    public function deleteSiteSetting($id)
    {
        Cache::forget("site_settings_*");

        $response = Http::withHeaders($this->headers())
            ->delete($this->url . "/site_settings?id=eq.{$id}");

        if (!$response->successful()) {
            Log::error("Error al eliminar configuración", [
                "id" => $id,
                "response" => $response->body()
            ]);
            return false;
        }

        return true;
    }

    // ===== MÉTODOS PARA EDUCACIÓN =====

    public function getEducation($useCache = true)
    {
        $cacheKey = 'supabase.education';

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Intentar primero con service key para acceso completo
        $response = Http::timeout(10)
            ->withHeaders($this->headers(true)) // Usar service key para lectura
            ->get($this->url . '/education?order=order_position.asc,end_date.desc.nullsfirst');

        if (!$response->successful()) {
            Log::error("Error al obtener educación", [
                "response" => $response->body(),
                "status" => $response->status()
            ]);
            return [];
        }

        $data = $response->json();

        if ($useCache) {
            Cache::put($cacheKey, $data, 60);
        }

        return $data;
    }

    public function getEducationById($id)
    {
        $response = Http::timeout(10)
            ->withHeaders($this->headers(true)) // Usar service key
            ->get($this->url . "/education?id=eq.$id&limit=1");

        if (!$response->successful()) {
            Log::error("Error al obtener educación por ID", [
                "id" => $id,
                "response" => $response->body()
            ]);
            return null;
        }

        $data = $response->json();
        return $data[0] ?? null;
    }

    public function createEducation(array $data)
    {
        $response = Http::timeout(15)
            ->withHeaders($this->headers(true)) // Usar service key
            ->post($this->url . '/education', $data);

        Cache::forget('supabase.education');

        if (!$response->successful()) {
            Log::error("Error al crear educación", [
                "data" => $data,
                "response" => $response->body()
            ]);
            return false;
        }

        return $response->json();
    }

    public function updateEducation($id, array $data)
    {
        $headers = $this->headers(true); // Usar service key
        $headers['Prefer'] = 'return=representation';

        $response = Http::timeout(15)
            ->withHeaders($headers)
            ->patch($this->url . "/education?id=eq.$id", $data);

        Cache::forget('supabase.education');

        if (!$response->successful()) {
            Log::error("Error al actualizar educación", [
                "id" => $id,
                "data" => $data,
                "response" => $response->body()
            ]);
            return false;
        }

        return true;
    }

    public function deleteEducation($id)
    {
        $response = Http::timeout(10)
            ->withHeaders($this->headers(true)) // Usar service key
            ->delete($this->url . "/education?id=eq.$id");

        Cache::forget('supabase.education');

        if (!$response->successful()) {
            Log::error("Error al eliminar educación", [
                "id" => $id,
                "response" => $response->body()
            ]);
            return false;
        }

        return true;
    }

    // ===== MÉTODOS PARA EXPERIENCIA LABORAL =====

    public function getWorkExperience($useCache = true)
    {
        $cacheKey = 'supabase.work_experience';

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Intentar primero con service key para acceso completo
        $response = Http::timeout(10)
            ->withHeaders($this->headers(true)) // Usar service key para lectura
            ->get($this->url . '/work_experience?order=order_position.asc,end_date.desc.nullsfirst');

        if (!$response->successful()) {
            Log::error("Error al obtener experiencia laboral", [
                "response" => $response->body(),
                "status" => $response->status()
            ]);
            return [];
        }

        $data = $response->json();

        if ($useCache) {
            Cache::put($cacheKey, $data, 60);
        }

        return $data;
    }

    public function getWorkExperienceById($id)
    {
        $response = Http::timeout(10)
            ->withHeaders($this->headers(true)) // Usar service key
            ->get($this->url . "/work_experience?id=eq.$id&limit=1");

        if (!$response->successful()) {
            Log::error("Error al obtener experiencia laboral por ID", [
                "id" => $id,
                "response" => $response->body()
            ]);
            return null;
        }

        $data = $response->json();
        return $data[0] ?? null;
    }

    public function createWorkExperience(array $data)
    {
        // Agregar timestamps si no existen
        $data = array_merge($data, [
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ]);

        $headers = $this->headers(true); // Usar service key
        $headers['Prefer'] = 'return=representation'; // Devolver el objeto creado

        $response = Http::timeout(15)
            ->withHeaders($headers)
            ->post($this->url . '/work_experience', $data);

        Cache::forget('supabase.work_experience');

        if (!$response->successful()) {
            Log::error("Error al crear experiencia laboral", [
                "data" => $data,
                "status" => $response->status(),
                "response" => $response->body()
            ]);
            return false;
        }

        $result = $response->json();
        Log::info("Work Experience creado exitosamente", [
            "id" => isset($result[0]['id']) ? $result[0]['id'] : 'unknown',
            "company" => $data['company'] ?? 'unknown'
        ]);

        return $result;
    }

    public function updateWorkExperience($id, array $data)
    {
        $headers = $this->headers(true); // Usar service key
        $headers['Prefer'] = 'return=representation';

        $response = Http::timeout(15)
            ->withHeaders($headers)
            ->patch($this->url . "/work_experience?id=eq.$id", $data);

        Cache::forget('supabase.work_experience');

        if (!$response->successful()) {
            Log::error("Error al actualizar experiencia laboral", [
                "id" => $id,
                "data" => $data,
                "response" => $response->body()
            ]);
            return false;
        }

        return $response->json();
    }

    public function deleteWorkExperience($id)
    {
        $response = Http::timeout(10)
            ->withHeaders($this->headers(true)) // Usar service key
            ->delete($this->url . "/work_experience?id=eq.$id");

        Cache::forget('supabase.work_experience');

        if (!$response->successful()) {
            Log::error("Error al eliminar experiencia laboral", [
                "id" => $id,
                "response" => $response->body()
            ]);
            return false;
        }

        return true;
    }

    // ===========================================
    // MÉTODOS PARA GESTIÓN DE USUARIOS CMS
    // ===========================================

    public function getCmsUsers($useCache = true)
    {
        $cacheKey = 'supabase.cms_users';

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = Http::timeout(10)
            ->withHeaders($this->headers(true)) // Usar service key
            ->get($this->url . '/cms_users?order=created_at.desc');

        $data = $response->json();

        if ($useCache && $response->successful()) {
            Cache::put($cacheKey, $data, $this->cacheTime);
        }

        return $data ?? [];
    }

    public function getCmsUserById($id)
    {
        $response = Http::timeout(10)
            ->withHeaders($this->headers())
            ->get($this->url . "/cms_users?id=eq.$id&limit=1");

        $data = $response->json();

        return !empty($data) ? $data[0] : null;
    }

    public function createCmsUser(array $data)
    {
        // Hash de la contraseña antes de enviar
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']); // Remover password plano
        }

        $response = Http::timeout(10)
            ->withHeaders($this->headers(true)) // Usar service key
            ->post($this->url . '/cms_users', $data);

        Cache::forget('supabase.cms_users');

        if (!$response->successful()) {
            Log::error("Error al crear usuario CMS", [
                "data" => $data,
                "response" => $response->body()
            ]);
            return false;
        }

        return true;
    }

    public function updateCmsUser($id, array $data)
    {
        // Agregar timestamp de actualización
        $data['updated_at'] = now()->toISOString();

        $response = Http::timeout(10)
            ->withHeaders($this->headers(true)) // Usar service key
            ->patch($this->url . "/cms_users?id=eq.$id", $data);

        Cache::forget('supabase.cms_users');
        return $response->json();
    }

    public function deleteCmsUser($id)
    {
        $response = Http::timeout(10)
            ->withHeaders($this->headers(true)) // Usar service key
            ->delete($this->url . "/cms_users?id=eq.$id");

        Cache::forget('supabase.cms_users');

        if (!$response->successful()) {
            Log::error("Error al eliminar usuario CMS", [
                "id" => $id,
                "response" => $response->body()
            ]);
            return false;
        }

        return true;
    }

    public function getCmsUserByEmail($email)
    {
        $response = Http::timeout(10)
            ->withHeaders($this->headers())
            ->get($this->url . "/cms_users?email=eq.$email&limit=1");

        $data = $response->json();

        return !empty($data) ? $data[0] : null;
    }

    public function activateCmsUser($id)
    {
        return $this->updateCmsUser($id, ['is_active' => true]);
    }

    public function deactivateCmsUser($id)
    {
        return $this->updateCmsUser($id, ['is_active' => false]);
    }

    public function updateLastLogin($id)
    {
        return $this->updateCmsUser($id, ['last_login' => now()->toISOString()]);
    }

    // ===========================================
    // MÉTODOS PARA GESTIÓN DE DISPONIBILIDAD
    // ===========================================

    public function getAvailability($useCache = true)
    {
        $cacheKey = 'supabase.availability';

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = Http::timeout(10)
            ->withHeaders($this->headers())
            ->get($this->url . '/availability?is_active=eq.true&limit=1');

        if (!$response->successful()) {
            Log::error("Error al obtener disponibilidad", [
                "response" => $response->body()
            ]);
            return null;
        }

        $data = $response->json();
        $availability = !empty($data) ? $data[0] : null;

        if ($useCache && $availability) {
            Cache::put($cacheKey, $availability, 30); // Cache por 30 segundos
        }

        return $availability;
    }

    public function createAvailability(array $data)
    {
        $response = Http::timeout(15)
            ->withHeaders($this->headers(true)) // Usar service key
            ->post($this->url . '/availability', $data);

        Cache::forget('supabase.availability');

        if (!$response->successful()) {
            Log::error("Error al crear disponibilidad", [
                "data" => $data,
                "response" => $response->body()
            ]);
            return false;
        }

        return $response->json();
    }

    public function updateAvailability(array $data)
    {
        // Primero obtener el registro activo
        $current = $this->getAvailability(false);

        if (!$current) {
            // Si no existe, crear uno nuevo
            return $this->createAvailability($data);
        }

        $headers = $this->headers(true); // Usar service key
        $headers['Prefer'] = 'return=representation';

        $response = Http::timeout(15)
            ->withHeaders($headers)
            ->patch($this->url . "/availability?id=eq." . $current['id'], $data);

        Cache::forget('supabase.availability');

        if (!$response->successful()) {
            Log::error("Error al actualizar disponibilidad", [
                "data" => $data,
                "response" => $response->body()
            ]);
            return false;
        }

        return $response->json();
    }

    public function getAvailabilityForPortfolio()
    {
        // Método específico para el portafolio público (sin cache sensible)
        $response = Http::timeout(8)
            ->withHeaders($this->headers())
            ->get($this->url . '/availability?is_active=eq.true&select=status,response_time,custom_message,show_calendar_link,calendar_url,availability_details&limit=1');

        if (!$response->successful()) {
            return [
                'status' => 'available',
                'response_time' => '24 horas',
                'custom_message' => 'Disponible para nuevos proyectos',
                'show_calendar_link' => false
            ];
        }

        $data = $response->json();
        return !empty($data) ? $data[0] : [
            'status' => 'available',
            'response_time' => '24 horas',
            'custom_message' => 'Disponible para nuevos proyectos',
            'show_calendar_link' => false
        ];
    }

    // ===== MÉTODOS PARA CERTIFICADOS =====

    public function getCertificates($useCache = true, $category = null, $featuredOnly = false)
    {
        $cacheKey = 'supabase.certificates.' . md5(json_encode([$category, $featuredOnly]));

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $url = $this->url . '/certificates?is_active=eq.true&order=order_position.asc,issue_date.desc';

        if ($category) {
            $url .= "&category=eq.$category";
        }

        if ($featuredOnly) {
            $url .= "&is_featured=eq.true";
        }

        $response = Http::timeout(10)
            ->withHeaders($this->headers())
            ->get($url);

        if (!$response->successful()) {
            Log::error("Error al obtener certificados", [
                "response" => $response->body(),
                "status" => $response->status()
            ]);
            return [];
        }

        $data = $response->json();

        if ($useCache && $response->successful()) {
            Cache::put($cacheKey, $data, 60);
        }

        return $data ?? [];
    }

    public function getCertificateById($id)
    {
        $response = Http::timeout(10)
            ->withHeaders($this->headers(true)) // Usar service key para admin
            ->get($this->url . "/certificates?id=eq.$id&limit=1");

        if (!$response->successful()) {
            Log::error("Error al obtener certificado por ID", [
                "id" => $id,
                "response" => $response->body()
            ]);
            return null;
        }

        $data = $response->json();
        return $data[0] ?? null;
    }

    public function createCertificate(array $data)
    {
        // Agregar timestamps
        $data['created_at'] = now()->toISOString();
        $data['updated_at'] = now()->toISOString();

        $response = Http::timeout(15)
            ->withHeaders($this->headers(true)) // Usar service key
            ->post($this->url . '/certificates', $data);

        $this->clearCertificatesCache();

        if (!$response->successful()) {
            Log::error("Error al crear certificado", [
                "data" => $data,
                "response" => $response->body()
            ]);
            return false;
        }

        return $response->json();
    }

    public function updateCertificate($id, array $data)
    {
        $data['updated_at'] = now()->toISOString();

        $headers = $this->headers(true); // Usar service key
        $headers['Prefer'] = 'return=representation';

        $response = Http::timeout(15)
            ->withHeaders($headers)
            ->patch($this->url . "/certificates?id=eq.$id", $data);

        $this->clearCertificatesCache();

        if (!$response->successful()) {
            Log::error("Error al actualizar certificado", [
                "id" => $id,
                "data" => $data,
                "response" => $response->body()
            ]);
            return false;
        }

        return $response->json();
    }

    public function deleteCertificate($id)
    {
        $response = Http::timeout(10)
            ->withHeaders($this->headers(true)) // Usar service key
            ->delete($this->url . "/certificates?id=eq.$id");

        $this->clearCertificatesCache();

        if (!$response->successful()) {
            Log::error("Error al eliminar certificado", [
                "id" => $id,
                "response" => $response->body()
            ]);
            return false;
        }

        return true;
    }

    public function getCertificateCategories()
    {
        $cacheKey = 'supabase.certificate_categories';

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = Http::timeout(8)
            ->withHeaders($this->headers())
            ->get($this->url . '/certificates?select=category&is_active=eq.true');

        if (!$response->successful()) {
            return ['general'];
        }

        $data = $response->json();
        $categories = array_unique(array_column($data ?? [], 'category'));

        Cache::put($cacheKey, $categories, 300); // Cache por 5 minutos
        return $categories;
    }

    public function getCertificatesForPortfolio($limit = null)
    {
        $url = $this->url . '/certificates?is_active=eq.true&order=is_featured.desc,order_position.asc,issue_date.desc';

        if ($limit) {
            $url .= "&limit=$limit";
        }

        $response = Http::timeout(8)
            ->withHeaders($this->headers())
            ->get($url);

        if (!$response->successful()) {
            return [];
        }

        return $response->json() ?? [];
    }

    protected function clearCertificatesCache()
    {
        // Limpiar cache de certificados
        Cache::forget('supabase.certificate_categories');

        // Limpiar patrones comunes
        $patterns = [
            'supabase.certificates.' . md5(json_encode([null, false])),
            'supabase.certificates.' . md5(json_encode([null, true])),
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    // ===== MÉTODOS PARA CV =====

    public function getCvDocuments($useCache = true)
    {
        $cacheKey = 'supabase.cv_documents';

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = Http::timeout(10)
            ->withHeaders($this->headers(true)) // Usar service key para admin
            ->get($this->url . '/cv_documents?order=created_at.desc');

        if (!$response->successful()) {
            Log::error("Error al obtener documentos CV", [
                "response" => $response->body(),
                "status" => $response->status()
            ]);
            return [];
        }

        $data = $response->json();

        if ($useCache && $response->successful()) {
            Cache::put($cacheKey, $data, 60);
        }

        return $data ?? [];
    }

    public function getCurrentCv()
    {
        $cacheKey = 'supabase.current_cv';

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = Http::timeout(8)
            ->withHeaders($this->headers())
            ->get($this->url . '/cv_documents?is_current=eq.true&limit=1');

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();
        $currentCv = !empty($data) ? $data[0] : null;

        if ($currentCv) {
            Cache::put($cacheKey, $currentCv, 300); // Cache por 5 minutos
        }

        return $currentCv;
    }

    public function createCvDocument(array $data)
    {
        // Si es el CV actual, desactivar otros
        if ($data['is_current'] ?? false) {
            $this->setCurrentCv(null); // Desactivar otros
        }

        $data['created_at'] = now()->toISOString();
        $data['updated_at'] = now()->toISOString();

        $response = Http::timeout(15)
            ->withHeaders($this->headers(true)) // Usar service key
            ->post($this->url . '/cv_documents', $data);

        $this->clearCvCache();

        if (!$response->successful()) {
            Log::error("Error al crear documento CV", [
                "data" => $data,
                "response" => $response->body()
            ]);
            return false;
        }

        return $response->json();
    }

    public function updateCvDocument($id, array $data)
    {
        $data['updated_at'] = now()->toISOString();

        // Si es el CV actual, desactivar otros
        if ($data['is_current'] ?? false) {
            $this->setCurrentCv($id);
        }

        $headers = $this->headers(true); // Usar service key
        $headers['Prefer'] = 'return=representation';

        $response = Http::timeout(15)
            ->withHeaders($headers)
            ->patch($this->url . "/cv_documents?id=eq.$id", $data);

        $this->clearCvCache();

        if (!$response->successful()) {
            Log::error("Error al actualizar documento CV", [
                "id" => $id,
                "data" => $data,
                "response" => $response->body()
            ]);
            return false;
        }

        return $response->json();
    }

    public function deleteCvDocument($id)
    {
        $response = Http::timeout(10)
            ->withHeaders($this->headers(true)) // Usar service key
            ->delete($this->url . "/cv_documents?id=eq.$id");

        $this->clearCvCache();

        if (!$response->successful()) {
            Log::error("Error al eliminar documento CV", [
                "id" => $id,
                "response" => $response->body()
            ]);
            return false;
        }

        return true;
    }

    public function setCurrentCv($id)
    {
        // Primero desactivar todos los CV
        $response = Http::timeout(10)
            ->withHeaders($this->headers(true))
            ->patch($this->url . "/cv_documents", ['is_current' => false]);

        if ($id) {
            // Luego activar el seleccionado
            $response = Http::timeout(10)
                ->withHeaders($this->headers(true))
                ->patch($this->url . "/cv_documents?id=eq.$id", ['is_current' => true]);
        }

        $this->clearCvCache();
        return $response->successful();
    }

    protected function clearCvCache()
    {
        Cache::forget('supabase.cv_documents');
        Cache::forget('supabase.current_cv');
    }

    /**
     * Incrementar contador de descargas
     */
    public function incrementCvDownloadCount($id)
    {
        try {
            Log::info('Incrementando contador de descargas', ['id' => $id]);

            $response = Http::timeout(30)
                ->withHeaders($this->headers(true)) // Usar service key
                ->post($this->url . '/rpc/increment_download_count', [
                    'cv_id' => $id
                ]);

            if ($response->successful()) {
                Log::info('Contador de descargas incrementado', ['id' => $id]);
                return true;
            }

            Log::error('Error incrementing download count', [
                'id' => $id,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Exception incrementing download count: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener CV actual por idioma (sobrecarga para API)
     */
    public function getCurrentCvByLanguage($language = 'es')
    {
        try {
            $cacheKey = "supabase.current_cv.{$language}";

            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = Http::timeout(30)
                ->withHeaders($this->headers())
                ->get($this->url . '/cv_documents', [
                    'is_current' => 'eq.true',
                    'language' => "eq.{$language}",
                    'limit' => 1
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $cv = !empty($result) ? $result[0] : null;

                if ($cv) {
                    Cache::put($cacheKey, $cv, $this->cacheTime);
                }

                return $cv;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting current CV by language: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener CVs con filtros (sobrecarga para API)
     */
    public function getCvDocumentsWithFilters($filters = [])
    {
        try {
            $cacheKey = 'supabase.cv_documents.' . md5(json_encode($filters));

            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = Http::timeout(30)
                ->withHeaders($this->headers())
                ->get($this->url . '/cv_documents', array_merge([
                    'order' => 'created_at.desc'
                ], $filters));

            if ($response->successful()) {
                $result = $response->json();
                Cache::put($cacheKey, $result, $this->cacheTime);
                return $result;
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Error getting CV documents with filters: ' . $e->getMessage());
            return [];
        }
    }

    // Método para upload de archivos PDF
    public function uploadPdf($file, $filename, $folder = 'pdfs')
    {
        // Log inicial
        Log::info('Iniciando subida de PDF', [
            'filename' => $filename,
            'folder' => $folder,
            'size' => $file->getSize(),
            'mime' => $file->getMimeType()
        ]);

        // Determinar el bucket correcto según la carpeta
        $bucket = match($folder) {
            'cv' => 'cv',
            'certificates' => 'certificates',
            default => config('supabase.bucket', 'portfolio-images')
        };

        // Primero intentar subir a Supabase (para acceso desde el portafolio)
        $fullPath = "$folder/$filename";
        $url = config('supabase.url') . "/storage/v1/object/$bucket/$fullPath";

        $client = new \GuzzleHttp\Client([
            'timeout' => 60,
            'verify' => false,
        ]);

        try {
            // Intentar subir a Supabase primero usando SERVICE KEY
            $response = $client->post($url, [
                'headers' => [
                    'apikey' => $this->serviceKey,
                    'Authorization' => 'Bearer ' . $this->serviceKey,
                    'Content-Type' => 'application/pdf',
                ],
                'body' => $file->get(),
            ]);

            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                $supabaseUrl = config('supabase.url') . "/storage/v1/object/public/$bucket/$fullPath";
                Log::info('PDF subido exitosamente a Supabase', [
                    'filename' => $filename,
                    'bucket' => $bucket,
                    'url' => $supabaseUrl
                ]);
                return $supabaseUrl;
            } else {
                Log::error('Error en respuesta de Supabase', [
                    'status' => $response->getStatusCode(),
                    'body' => $response->getBody()->getContents()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error subiendo PDF a Supabase: ' . $e->getMessage());

            // Fallback: intentar guardar localmente
            try {
                $publicPath = public_path("storage/$folder");
                if (!is_dir($publicPath)) {
                    mkdir($publicPath, 0755, true);
                }

                $file->move($publicPath, $filename);
                $localUrl = asset("storage/$folder/$filename");

                Log::warning('PDF guardado localmente como fallback', [
                    'filename' => $filename,
                    'url' => $localUrl,
                    'reason' => 'Fallo en Supabase: ' . $e->getMessage()
                ]);

                return $localUrl;

            } catch (\Exception $e2) {
                Log::error('Error guardando PDF localmente como fallback: ' . $e2->getMessage());
            }
        }

        return null;
    }
}
