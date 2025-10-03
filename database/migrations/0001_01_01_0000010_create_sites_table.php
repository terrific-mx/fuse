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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->cascadeOnDelete();
                $table->string('hostname');
                $table->string('php_version');
                $table->string('repository_url')->nullable();
                $table->string('repository_branch')->nullable();
                $table->json('shared_directories')->nullable();
                $table->json('shared_files')->nullable();
                $table->json('writable_directories')->nullable();
                $table->text('script_before_deploy')->nullable();
                $table->text('script_after_deploy')->nullable();
                $table->text('script_before_activate')->nullable();
                $table->text('script_after_activate')->nullable();
                $table->timestamp('caddy_installed_at')->nullable();
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
