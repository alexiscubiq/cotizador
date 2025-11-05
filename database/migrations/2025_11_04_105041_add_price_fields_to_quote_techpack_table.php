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
        Schema::table('quote_techpack', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->nullable()->after('techpack_id');
            $table->integer('quantity')->nullable()->after('unit_price');
            $table->decimal('total_price', 12, 2)->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_techpack', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'quantity', 'total_price']);
        });
    }
};
