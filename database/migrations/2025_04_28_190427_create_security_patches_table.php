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
        Schema::create('security_patches', function (Blueprint $table) {
            $table->id();
            $table->string('magento_version', 20)->nullable();
            $table->string('patch_name');
            $table->date('release_date')->nullable();
            $table->enum('type', ['security', 'feature'])->default('feature');
            $table->tinyInteger('severity_score')->unsigned()->nullable(); // 0-10
            $table->enum('severity_level', ['lithe', 'critical', 'severe'])->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_patches');
    }
};
