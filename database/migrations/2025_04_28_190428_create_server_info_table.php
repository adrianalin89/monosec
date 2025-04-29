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
        Schema::create('server_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('os_info')->nullable();
            $table->string('php_version', 20)->nullable();
            $table->string('composer_version', 20)->nullable();
            $table->string('redis_version', 20)->nullable();
            $table->string('opensearch_version', 20)->nullable();
            $table->string('mariadb_version', 20)->nullable();
            $table->string('rabbitmq_version', 20)->nullable();
            $table->text('other_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_info');
    }
};
