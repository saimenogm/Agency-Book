<?php

namespace App\Http\Controllers;

use App\BankTransfer;
use App\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $transfers=DB::table('bank_accounts')
        // ->join('bank_transfers as ft','ft.from_account','bank_accounts.id')
        // ->join('bank_transfers as tt','tt.to_account','bank_accounts.id')
        // ->get();
        //
        // $transfers=BankTransfer::join('bank_accounts','bank_accounts.id','bank_transfers.from_account')
        // ->join('bank_accounts','bank_accounts.id','bank_transfers.from_account')
        // ->where('bank_transfers.status','active')
        // ->get()->toArray();
        // $transfers=BankAccount::with(["bank_transfers"])->all();
        // $transfers=BankTransfer::all();
        // $transfers = BankTransfer::join('bank_accounts', function($join)
        // {
        //             $join->on('bank_accounts.id', '=', 'bank_transfers.from_account')
        //                  ->orOn('bank_accounts.id', '=', 'bank_transfers.to_account');
        //  })
        //  ->get();
        // dd($transfers);
        $transfers =BankTransfer::join('bank_accounts as u1', 'u1.id', '=', 'bank_transfers.from_account')
            ->join('bank_accounts as u2', 'u2.id', '=', 'bank_transfers.to_account')
            ->select(['bank_transfers.id', 'bank_transfers.transfer_name', 'bank_transfers.amount',  'bank_transfers.currency', 'u1.branch as from_branch', 'u1.account as from_account','u2.branch as branch_to', 'u2.account as account_to'])
            ->get();
        // dd($transfers);
        return view('bank_transfers.index',['transfers'=>$transfers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $accounts=BankAccount::where('status','active')->get();
        return view('bank_transfers.form',['accounts'=>$accounts]);

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
        $from_bank=$request->input('from_bank');
        $to_bank=$request->input('to_bank');
        $amount=$request->input('amount');
        $currency=$request->input('currency');
        // dd($from_bank);
        BankTransfer::create(
            [
                'transfer_name'=>$request->input('transfer_name'),
                'from_account'=>$from_bank,
                'to_account'=>$to_bank,
                'currency'=>$request->input('currency'),
                'amount'=>$request->input('amount'),
                'remark'=>$request->input('remark'),
                'ref_num'=>$request->input('ref_num'),
                'user'=>1
            ]
        );
        $from_bank_=BankAccount::where('id',$from_bank)->first();
        $to_bank_=BankAccount::where('id',$to_bank)->first();
        if ($currency =='USD'){
            BankAccount::where('id',$from_bank)->update([
                'balance_usd'=>$from_bank_->balance_usd - $amount,

            ]);
            BankAccount::where('id',$to_bank)->update([

                'balance_usd'=>$to_bank_->balance_usd + $amount,
            ]);


        }
        else{
            BankAccount::where('id',$from_bank)->update([
                'balance_ern'=>$from_bank_->balance_ern - $amount,

            ]);
            BankAccount::where('id',$to_bank)->update([

                'balance_ern'=>$to_bank_->balance_ern + $amount,
            ]);

        }

        return redirect('/bank_transfers');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BankTransfer  $bankTransfer
     * @return \Illuminate\Http\Response
     */
    public function show(BankTransfer $bankTransfer)
    {
        //
        $bank_transfer =BankTransfer::join('bank_accounts as u1', 'u1.id', '=', 'bank_transfers.from_account')
            ->join('bank_accounts as u2', 'u2.id', '=', 'bank_transfers.to_account')
            ->where('bank_transfers.id',$bankTransfer->id)
            ->select(['bank_transfers.id', 'bank_transfers.transfer_name', 'bank_transfers.amount', 'bank_transfers.ref_num',  'bank_transfers.currency','u1.id as from_id',
                'u1.branch as from_branch','u2.id as to_id','u1.account as from_account','u2.branch as branch_to', 'u2.account as account_to'])
            ->first();
        return view('bank_transfers.index',['bank_transfer'=>$bank_transfer]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BankTransfer  $bankTransfer
     * @return \Illuminate\Http\Response
     */
    public function edit(BankTransfer $bankTransfer)
    {
        //
        $bank_transfer =BankTransfer::join('bank_accounts as u1', 'u1.id', '=', 'bank_transfers.from_account')
            ->join('bank_accounts as u2', 'u2.id', '=', 'bank_transfers.to_account')
            ->where('bank_transfers.id',$bankTransfer->id)
            ->select(['bank_transfers.id', 'bank_transfers.transfer_name', 'bank_transfers.amount', 'bank_transfers.ref_num',  'bank_transfers.currency','bank_transfers.remark',
                'u1.id as from_id','u1.branch as from_branch','u2.id as to_id','u1.account as from_account',
                'u2.branch as branch_to', 'u2.account as account_to'])
            ->first();
        $accounts=BankAccount::where('status','active')->get();
        return view('bank_transfers.detail',['bank_transfer'=>$bank_transfer,'accounts'=>$accounts]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BankTransfer  $bankTransfer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankTransfer $bankTransfer)
    {
        //
        $from_bank=$request->input('from_bank');
        $to_bank=$request->input('to_bank');
        $amount=$request->input('amount');
        $currency=$request->input('currency');
        // dd($from_bank);
        $previour_transfered_amount=BankTransfer::where('id',$bankTransfer->id)->first();
        // to restore the previous transfered amount to the bank
        $from_bank_=BankAccount::where('id',$previour_transfered_amount->from_account)->first();
        $to_bank_=BankAccount::where('id',$previour_transfered_amount->to_account)->first();
        if ($previour_transfered_amount->currency =='USD'){
            BankAccount::where('id',$previour_transfered_amount->from_account)->update([
                'balance_usd'=>$from_bank_->balance_usd + $previour_transfered_amount->amount,

            ]);
            BankAccount::where('id',$previour_transfered_amount->to_account)->update([

                'balance_usd'=>$to_bank_->balance_usd - $previour_transfered_amount->amount,
            ]);


        }
        else if($previour_transfered_amount->currency=='ERN'){

            BankAccount::where('id',$previour_transfered_amount->from_account)->update([
                'balance_ern'=>$from_bank_->balance_ern + $previour_transfered_amount->amount,

            ]);
            BankAccount::where('id',$previour_transfered_amount->to_account)->update([

                'balance_ern'=>$to_bank_->balance_ern - $previour_transfered_amount->amount,
            ]);

        }

        BankTransfer::where('id',$bankTransfer->id)->update(
            [
                'transfer_name'=>$request->input('transfer_name'),
                'from_account'=>$from_bank,
                'to_account'=>$to_bank,
                'currency'=>$request->input('currency'),
                'amount'=>$request->input('amount'),
                'remark'=>$request->input('remark'),
                'ref_num'=>$request->input('ref_num'),
                'user'=>1
            ]
        );
        $from_bank_=BankAccount::where('id',$from_bank)->first();
        $to_bank_=BankAccount::where('id',$to_bank)->first();
        if ($currency =='USD'){
            BankAccount::where('id',$from_bank)->update([
                'balance_usd'=>$from_bank_->balance_usd - $amount,

            ]);
            BankAccount::where('id',$to_bank)->update([

                'balance_usd'=>$to_bank_->balance_usd + $amount,
            ]);


        }
        else{
            BankAccount::where('id',$from_bank)->update([
                'balance_ern'=>$from_bank_->balance_ern - $amount,

            ]);
            BankAccount::where('id',$to_bank)->update([

                'balance_ern'=>$to_bank_->balance_ern + $amount,
            ]);

        }

        return redirect('/bank_transfers');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BankTransfer  $bankTransfer
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankTransfer $bankTransfer)
    {
        //
        // dd($bankTransfer->id);
        $previour_transfered_amount=BankTransfer::where('id',$bankTransfer->id)->first();
        // to restore the previous transfered amount to the bank
        $from_bank_=BankAccount::where('id',$previour_transfered_amount->from_account)->first();
        $to_bank_=BankAccount::where('id',$previour_transfered_amount->to_account)->first();
        if ($previour_transfered_amount->currency =='USD'){
            BankAccount::where('id',$previour_transfered_amount->from_account)->update([
                'balance_usd'=>$from_bank_->balance_usd + $previour_transfered_amount->amount,

            ]);
            BankAccount::where('id',$previour_transfered_amount->to_account)->update([

                'balance_usd'=>$to_bank_->balance_usd - $previour_transfered_amount->amount,
            ]);


        }
        else if($previour_transfered_amount->currency=='ERN'){

            BankAccount::where('id',$previour_transfered_amount->from_account)->update([
                'balance_ern'=>$from_bank_->balance_ern + $previour_transfered_amount->amount,

            ]);
            BankAccount::where('id',$previour_transfered_amount->to_account)->update([

                'balance_ern'=>$to_bank_->balance_ern - $previour_transfered_amount->amount,
            ]);

        }

        BankTransfer::where('id',$bankTransfer->id)->update(
            [
                'status'=>'inactive'

            ]
        );
        return redirect('/bank_transfers');

    }
}
