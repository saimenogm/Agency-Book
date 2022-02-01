<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuestHouseOrder extends Model
{
    //
    protected $fillable=[
        'place',
        'room_no',
        'sale_id',
        'sale_item_id',
        'from_date',
        'to_date',
        'currency',
        'price',
        'gross',
        'prepared_by'
    ];
}

