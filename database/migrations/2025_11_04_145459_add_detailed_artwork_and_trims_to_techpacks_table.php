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
        Schema::table('techpacks', function (Blueprint $table) {
            // Campo JSON para almacenar los artwork placements detallados
            $table->json('artwork_placements')->nullable()->after('artwork_comments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('techpacks', function (Blueprint $table) {
            $table->dropColumn('artwork_placements');
        });
    }
};
