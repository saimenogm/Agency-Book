<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankTransfer extends Model
{
    //
    protected $fillable=[
        'transfer_name',
        'from_account',
        'to_account',
        'currency',
        'amount',
        'remark',
        'user',
        'status',
        'ref_num'];

}
