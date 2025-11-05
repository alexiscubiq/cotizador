<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tnas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // TNA Plan Name
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->json('milestones'); // Array of milestone objects
            $table->enum('status', ['draft', 'active', 'on_track', 'at_risk', 'delayed', 'completed'])->default('draft');
            $table->string('imported_from')->nullable(); // CSV, Manual, WFX, etc.
            $table->timestamp('imported_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Pivot table for TNA-Techpack relationship (one TNA can apply to multiple techpacks)
        Schema::create('techpack_tna', function (Blueprint $table) {
            $table->id();
            $table->foreignId('techpack_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tna_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('techpack_tna');
        Schema::dropIfExists('tnas');
    }
};
