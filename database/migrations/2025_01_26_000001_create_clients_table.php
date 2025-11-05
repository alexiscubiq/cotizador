<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            // Información Básica
            $table->string('name'); // Nombre de la Empresa
            $table->string('legal_name')->nullable(); // Razón Social
            $table->string('tax_id')->nullable(); // RUC/ID Fiscal
            $table->string('contact')->nullable(); // Nombre de Contacto

            // Contacto
            $table->string('email')->nullable();
            $table->string('phone')->nullable(); // Teléfono
            $table->string('whatsapp')->nullable(); // WhatsApp
            $table->text('address')->nullable(); // Dirección
            $table->string('city')->nullable(); // Ciudad
            $table->string('country_code')->nullable(); // País (Código)

            // Ubicación Internacional
            $table->string('country')->nullable(); // País
            $table->string('timezone')->nullable(); // Zona Horaria
            $table->string('currency', 3)->default('USD'); // Moneda

            // Configuración Comercial
            $table->decimal('credit_limit', 12, 2)->nullable(); // Límite de Crédito
            $table->string('payment_terms')->nullable(); // Términos de Pago
            $table->string('logo_url')->nullable(); // Logo
            $table->boolean('is_active')->default(true); // Activo

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
