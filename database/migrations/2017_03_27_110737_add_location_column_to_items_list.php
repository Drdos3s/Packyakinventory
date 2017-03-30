<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationColumnToItemsList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //add location id column to the item list to be sold at to update with square schema
        Schema::table('inventoryList', function (Blueprint $table) {
            $table->string('itemLocationID', 20)->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //remove column of the location Id
        Schema::table('inventoryList', function (Blueprint $table) {
            $table->dropColumn('itemLocationID');
        });
    }
}
