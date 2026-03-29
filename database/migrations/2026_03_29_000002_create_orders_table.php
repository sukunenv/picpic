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
            $table->string('customer_name');
            $table->string('table_number')->nullable();
            $table->enum('status', ['pending', 'processing', 'done'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->enum('payment_method', ['qris', 'transfer', 'cash', 'pay_later'])->default('cash');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
