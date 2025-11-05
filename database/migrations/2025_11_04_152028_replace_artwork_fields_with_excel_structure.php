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
            // Eliminar el campo JSON genérico
            $table->dropColumn('artwork_placements');

            // Agregar campos exactos del Excel - Development Chart
            $table->string('sketch_image')->nullable()->after('image_url');
            $table->string('front_artwork_image')->nullable()->after('sketch_image');
            $table->text('front_technique')->nullable()->after('front_artwork_image');
            $table->string('back_artwork_image')->nullable()->after('front_technique');
            $table->text('back_technique')->nullable()->after('back_artwork_image');
            $table->string('sleeve_artwork_image')->nullable()->after('back_technique');
            $table->text('sleeve_technique')->nullable()->after('sleeve_artwork_image');
            $table->string('color')->nullable()->after('sleeve_technique');
            $table->string('dyed_process')->nullable()->after('color');
            // fabric ya existe
            $table->date('initial_request_date')->nullable()->after('dyed_process');
            $table->date('sms_x_date')->nullable()->after('initial_request_date');
            $table->text('sms_comments')->nullable()->after('sms_x_date');
            $table->string('pp_sample')->nullable()->after('sms_comments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('techpacks', function (Blueprint $table) {
            // Revertir: eliminar los campos del Excel
            $table->dropColumn([
                'sketch_image',
                'front_artwork_image',
                'front_technique',
                'back_artwork_image',
                'back_technique',
                'sleeve_artwork_image',
                'sleeve_technique',
                'color',
                'dyed_process',
                'initial_request_date',
                'sms_x_date',
                'sms_comments',
                'pp_sample',
            ]);

            // Restaurar el campo JSON
            $table->json('artwork_placements')->nullable();
        });
    }
};
