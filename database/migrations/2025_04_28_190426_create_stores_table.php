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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('url');
            $table->string('admin_path')->nullable();
            $table->enum('platform_type', ['magento_ce', 'mage-os', 'magento_ee'])->nullable();
            $table->string('magento_version', 20)->nullable();
            $table->string('repository_url')->nullable();
            $table->text('contact_info')->nullable();
            $table->string('developer_info')->nullable();
            $table->boolean('has_cpanel')->default(false);
            $table->boolean('has_root_access')->default(false);
            $table->timestamp('last_check')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
