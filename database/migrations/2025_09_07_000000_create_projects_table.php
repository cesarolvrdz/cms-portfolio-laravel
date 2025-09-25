<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->json('tech');
            $table->string('link')->nullable();
            $table->string('image');
            $table->enum('status', ['completed', 'in-progress', 'planned']);
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('projects');
    }
};
