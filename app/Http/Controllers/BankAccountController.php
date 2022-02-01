<?php

namespace App\Http\Controllers;

use App\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $accounts=BankAccount::where('status','active')->get();
        return view('bank_accounts.index',['accounts'=>$accounts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('bank_accounts.form');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        BankAccount::create(
            [
                'account'=>$request->input('account_name'),
                'branch'=>$request->input('branch'),
                'balance_ern'=>$request->input('balance_amount_ern'),
                'balance_usd'=>$request->input('balance_amount_usd'),
                'description'=>$request->input('description')
            ]
        );
        $accounts=BankAccount::where('status','active')->get();
        return view('bank_accounts.index',['accounts'=>$accounts]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccount $bankAccount)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(BankAccount $bankAccount)
    {
        //

        $bank_account=BankAccount::find($bankAccount->id);
        return view('bank_accounts.detail',['bank_account'=>$bank_account]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        //
        BankAccount::where('id',$bankAccount->id)->update(
            [
                'account'=>$request->input('account_name'),
                'branch'=>$request->input('branch'),
                'balance_ern'=>$request->input('balance_amount_ern'),
                'balance_usd'=>$request->input('balance_amount_usd'),
                'description'=>$request->input('description')
            ]
        );
        $accounts=BankAccount::where('status','active')->get();
        return view('bank_accounts.index',['accounts'=>$accounts]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccount $bankAccount)
    {
        //
        BankAccount::where('id',$bankAccount->id)->update(['status'=>'inactive']);
        $accounts=BankAccount::where('status','active')->get();
        return view('bank_accounts.index',['accounts'=>$accounts]);
    }
}
