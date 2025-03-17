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
        Schema::create('stock_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('head_office_stock_id');
            $table->unsignedBigInteger('gas_type_id')->nullable();
            $table->unsignedBigInteger('outlet_id')->nullable(); // Make this nullable
            $table->decimal('total_quantity', 10, 2)->nullable();
            $table->decimal('allocated_quantity', 10, 2)->nullable();
            $table->datetime('allocation_date')->nullable();
            $table->enum('status', [
                'pending',
                'reserved',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('head_office_stock_id')
                  ->references('id')
                  ->on('head_office_stocks')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_allocations');
    }
};
