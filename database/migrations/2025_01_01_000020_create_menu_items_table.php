<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('image')->nullable();
            $table->integer('preparation_time')->default(15); // in minutes
            $table->boolean('is_available')->default(true);
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_spicy')->default(false);
            $table->string('calories')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
