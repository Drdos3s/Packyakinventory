<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Locations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->char('squareID', 255);
            $table->char('businessName', 255);
            $table->char('businessEmail', 255);
            $table->char('locationAddressLine1', 255);
            $table->char('locationAddressLine2', 255);
            $table->char('locationCity', 255);
            $table->char('locationState', 255);
            $table->char('locationZip', 255);
            $table->char('locationPhone', 255);
            $table->char('locationNickname', 255);
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
        Schema::drop('locations');
    }
}
