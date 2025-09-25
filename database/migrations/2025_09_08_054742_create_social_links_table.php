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
        Schema::create('social_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('platform', 50); // github, linkedin, twitter, etc.
            $table->string('url'); // URL completa del perfil
            $table->string('icon', 50)->nullable(); // Nombre del icono (ej: bi-github)
            $table->string('color', 7)->nullable(); // Color hex para el botón (#000000)
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0); // Para ordenar los links
            $table->timestamps();

            // Índices para optimización
            $table->index(['is_active', 'order']);
            $table->unique(['platform']); // Solo un link por plataforma
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_links');
    }
};
