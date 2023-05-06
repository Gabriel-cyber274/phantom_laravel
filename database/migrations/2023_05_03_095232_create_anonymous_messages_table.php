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
        Schema::create('anonymous_messages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('anonymous_id');
            $table->longText('message');
            $table->string('review');
            $table->boolean('hint');
            $table->string('hint_text')->nullable();
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
        Schema::dropIfExists('anonymous_messages');
    }
};
