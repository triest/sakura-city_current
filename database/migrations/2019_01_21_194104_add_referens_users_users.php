<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferensUsersUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('user_user', function (Blueprint $table) {
            $table->foreign('my_id')->references('id')->on('users');
            $table->foreign('other_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('user_user', function (Blueprint $table) {
            $table->dropForeign('my_id');
        });
        Schema::table('user_user', function (Blueprint $table) {
            $table->dropForeign('other_id');
        });
    }
}
