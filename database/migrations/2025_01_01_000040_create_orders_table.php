<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('waiter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cooker_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('table_id')->nullable()->constrained('tables')->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->integer('customer_count')->default(1);
            $table->string('order_type')->default('dine_in'); // dine_in, takeaway, delivery
            $table->string('status')->default('ordered');
            // ordered -> accepted -> cooking -> ready -> delivered -> paid
            // cancelled (terminal)
            $table->text('notes')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('cooking_started_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
