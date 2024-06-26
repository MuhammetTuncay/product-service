<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stock', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('stock_location_id')->constrained('stock_locations')->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
            $table->primary(['product_id', 'stock_location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stock');
    }
};
