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
        Schema::create('tbl_post', function (Blueprint $table) {
            $table->id('post_id');
            $table->string('post_title');
            $table->string('post_slug')->unique();
            $table->string('post_desc');
            $table->string('post_content');
            $table->string('post_image');
            $table->integer('cate_post_id');
            $table->integer('post_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_post');
    }
};
