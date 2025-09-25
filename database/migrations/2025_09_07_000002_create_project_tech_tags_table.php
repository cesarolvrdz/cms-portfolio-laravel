<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('project_tech_tags', function (Blueprint $table) {
            $table->uuid('project_id');
            $table->uuid('tech_tag_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('tech_tag_id')->references('id')->on('tech_tags')->onDelete('cascade');
        });
    }
    public function down() {
        Schema::dropIfExists('project_tech_tags');
    }
};
