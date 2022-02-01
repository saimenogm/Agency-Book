<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
    protected $fillable=[
        'name',
        'supplier',
        'company',
        'payment_account',
        'payment_from',
        'amount_ern',
        'amount_usd',
        'date',
        'mode',
        'remark'
    ];

}
