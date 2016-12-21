<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function ($table) {
            $table->string('user_id');
            $table->string('email');
            $table->string('phone_number');
            $table->string('contact_name');
            $table->string('company_name');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->integer('zip');
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
            $table->dropColumn(['user_id',
                                'email',
                                'phone_number',
                                'contact_name',
                                'company_name',
                                'address',
                                'city',
                                'state',
                                'zip']);
        });
    }
}
