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
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->text('comment');
            $table->unsignedInteger('createdBy_user_id')->nullable();
            $table->unsignedInteger('editedBy_user_id')->nullable();
            $table->unsignedInteger('parentComment_id')->nullable();
            $table->unsignedInteger('post_id');
            $table->boolean('removed');
            $table->timestamps();

            $table->foreign('createdBy_user_id')->references('id')->on('users');
            $table->foreign('editedBy_user_id')->references('id')->on('users');
            $table->foreign('parentComment_id')->references('id')->on('comments');
            $table->foreign('post_id')->references('id')->on('posts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
