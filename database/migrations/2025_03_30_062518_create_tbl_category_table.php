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
        Schema::create('tbl_category', function (Blueprint $table) {
            $table->id('category_id'); // Primary key, auto-increment
            $table->string('category_name', 255);
            $table->text('category_desc');
            $table->integer('category_status');
            $table->string('category_slug', 255)->nullable();
            $table->unsignedBigInteger('category_parent')->nullable(); // Để liên kết đến danh mục cha nếu có
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_category');
    }
};
