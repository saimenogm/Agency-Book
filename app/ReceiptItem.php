<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReceiptItem extends Model
{
    //
    protected $fillable=[
        'receipt_id',
        'sales_id',
        'amount_ern',
        'amount_usd',
        'status'

    ];

}
