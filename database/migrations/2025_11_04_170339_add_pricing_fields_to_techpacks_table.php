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
            $table->decimal('unit_price', 10, 2)->nullable()->after('costsheet');
            $table->decimal('profit_margin', 5, 2)->nullable()->after('unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('techpacks', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'profit_margin']);
        });
    }
};
