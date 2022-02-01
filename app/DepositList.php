<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepositList extends Model
{
    //
    protected $fillable=[
    'deposit_id',
    'amount_ern',
     'amount_usd',
     'receipt_id',
     'company'
    ];
}
