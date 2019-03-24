<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGirlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('girls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->unsigned()->index();
            $table->string('name', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->text('description')->nullable();
            $table->text('private')->nullable();
            $table->string('main_image', 255)->nullable();
            $table->dateTime('biginvip')->nullable();
            $table->dateTime('endvip')->nullable();
            $table->string('sex', 10)->nullable();
            $table->integer('age')->nullable()->unsigned();
            $table->integer('weight')->nullable()->unsigned();
            $table->integer('height')->nullable()->unsigned();
            $table->string('meet', 10)->nullable();
            $table->boolean('banned');
            $table->integer('country_id')->nullable()->unsigned()->index();
            $table->integer('region_id')->nullable()->unsigned()->index();
            $table->integer('city_id')->nullable()->unsigned()->index();
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
        Schema::dropIfExists('girls');
    }
}
