<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
	protected $table = 'inventoryList';
    public $timestamps = true; //using timestamps
    public $incrementing = false;
    protected $fillable = ['*'];
    protected $primaryKey = 'itemVariationID';
     //chanign the default table
}