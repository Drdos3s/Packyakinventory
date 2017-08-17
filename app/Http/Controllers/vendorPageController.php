<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use Mail;
Use App\Vendor;


class vendorPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()){//The user is logged in
            //get all of the availabe resources for vendors
            $vendors = Vendor::all();

            //return view with all vendors info
            return view('vendors', ['vendors' => $vendors]);
        }else{
            return redirect('/auth/register');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        //FORM IS SHOWED IN VENDORS BLADE FILE
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'companyName' => 'required|max:255',
            'contact_name' => 'required|max:255',
            'phone_number' => 'required|max:10',
            'extension' => 'max:5',
            'email' => 'required|max:255|email',
            'address' => 'max:255',
            'city' => 'max:255',
            'state' => 'max:255',
            'zip' => 'integer'
        ]);

        //Initiate new model
        $vendor = new Vendor;

        //Set vendor model values
        $vendor -> user_id = Auth::user() -> id;
        $vendor -> email = $request -> email;
        $vendor -> phone_number = $request -> phone_number;
        $vendor -> phone_extension = $request -> extension;
        //need to add in extension migration for vendor phone number
        $vendor -> contact_name = $request -> contact_name;
        $vendor -> company_name = $request -> companyName;
        $vendor -> address = $request -> address;
        $vendor -> city = $request -> city;
        $vendor -> state = $request -> state;
        $vendor -> zip = $request -> zip;

        //save the model to the database 
        $vendor -> save(); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        $vendor = Vendor::where('id', $id)->first();

        //if there is no vendor that was found then 404
        if(is_null($vendor)){
            abort(404);
        };

        //This should be the variable to retrieve all the items associated with that vendor and display them in a nice way almost like a categlog. 
        $items = null;
        //Get all items associated with that vendor
        //Show all information for that vendor
        //This is where button to delete and edit are held as well as other info for the future
        return view('singleVendor', ['info' => $vendor, 'items' => $items]);
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
     *
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deletedRows = Vendor::where('id', $id)->delete();
        return $deletedRows;
    }
}
