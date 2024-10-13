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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID (Primary key)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 255); // Product name
            $table->text('description')->nullable(); // Optional product description
            $table->string('vendor', 100); // Vendor name
            $table->string('sku', 50)->unique(); // Unique SKU for the product
            $table->decimal('price', 8, 2); // Product price (e.g., 59.00)
            $table->boolean('availability')->default(true); // Product availability
            $table->enum('size', ['XS', 'S', 'M', 'L', 'XL'])->nullable(); // Size options
            $table->string('color', 50)->nullable(); // Color of the product
            $table->timestamps(); // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
