<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Table 1" or "T-01"
            $table->integer('seats')->default(4);
            $table->string('location')->nullable(); // Indoor, Outdoor, VIP, etc.
            $table->string('status')->default('available'); // available, occupied, reserved
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
