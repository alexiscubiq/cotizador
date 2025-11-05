<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sample_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('techpack_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('requested_by');
            $table->date('requested_at');
            $table->date('eta')->nullable();
            $table->json('sizes'); // {S: {client:1, wts:1, received:0}, M: {...}}
            $table->enum('status', ['requested', 'in_production', 'shipped', 'received', 'approved', 'rejected'])->default('requested');
            $table->string('shipping_address')->nullable();
            $table->string('courier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->date('shipped_at')->nullable();
            $table->integer('packages')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->integer('attachments_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sample_orders');
    }
};
