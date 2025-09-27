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
                $table->string('type');
                $table->string('web_folder')->default('/public');
                $table->boolean('zero_downtime')->default(true);
                $table->string('repository_url')->nullable();
                $table->string('repository_branch')->nullable();
                $table->boolean('use_deploy_key')->default(false);
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
