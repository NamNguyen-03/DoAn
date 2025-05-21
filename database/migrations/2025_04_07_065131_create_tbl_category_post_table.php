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
        Schema::create('tbl_category_post', function (Blueprint $table) {
            $table->id('cate_post_id');
            $table->string('cate_post_name');
            $table->string('cate_post_slug')->unique();
            $table->string('cate_post_desc');
            $table->integer('cate_post_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_category_post');
    }
};
