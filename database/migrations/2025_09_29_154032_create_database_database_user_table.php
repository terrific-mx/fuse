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
        Schema::create('database_database_user', function (Blueprint $table) {
$table->foreignId('database_id')->constrained('databases')->cascadeOnDelete();
$table->foreignId('database_user_id')->constrained('database_users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_database_user');
    }
};
