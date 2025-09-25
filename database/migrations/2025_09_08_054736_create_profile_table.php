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
        Schema::create('profile', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('title', 150); // ej: "Full Stack Developer"
            $table->text('bio'); // Biografía/descripción personal
            $table->string('avatar')->nullable(); // URL del avatar/foto de perfil
            $table->string('resume_url')->nullable(); // URL al CV/Resume
            $table->string('location', 100)->nullable(); // Ubicación
            $table->string('phone', 20)->nullable(); // Teléfono
            $table->string('email', 100)->nullable(); // Email público
            $table->json('skills')->nullable(); // Array de habilidades
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile');
    }
};
