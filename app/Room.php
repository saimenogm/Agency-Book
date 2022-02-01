<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    //
    protected $fillable=[
        'room_name',
        'room_category',
        'floor',
        'place',
        'status',
        'unit_price_month',
        'unit_price_day',
        'description'
    ];

}
