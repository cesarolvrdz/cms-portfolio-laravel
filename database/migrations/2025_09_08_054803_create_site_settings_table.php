<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key', 100)->unique(); // Clave única del setting
            $table->text('value'); // Valor del setting (puede ser JSON)
            $table->string('type', 20)->default('text'); // text, boolean, json, number, url, email
            $table->string('group', 50)->default('general'); // Para agrupar settings
            $table->string('label', 100); // Etiqueta para mostrar en UI
            $table->text('description')->nullable(); // Descripción del setting
            $table->boolean('is_public')->default(false); // Si es accesible desde API pública
            $table->timestamps();

            // Índices
            $table->index(['group', 'key']);
            $table->index('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
