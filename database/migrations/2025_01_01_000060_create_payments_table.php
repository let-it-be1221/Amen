<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cashier_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('subtotal', 10, 2)->default(0); // sum of all delivered items
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('service_charge', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0); // total payable
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('change', 10, 2)->default(0);
            $table->string('payment_method')->default('cash'); // cash, card, mobile
            $table->string('status')->default('completed'); // pending, completed, refunded
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
