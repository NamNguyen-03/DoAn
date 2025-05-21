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
        Schema::create('tbl_statistic', function (Blueprint $table) {
            $table->id('id_statistic');
            $table->string('order_date');
            $table->integer('quantity');
            $table->integer('total_order');
            $table->string('sales');
            $table->string('profit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_statistic');
    }
};
