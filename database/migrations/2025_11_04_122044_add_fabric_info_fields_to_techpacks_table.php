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
            // Fabric Information Fields (Punto D: Información de Materiales)
            $table->string('fabric_construction')->nullable()->after('garment_type'); // jersey, rib, etc.
            $table->string('fabric_yarn_count')->nullable()->after('fabric_construction'); // 30/1, 18/1, etc.
            $table->string('fabric_content')->nullable()->after('fabric_yarn_count'); // 100% Cotton, mezclas, etc.
            $table->string('fabric_dyeing_type')->nullable()->after('fabric_content'); // Piece Dye, Yarn Dye, etc.
            $table->string('fabric_weight')->nullable()->after('fabric_dyeing_type'); // 230 gr/m2, etc.
            $table->string('fabric_width')->nullable()->after('fabric_weight'); // CW: 172, etc.
            $table->text('fabric_finishing')->nullable()->after('fabric_width'); // Acabados especiales
            $table->string('fabric_article_code')->nullable()->after('fabric_finishing'); // Código WFX o artículo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('techpacks', function (Blueprint $table) {
            $table->dropColumn([
                'fabric_construction',
                'fabric_yarn_count',
                'fabric_content',
                'fabric_dyeing_type',
                'fabric_weight',
                'fabric_width',
                'fabric_finishing',
                'fabric_article_code',
            ]);
        });
    }
};
