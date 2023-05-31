<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('room_name');
            $table->bigInteger('creator_id');
            $table->bigInteger('creator_avatar');
            $table->boolean('block')->nullable();
            $table->boolean('report')->nullable();
            $table->boolean('reveal')->nullable();
            $table->boolean('links')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
};
