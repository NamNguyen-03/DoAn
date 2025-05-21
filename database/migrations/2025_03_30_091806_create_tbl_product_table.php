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
        Schema::create('tbl_product', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name');
            $table->integer('product_quantity');
            $table->integer('product_sold');
            $table->string('product_slug')->unique();
            $table->integer('category_id');
            $table->integer('brand_id');
            $table->text('product_desc')->nullable();
            $table->text('product_tags')->nullable();
            $table->text('product_content')->nullable();
            $table->string('product_price', 10, 2);
            $table->string('product_image');
            $table->integer('product_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_product');
    }
};
