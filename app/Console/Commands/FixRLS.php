<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixRLS extends Command
{
    protected $signature = 'fix:rls';
    protected $description = 'Instrucciones para arreglar RLS en Supabase';

    public function handle()
    {
        $this->info('🔐 === ARREGLO DE ROW LEVEL SECURITY ===');
        $this->newLine();

        $this->info('🎯 PROBLEMA IDENTIFICADO:');
        $this->line('   Las políticas RLS están bloqueando UPDATE y DELETE');
        $this->newLine();

        $this->info('🚀 SOLUCIÓN RÁPIDA - Ve al Dashboard de Supabase:');
        $this->line('   1. Abre https://app.supabase.com');
        $this->line('   2. Selecciona tu proyecto');
        $this->line('   3. Ve a Authentication > Policies');
        $this->line('   4. Busca la tabla "projects"');
        $this->line('   5. Crea estas políticas:');
        $this->newLine();

        $this->info('📝 POLÍTICA UPDATE:');
        $this->line('   Nombre: Allow all updates');
        $this->line('   Tipo: UPDATE');
        $this->line('   Target roles: public');
        $this->line('   USING: true');
        $this->line('   WITH CHECK: true');
        $this->newLine();

        $this->info('📝 POLÍTICA DELETE:');
        $this->line('   Nombre: Allow all deletes');
        $this->line('   Tipo: DELETE');
        $this->line('   Target roles: public');
        $this->line('   USING: true');
        $this->newLine();

        $this->info('🎉 DESPUÉS DE CREAR LAS POLÍTICAS:');
        $this->line('   • UPDATE funcionará correctamente');
        $this->line('   • DELETE funcionará correctamente');
        $this->line('   • Tu CMS estará 100% funcional');
        $this->newLine();

        $this->info('✅ ¿Quieres probar cuando hayas terminado?');
        $this->line('   Ejecuta: php artisan test:crud');
    }
}
