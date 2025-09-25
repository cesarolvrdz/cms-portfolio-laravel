<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestLoginCommand extends Command
{
    protected $signature = 'test:login {email?} {password?}';
    protected $description = 'Probar las credenciales de login';

    public function handle()
    {
        $email = $this->argument('email') ?: env('ADMIN_EMAIL');
        $password = $this->argument('password') ?: env('ADMIN_PASSWORD');

        $this->info("=== PRUEBA DE CREDENCIALES ===");
        $this->line("");

        $this->info("Credenciales desde .env:");
        $this->info("- ADMIN_EMAIL: " . env('ADMIN_EMAIL'));
        $this->info("- ADMIN_PASSWORD: " . env('ADMIN_PASSWORD'));
        $this->line("");

        $this->info("Credenciales a probar:");
        $this->info("- Email: " . $email);
        $this->info("- Contraseña: " . $password);
        $this->line("");

        // Simular verificación
        $adminEmail = env('ADMIN_EMAIL', 'admin@cms.local');
        $adminPassword = env('ADMIN_PASSWORD', 'admin123');

        if ($email === $adminEmail && $password === $adminPassword) {
            $this->info("✅ CREDENCIALES CORRECTAS");
            $this->info("El login debería funcionar con estas credenciales.");
        } else {
            $this->error("❌ CREDENCIALES INCORRECTAS");
            $this->error("Email esperado: " . $adminEmail);
            $this->error("Email proporcionado: " . $email);
            $this->error("¿Contraseñas coinciden? " . ($password === $adminPassword ? 'Sí' : 'No'));
        }

        $this->line("");
        $this->info("💡 Si las credenciales son correctas pero el login no funciona:");
        $this->info("1. Limpia la cache del navegador (Ctrl+F5)");
        $this->info("2. Intenta en una ventana de incógnito");
        $this->info("3. Verifica que no haya espacios extra en el .env");
    }
}
