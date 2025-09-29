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
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('disk');
                $table->json('databases');
                $table->json('directories');
                $table->integer('retention');
                $table->string('frequency');
                $table->boolean('notify_failure')->default(false);
                $table->boolean('notify_success')->default(false);
                $table->string('notification_email')->nullable();
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
