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
        Schema::table('quotes', function (Blueprint $table) {
            // WFX / Buyer fields
            $table->string('buyer')->nullable()->after('client_id');
            $table->string('buyer_department')->nullable()->after('buyer');
            $table->string('season')->nullable()->after('buyer_department');

            // Factory price (renaming unit_price conceptually, keeping column)
            // Lead time
            $table->integer('lead_time_days')->nullable()->after('profit_margin');

            // Minimums (JSON for flexibility)
            $table->json('minimums_by_color')->nullable()->after('lead_time_days');
            $table->integer('minimums_by_style')->nullable()->after('minimums_by_color');
            $table->json('minimums_by_fabric')->nullable()->after('minimums_by_style');

            // Size range
            $table->string('size_range')->nullable()->after('minimums_by_fabric');

            // Fabric, Trims, Artwork
            $table->json('fabric_information')->nullable()->after('size_range');
            $table->json('trims_list')->nullable()->after('fabric_information');
            $table->json('artwork_details')->nullable()->after('trims_list');

            // Costsheet data (JSON for breakdown)
            $table->json('costsheet_data')->nullable()->after('artwork_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'buyer',
                'buyer_department',
                'season',
                'lead_time_days',
                'minimums_by_color',
                'minimums_by_style',
                'minimums_by_fabric',
                'size_range',
                'fabric_information',
                'trims_list',
                'artwork_details',
                'costsheet_data',
            ]);
        });
    }
};
