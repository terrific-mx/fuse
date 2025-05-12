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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->foreignId('server_provider_id')->index();
            $table->string('provider_server_id')->nullable();
            $table->string('name');
            $table->string('size', 25);
            $table->string('region', 25);
            $table->integer('port')->default(22);
            $table->timestamp('provisioning_job_dispatched_at')->nullable();
            $table->string('public_address')->nullable();
            $table->string('status', 25);
            $table->string('username');
            $table->string('sudo_password');
            $table->string('database_password');
            $table->longText('public_key')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
