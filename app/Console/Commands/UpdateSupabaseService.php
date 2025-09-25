<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateSupabaseService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supabase:update-service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el servicio de Supabase con métodos para las nuevas tablas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Actualizando SupabaseServiceOptimized...');

        $servicePath = app_path('Services/SupabaseServiceOptimized.php');

        if (!File::exists($servicePath)) {
            $this->error('❌ El archivo SupabaseServiceOptimized.php no existe');
            return 1;
        }

        $content = File::get($servicePath);

        // Agregar métodos para Profile
        $profileMethods = '
    /**
     * 👤 MÉTODOS PARA PROFILE
     */
    public function getProfile()
    {
        $cacheKey = "profile_data";

        return Cache::remember($cacheKey, $this->cacheTime, function () {
            $response = $this->client->from("profile")
                ->select("*")
                ->eq("is_active", true)
                ->single()
                ->execute();

            if ($response->status !== 200) {
                Log::error("Error al obtener perfil", ["response" => $response->body]);
                return null;
            }

            return $response->data;
        });
    }

    public function updateProfile($data)
    {
        Cache::forget("profile_data");

        $data["updated_at"] = now()->toISOString();

        $response = $this->client->from("profile")
            ->update($data)
            ->eq("is_active", true)
            ->execute();

        if ($response->status !== 200 && $response->status !== 204) {
            Log::error("Error al actualizar perfil", [
                "data" => $data,
                "response" => $response->body
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

        $response = $this->client->from("profile")
            ->insert($data)
            ->execute();

        if ($response->status !== 201) {
            Log::error("Error al crear perfil", [
                "data" => $data,
                "response" => $response->body
            ]);
            return false;
        }

        return $response->data[0] ?? true;
    }';

        // Agregar métodos para Social Links
        $socialMethods = '
    /**
     * 🔗 MÉTODOS PARA SOCIAL LINKS
     */
    public function getSocialLinks($activeOnly = true)
    {
        $cacheKey = $activeOnly ? "social_links_active" : "social_links_all";

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($activeOnly) {
            $query = $this->client->from("social_links")->select("*");

            if ($activeOnly) {
                $query = $query->eq("is_active", true);
            }

            $response = $query->order("order", ["ascending" => true])->execute();

            if ($response->status !== 200) {
                Log::error("Error al obtener enlaces sociales", ["response" => $response->body]);
                return [];
            }

            return $response->data;
        });
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

        $response = $this->client->from("social_links")
            ->insert($data)
            ->execute();

        if ($response->status !== 201) {
            Log::error("Error al crear enlace social", [
                "data" => $data,
                "response" => $response->body
            ]);
            return false;
        }

        return $response->data[0] ?? true;
    }

    public function updateSocialLink($id, $data)
    {
        Cache::forget("social_links_active");
        Cache::forget("social_links_all");

        $data["updated_at"] = now()->toISOString();

        $response = $this->client->from("social_links")
            ->update($data)
            ->eq("id", $id)
            ->execute();

        if ($response->status !== 200 && $response->status !== 204) {
            Log::error("Error al actualizar enlace social", [
                "id" => $id,
                "data" => $data,
                "response" => $response->body
            ]);
            return false;
        }

        return true;
    }

    public function deleteSocialLink($id)
    {
        Cache::forget("social_links_active");
        Cache::forget("social_links_all");

        $response = $this->client->from("social_links")
            ->delete()
            ->eq("id", $id)
            ->execute();

        if ($response->status !== 200 && $response->status !== 204) {
            Log::error("Error al eliminar enlace social", [
                "id" => $id,
                "response" => $response->body
            ]);
            return false;
        }

        return true;
    }';

        // Agregar métodos para Site Settings
        $settingsMethods = '
    /**
     * ⚙️ MÉTODOS PARA SITE SETTINGS
     */
    public function getSiteSettings($group = null, $publicOnly = false)
    {
        $cacheKey = "site_settings_" . ($group ?? "all") . "_" . ($publicOnly ? "public" : "all");

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($group, $publicOnly) {
            $query = $this->client->from("site_settings")->select("*");

            if ($group) {
                $query = $query->eq("group", $group);
            }

            if ($publicOnly) {
                $query = $query->eq("is_public", true);
            }

            $response = $query->order("group")->order("key")->execute();

            if ($response->status !== 200) {
                Log::error("Error al obtener configuraciones", ["response" => $response->body]);
                return [];
            }

            return $response->data;
        });
    }

    public function getSiteSetting($key, $default = null)
    {
        $cacheKey = "site_setting_" . $key;

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($key, $default) {
            $response = $this->client->from("site_settings")
                ->select("value")
                ->eq("key", $key)
                ->single()
                ->execute();

            if ($response->status !== 200 || !$response->data) {
                return $default;
            }

            return $response->data["value"] ?? $default;
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

        $response = $this->client->from("site_settings")
            ->update($data)
            ->eq("key", $key)
            ->execute();

        if ($response->status !== 200 && $response->status !== 204) {
            Log::error("Error al actualizar configuración", [
                "key" => $key,
                "value" => $value,
                "response" => $response->body
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

        $response = $this->client->from("site_settings")
            ->insert($data)
            ->execute();

        if ($response->status !== 201) {
            Log::error("Error al crear configuración", [
                "data" => $data,
                "response" => $response->body
            ]);
            return false;
        }

        return $response->data[0] ?? true;
    }

    public function deleteSiteSetting($id)
    {
        Cache::forget("site_settings_*");

        $response = $this->client->from("site_settings")
            ->delete()
            ->eq("id", $id)
            ->execute();

        if ($response->status !== 200 && $response->status !== 204) {
            Log::error("Error al eliminar configuración", [
                "id" => $id,
                "response" => $response->body
            ]);
            return false;
        }

        return true;
    }';

        // Buscar el final de la clase y agregar los nuevos métodos
        $classEnd = strrpos($content, '}');
        if ($classEnd === false) {
            $this->error('❌ No se pudo encontrar el final de la clase');
            return 1;
        }

        $newContent = substr($content, 0, $classEnd) .
                     $profileMethods .
                     $socialMethods .
                     $settingsMethods .
                     "\n}";

        File::put($servicePath, $newContent);

        $this->info('✅ SupabaseServiceOptimized actualizado correctamente');
        $this->info('');
        $this->info('📊 Métodos agregados:');
        $this->info('   👤 Profile: getProfile, updateProfile, createProfile');
        $this->info('   🔗 Social Links: getSocialLinks, createSocialLink, updateSocialLink, deleteSocialLink');
        $this->info('   ⚙️  Site Settings: getSiteSettings, getSiteSetting, updateSiteSetting, createSiteSetting, deleteSiteSetting');
        $this->info('');
        $this->info('🎯 Ahora ejecuta: php artisan test:new-tables');

        return 0;
    }
}
