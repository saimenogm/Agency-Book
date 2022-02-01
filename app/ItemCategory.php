<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    //
    protected $fillable=[
        'category_name',
        'category_code',
        'category_description',
        'status'
    ];

}
