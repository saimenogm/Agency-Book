<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //
    protected $fillable=[
        'item_name',
        'item_category',
        'unit_cost',
        'unit_price',
        'description'
    ];

}
