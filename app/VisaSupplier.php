<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisaSupplier extends Model
{
    //
    protected $fillable = [
        'supplier_name', 'email', 'status','telephone',
        'address', 'remark', 'balance_usd','balance_ern','mobile','accont_number'
    ];

}
