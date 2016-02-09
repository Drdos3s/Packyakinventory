<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InventoryList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventoryList', function (Blueprint $table) {
            $table->increments('id');
            $table->char('squareItemID', 255); //<- use the item id from the variation if it is easier
            $table->char('itemName', 255);
            $table->char('itemCategoryName', 255);
            $table->char('itemCategoryID', 255);
            $table->char('itemVariationName', 255);
            $table->char('itemVariationID', 255);
            $table->char('itemVariationPrice', 255);
            $table->char('itemVariationSKU', 255);
            $table->char('locationSoldAt', 255);
            $table->char('itemVariationInventory', 255);
            $table->char('itemVendorToOrderFrom', 255);
            $table->integer('itemVariationUnitCost');
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
        Schema::drop('inventoryList');
    }
}
