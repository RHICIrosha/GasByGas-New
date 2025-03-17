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
        Schema::create('outlet_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
            $table->foreignId('gas_type_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('available_quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->integer('minimum_stock_level')->default(5);
            $table->timestamp('last_restock_date')->nullable();
            $table->string('status')->default('normal'); // normal, low, critical
            $table->timestamps();

            // Unique constraint to ensure one gas type per outlet
            $table->unique(['outlet_id', 'gas_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_stocks');
    }
};
