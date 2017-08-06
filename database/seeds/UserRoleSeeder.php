<?php

use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    	//$userRole = new UserRole;


    	$roles = [
    		['role_name' => 'owner'],
        	['role_name' => 'manager']
        ];

        DB::table('userRoles')->insert($roles);
    }
}
