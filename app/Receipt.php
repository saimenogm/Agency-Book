<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    //
    protected $fillable=[
        'customer',
        'name',
        'total_amount_usd',
        'total_amount_ern',
        'deposit_ern',
        'deposit_usd',
        'received_by',
        'date',
        'mode',
        'remark',
        'status',
        'check_by',
        'check_num',
        'company',
        'user_id',
        'reference',


    ];
}
