<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeginKeysToGiftAct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gift_act', function (Blueprint $table) {
            //
            $table->foreign('who_id')->references('id')->on('users');
            $table->foreign('target_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gift_act', function (Blueprint $table) {
            //
            $table->dropForeign('who_id');
            $table->dropForeign('target_id');
        });
    }
}
