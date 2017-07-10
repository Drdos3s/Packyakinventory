<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VendorsAddPhoneExtension extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Add in phone extensions column to vendors table
        Schema::table('vendors', function ($table) {
            $table->string('phone_extension');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function ($table) {
            $table->dropColumn('phone_extension');
        });
    }
}
