<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('techpacks', function (Blueprint $table) {
            // Eliminar la relación con quotes
            $table->dropForeign(['quote_id']);
            $table->dropColumn('quote_id');

            // Agregar relación con clients
            $table->foreignId('client_id')->after('id')->constrained()->cascadeOnDelete();

            // Agregar campos adicionales útiles
            $table->string('original_file_path')->nullable()->after('image_url');
            $table->timestamp('uploaded_at')->nullable()->after('original_file_path');
        });
    }

    public function down(): void
    {
        Schema::table('techpacks', function (Blueprint $table) {
            // Revertir cambios
            $table->dropForeign(['client_id']);
            $table->dropColumn(['client_id', 'original_file_path', 'uploaded_at']);

            // Restaurar relación con quotes
            $table->foreignId('quote_id')->after('id')->constrained()->cascadeOnDelete();
        });
    }
};
