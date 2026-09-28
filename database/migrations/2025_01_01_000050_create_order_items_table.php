<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->string('menu_item_name'); // snapshot of name at order time
            $table->decimal('menu_item_price', 10, 2); // snapshot of price at order time
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, cooking, ready, served
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
