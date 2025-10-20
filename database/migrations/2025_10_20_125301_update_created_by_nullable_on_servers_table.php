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
        Schema::table('servers', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign('servers_created_by_foreign');
            // Make the column nullable
            $table->unsignedBigInteger('created_by')->nullable()->change();
            // Add the new foreign key with nullOnDelete
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign('servers_created_by_foreign');
            // Make the column not nullable
            $table->unsignedBigInteger('created_by')->nullable(false)->change();
            // Restore the original foreign key with cascadeOnDelete
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
