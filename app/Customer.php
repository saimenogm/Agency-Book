<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    public $table = 'customers';
    protected $fillable = [
        'first_name', 'middle_name', 'last_name','gender'
    ];
}
