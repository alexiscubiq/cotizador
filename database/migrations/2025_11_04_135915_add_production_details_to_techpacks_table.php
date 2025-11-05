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
            // Production details
            $table->integer('lead_time_days')->nullable()->after('season');
            $table->integer('minimums_by_color')->nullable()->after('lead_time_days');
            $table->integer('minimums_by_style')->nullable()->after('minimums_by_color');
            $table->integer('minimums_by_fabric')->nullable()->after('minimums_by_style');
            $table->string('size_range')->nullable()->after('minimums_by_fabric');
            $table->json('trims')->nullable()->after('size_range');
            $table->text('artwork_comments')->nullable()->after('trims');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('techpacks', function (Blueprint $table) {
            $table->dropColumn([
                'lead_time_days',
                'minimums_by_color',
                'minimums_by_style',
                'minimums_by_fabric',
                'size_range',
                'trims',
                'artwork_comments',
            ]);
        });
    }
};
