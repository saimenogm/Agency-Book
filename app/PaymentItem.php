<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentItem extends Model
{
    //
    protected $fillable=[
        'expense_id',
        'payment_id',
        'amount',

    ];

}
