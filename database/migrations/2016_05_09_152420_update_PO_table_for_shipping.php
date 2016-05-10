<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePOTableForShipping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('po_status')->default('Pending')->change();
            $table->integer('po_shipping_cost')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            //$table->dropColumn('po_status');
            //$table->dropColumn('po_tax_rate');
            //$table->dropColumn('po_shipping_cost');
            $table->dropColumn(['po_status', 'po_shipping_cost']);
        });
    }
}
