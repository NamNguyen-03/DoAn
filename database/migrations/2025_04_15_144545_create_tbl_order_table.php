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
        Schema::create('tbl_order', function (Blueprint $table) {
            $table->id('order_id');
            $table->integer('order_details_id');
            $table->integer('customer_id');
            $table->integer('shipping_id');
            $table->integer('order_status');
            $table->string('order_ship');
            $table->string('order_coupon');
            $table->timestamp('create_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_order');
    }
};
