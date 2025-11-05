<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Empresa
            $table->string('contact')->nullable(); // Contacto
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('country')->nullable(); // País
            $table->string('city')->nullable(); // Ciudad
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
