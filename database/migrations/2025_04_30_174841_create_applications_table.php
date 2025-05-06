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
