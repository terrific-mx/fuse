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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->index();
            $table->foreignId('source_provider_id')->index();
            $table->string('domain');
            $table->string('repository');
            $table->string('branch');
            $table->string('status', 25);
            $table->string('tls');
            $table->string('type');
            $table->string('web_directory');
            $table->string('php_version');
            $table->integer('releases_retention')->default(10);
            $table->json('shared_directories');
            $table->json('writeable_directories');
            $table->json('shared_files');
            $table->longText('before_update_hook')->nullable();
            $table->longText('after_update_hook')->nullable();
            $table->longText('before_activate_hook')->nullable();
            $table->longText('after_activate_hook')->nullable();
            $table->timestamps();

            $table->unique(['server_id', 'domain']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
