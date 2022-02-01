<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    //
    protected $fillable=[
        'name',
        'deposited_to',
        'account_num',
        'total_amount_ern',
         'total_amount_usd',
         'deposited_by',
         'date',
          'mode',
          'description',
        'company'
        ];
}
