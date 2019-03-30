<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeginKeysToGirlTargetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('girl_target', function (Blueprint $table) {
            $table->foreign('girl_id')->references('id')->on('girls');
            $table->foreign('target_id')->references('id')->on('target');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('girl_target', function (Blueprint $table) {
            $table->dropForeign("girl_id");
            $table->dropForeign("target_id");
        });
    }
}
