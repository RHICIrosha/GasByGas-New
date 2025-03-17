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
        Schema::create('head_office_stocks', function (Blueprint $table) {
            $table->id();
        $table->unsignedBigInteger('gas_type_id');
        $table->decimal('total_quantity', 10, 2)->default(0);
        $table->decimal('available_quantity', 10, 2)->default(0);
        $table->decimal('allocated_quantity', 10, 2)->default(0);
        $table->decimal('minimum_stock_level', 10, 2)->default(0);

        $table->timestamp('last_restock_date')->nullable();
        $table->string('status')->default('normal');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('head_office_stocks');
    }
};
