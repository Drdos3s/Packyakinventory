<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PurchaseOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Create table for purchase orders
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po_name');
            $table->string('po_status');
            $table->string('po_invoice_number');
            $table->string('po_vendor');
            $table->timestamps();
        });

        //Create table for purchase order items
        /*Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('related_po_id');
            $table->string('square_variation_id');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('purchase_orders');
    }
}
