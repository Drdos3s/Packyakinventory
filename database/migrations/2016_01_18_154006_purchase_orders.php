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
            $table->string('po_status')->default('Pending');
            $table->string('po_invoice_number');
            $table->string('po_vendor');
            $table->string('po_location');
            $table->integer('po_subtotal')->default(0);
            $table->double('po_tax_rate',5,3)->default(0.083);
            $table->double('po_shipping_cost')->default(0.00);
            $table->double('po_total_cost',15,2)->default(0.00);
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
        Schema::drop('purchase_orders');
    }
}
