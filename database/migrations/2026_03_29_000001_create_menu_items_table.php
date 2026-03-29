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
            $table->string('name');
            $table->string('category'); // Kopi, Non-Kopi, Makanan, Dessert
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // emoji atau path gambar
            $table->boolean('is_available')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->decimal('rating', 3, 1)->default(0.0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
