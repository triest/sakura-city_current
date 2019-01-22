<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferensesToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('requwest', function (Blueprint $table) {
            $table->foreign('who_id')->references('id')->on('users');
            $table->foreign('target_id')->references('id')->on('users');
        });
    }

}
