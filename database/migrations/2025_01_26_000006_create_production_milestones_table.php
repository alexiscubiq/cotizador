<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->string('milestone'); // Hilado, Tejido, Corte, Lavado, Planchado, Empaque
            $table->date('planned_at')->nullable();
            $table->date('actual_at')->nullable();
            $table->integer('delay_days')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'delayed'])->default('pending');
            $table->text('comment')->nullable();
            $table->string('updated_by')->nullable();
            $table->integer('attachments_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_milestones');
    }
};
