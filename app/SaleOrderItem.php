<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleOrderItem extends Model
{
    //
    public function sale()
    {
        return $this->belongsTo('App\SaleOrder','sale_id','id');
    }

}
