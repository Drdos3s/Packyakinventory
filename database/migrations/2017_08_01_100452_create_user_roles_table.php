<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //create the user roles index to determine what user can do or see
        Schema::create('userRoles', function (Blueprint $table) {
            $table->increments('role_id');
            $table->string('role_name', 50); 
        });

        // Insert user roles stuff by calling the seeder
        Artisan::call( 'db:seed', [
            '--class' => 'UserRoleSeeder',
            '--force' => true ]
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('userRoles');
    }
}
