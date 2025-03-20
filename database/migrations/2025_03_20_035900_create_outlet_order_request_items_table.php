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
        Schema::create('outlet_order_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_order_request_id')->constrained('outlet_order_requests')->onDelete('cascade');
            $table->foreignId('gas_type_id')->constrained('gas_types')->onDelete('cascade');
            $table->integer('quantity_requested');
            $table->integer('quantity_approved')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_order_request_items');
    }
};
