<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Location;
Use Auth;

class userController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()){//The user is logged in
            //Show all current managers associated to owner

            $managers = User::where('account_owner', Auth::user() -> id)
               ->where('user_role', 2)
               ->orderBy('name', 'desc')
               ->get();

            $locations = Location::all(); //<---- Need to change this through application to only get locations vendors items and others based on user id

            $user = Auth::user();
            
            return view('userProfile', ['user' => $user, 'managers' => $managers, 'locations' => $locations]);
        }else{
            return redirect('/auth/login');
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //This is taken care of within the blade template
    }

    /**
     * Store a newly created resource in storage.
     * This is where the new manager will be created
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|max:255|confirmed',
            'password_confirmation' => 'required| max:255',
        ]);

        $manager = new User;

        $manager -> name = $request -> name;
        $manager -> email = $request -> email;
        $manager -> password = bcrypt($request -> password);
        $manager -> user_role = 2;  
        $manager -> account_owner = Auth::user() -> id;
        $manager -> manager_location = $request -> location;

        $manager -> save();

        return $manager;
    }

    /**
     * Display the specified resource.
     *Show details about specific manager
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *Edit details such as email for managers
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *Delete and remove permission from that user
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deletedRows = User::where('id', $id)->delete();
        return $deletedRows;
    }
}
