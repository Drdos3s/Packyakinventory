<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'inventoryList'; //chanign the default table
    public $timestamps = false; //not using timestamps
    $primaryKey = 'itemVariationID';
    $incrementing = false;
}
