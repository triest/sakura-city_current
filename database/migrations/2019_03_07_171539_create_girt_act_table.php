<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGirtActTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_act', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('present_id')->unsigned()->index();
            $table->integer('who_id')->unsigned()->index();
            $table->integer('target_id')->unsigned()->index();
            $table->string('comment', 191)->nullable();
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
        Schema::dropIfExists('gift_act');
    }
}
