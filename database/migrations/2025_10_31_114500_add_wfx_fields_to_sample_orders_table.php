<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sample_orders', function (Blueprint $table) {
            $table->string('wfx_sample_id')->nullable()->after('tracking_number');
            $table->timestamp('synced_to_wfx_at')->nullable()->after('wfx_sample_id');
            $table->json('wfx_metadata')->nullable()->after('synced_to_wfx_at');
        });
    }

    public function down(): void
    {
        Schema::table('sample_orders', function (Blueprint $table) {
            $table->dropColumn(['wfx_sample_id', 'synced_to_wfx_at', 'wfx_metadata']);
        });
    }
};
