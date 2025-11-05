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
            $table->string('style_code')->nullable()->after('code');
            $table->string('buyer')->nullable()->after('client_id');
            $table->string('buyer_department')->nullable()->after('buyer');
            $table->string('season')->nullable()->after('buyer_department');
            $table->string('wfx_id')->nullable()->after('style_code');
            $table->timestamp('synced_to_wfx_at')->nullable()->after('wfx_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('techpacks', function (Blueprint $table) {
            $table->dropColumn([
                'style_code',
                'buyer',
                'buyer_department',
                'season',
                'wfx_id',
                'synced_to_wfx_at'
            ]);
        });
    }
};
