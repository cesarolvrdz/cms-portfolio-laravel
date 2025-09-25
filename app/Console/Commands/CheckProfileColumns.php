<?php

namespace App\Console\Commands;

use App\Services\SupabaseServiceOptimized;
use Illuminate\Console\Command;

class CheckProfileColumns extends Command
{
    protected $signature = 'profile:check-columns';
    protected $description = 'Check available columns in profile table';

    public function handle()
    {
        $this->info('Checking profile table columns...');

        try {
            $supabase = new SupabaseServiceOptimized();
            $profile = $supabase->getProfile();

            if ($profile) {
                $this->info('Available columns in profile:');
                foreach (array_keys($profile) as $key) {
                    $this->line("- $key");
                }

                $hasAvatar = isset($profile['avatar_url']);
                $this->info("\n¿Existe avatar_url? " . ($hasAvatar ? 'Sí ✓' : 'No ✗'));

                if (!$hasAvatar) {
                    $this->warn('La columna avatar_url no existe. Debes ejecutar el SQL en Supabase:');
                    $this->line('ALTER TABLE profile ADD COLUMN avatar_url TEXT;');
                }
            } else {
                $this->error('No se encontró perfil');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
