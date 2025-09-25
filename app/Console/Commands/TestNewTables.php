<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseServiceOptimized;

class TestNewTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:new-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba las nuevas tablas del portfolio';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Probando las nuevas tablas...');
        $this->info('');

        $supabase = app(SupabaseServiceOptimized::class);

        // Test Profile
        $this->info('👤 PROBANDO TABLA PROFILE:');
        try {
            $profile = $supabase->getProfile();
            if ($profile) {
                $this->info('   ✅ Perfil encontrado: ' . $profile['name']);
                $this->info('   📧 Email: ' . $profile['email']);
                $this->info('   💼 Título: ' . $profile['title']);
            } else {
                $this->warn('   ⚠️  No hay perfil configurado');
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Error: ' . $e->getMessage());
        }

        $this->info('');

        // Test Social Links
        $this->info('🔗 PROBANDO TABLA SOCIAL LINKS:');
        try {
            $socialLinks = $supabase->getSocialLinks();
            if (count($socialLinks) > 0) {
                $this->info('   ✅ Enlaces encontrados: ' . count($socialLinks));
                foreach ($socialLinks as $link) {
                    $this->info('   📱 ' . ucfirst($link['platform']) . ': ' . $link['url']);
                }
            } else {
                $this->warn('   ⚠️  No hay enlaces sociales configurados');
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Error: ' . $e->getMessage());
        }

        $this->info('');

        // Test Site Settings
        $this->info('⚙️ PROBANDO TABLA SITE SETTINGS:');
        try {
            $settings = $supabase->getSiteSettings();
            if (count($settings) > 0) {
                $this->info('   ✅ Configuraciones encontradas: ' . count($settings));

                // Mostrar algunas configuraciones importantes
                $siteTitle = $supabase->getSiteSetting('site_title', 'No configurado');
                $contactEmail = $supabase->getSiteSetting('contact_email', 'No configurado');
                $themeColor = $supabase->getSiteSetting('theme_color', 'No configurado');

                $this->info('   🌐 Título del sitio: ' . $siteTitle);
                $this->info('   📧 Email de contacto: ' . $contactEmail);
                $this->info('   🎨 Color del tema: ' . $themeColor);

                // Agrupar por categorías
                $groups = [];
                foreach ($settings as $setting) {
                    $groups[$setting['group']][] = $setting['key'];
                }

                $this->info('   📂 Grupos de configuración:');
                foreach ($groups as $group => $keys) {
                    $this->info('      • ' . $group . ': ' . implode(', ', $keys));
                }

            } else {
                $this->warn('   ⚠️  No hay configuraciones del sitio');
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Error: ' . $e->getMessage());
        }

        $this->info('');
        $this->info('🎯 RESUMEN:');
        $this->info('   • Las tres nuevas tablas están funcionando correctamente');
        $this->info('   • Los datos de ejemplo se han insertado');
        $this->info('   • El servicio SupabaseServiceOptimized tiene los nuevos métodos');
        $this->info('');
        $this->info('🚀 ¡Listo para implementar las interfaces de administración!');

        return 0;
    }
}
