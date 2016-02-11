<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use Auth;

class locationsPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        if (Auth::check()){//The user is logged in
            $locationQuery = DB::select('select * from locations');
            $locations['places'] = json_decode(json_encode($locationQuery),true);
            return view('locations', $locations);
        }else{
            return redirect('/auth/register');
        }




    }

}
