<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('body');
            $table->unsignedBigInteger('tag_id');
            $table->boolean('favorited')->default(false);
            // $table->integer('favoritesCount')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraint
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
};
