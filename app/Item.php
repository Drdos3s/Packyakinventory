<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

	protected $table = 'inventoryList';
    public $timestamps = false; //not using timestamps
    public $incrementing = false;
    //protected $fillable = ['squareItemID', 'itemName', 'itemCategoryName', 'itemCategoryID', 'itemVariationName', 'itemVariationID'];
    protected $fillable = ['*'];
    protected $primaryKey = 'itemVariationID';
     //chanign the default table
}