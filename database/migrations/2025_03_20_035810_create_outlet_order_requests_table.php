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
        Schema::create('outlet_order_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->string('request_number')->unique();
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Partial', 'Fulfilled', 'Cancelled'])->default('Pending');
            $table->text('notes')->nullable();
            $table->date('requested_date');
            $table->date('delivery_date')->nullable();
            $table->foreignId('manager_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_order_requests');
    }
};
