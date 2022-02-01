<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomCategory extends Model
{
    //
    protected $fillable=[
        'category_name',
        'description',
        'unit_price',
        'description'
    ];

}
