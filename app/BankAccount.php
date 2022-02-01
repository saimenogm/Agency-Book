<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    //

    protected $fillable=[
        'account',
        'branch',
        'description',];
    public function bank_transfers(){
        return $this->belongsToMany(BankAccount::class, "bank_accounts", "from_account", "to_account");
    }

}
