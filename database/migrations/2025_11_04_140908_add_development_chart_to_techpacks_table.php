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
            $table->string('development_chart_path')->nullable()->after('artwork_comments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('techpacks', function (Blueprint $table) {
            $table->dropColumn('development_chart_path');
        });
    }
};
