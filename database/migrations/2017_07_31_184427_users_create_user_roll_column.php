<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersCreateUserRollColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Add user role column to users table in DB
        Schema::table('users', function (Blueprint $table) {
            $table->integer('user_role'); //integer value for manager or owner
            $table->integer('account_owner')->nullable()->default(null); //Integer value for user ID that acocunt is owned by
            $table->string('manager_location')->nullable()->default(null); //Location that manager is created for
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //remove columns made in up function
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_role');
            $table->dropColumn('account_owner');
            $table->dropColumn('manager_location');
        });
    }
}
