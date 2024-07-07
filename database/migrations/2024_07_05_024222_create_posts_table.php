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
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('content');
            $table->string('author');
            $table->timestamp('publishedDate')->nullable();
            $table->string('status');
            $table->unsignedInteger('createdBy_user_id');
            $table->unsignedInteger('updatedBy_user_id')->nullable();
            $table->timestamps();

            $table->foreign('createdBy_user_id')->references('id')->on('users');
            $table->foreign('updatedBy_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
