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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete(); // Link to the parent order
            $table->foreignId('product_id')->constrained()->cascadeOnDelete(); // Link to the product
            $table->integer('quantity'); // quantity of the product
            $table->decimal('price', 10, 2); // price of the product
            $table->string('name', 255); // name of the product (stored for convenience)
            $table->string('attributes')->nullable(); //Product attributes (e.g., color, price)
            $table->decimal('total', 10, 2); // total price of the product
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
