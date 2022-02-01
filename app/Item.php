<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
    protected $fillable=[
        'item_name',
        'item_category',
        'unit_cost',
        'unit_price',
        'status',
        'description'
    ];

}
