<?php

namespace App\Http\Controllers;

use App\Deposit;
use App\DepositList;
use App\BankAccount;
use App\Receipt as Receipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $deposits=Deposit::where('status','active')->get();
        return view('deposits.index',['deposits'=>$deposits]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $data['receipts'] = DB::table('customers')
            ->join('receipts', 'customers.id', '=', 'receipts.customer')
            ->get();
        $data['companys'] = DB::table ('companys')->get ();

        $accounts=BankAccount::all();
        return view('deposits.create',['accounts'=>$accounts])->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        DB::beginTransaction ();

        try{

            $name=$request->input('name');
            $deposited_to=$request->input('deposited_to');
            $amount_ern=$request->input('amount_ern');
            $amount_usd=$request->input('amount_usd');
            $mode=$request->input('mode');
            $deposit_by=$request->input('deposit_by');
            $date=$request->input('date');
            $description=$request->input('description');
            $company=$request->input('company');

            //if the deposit is bank we must specify both the bank and the account using the code below
            $temp=explode('=>',$deposited_to);
            if(count($temp)==2){
                $account_num=$temp[0];
                $deposited_to=$temp[1];
                $account= BankAccount::where('account',$account_num)->where('branch',$deposited_to)->first();
                BankAccount::where('account',$account_num)
                    ->where('branch',$deposited_to)->update(
                        [
                            'balance_usd'=>$account->balance_usd+$amount_usd,
                            'balance_ern'=>$account->balance_ern+$amount_ern
                        ]
                    );
            }
            else{
                $account_num=null;

            }


            $deposit_id= Deposit::create(
                [
                    'name'=>$name,
                    'deposited_to'=>$deposited_to,
                    'account_num'=>$account_num,
                    'total_amount_ern'=>$amount_ern,
                    'total_amount_usd'=>$amount_usd,
                    'deposited_by'=>$deposit_by,
                    'date'=>$date,
                    'mode'=>$mode,
                    'description'=>$description,
                    'company'=>$company
                ]
            );

            //deposit list


            //update the bank account balance

            $deposited=$request->input('deposited');
            $receipt=$request->input('receipt');
            $deposited_usd=$request->input('deposited_usd');
            $deposited_ern=$request->input('deposited_ern');
            $deposited_count=count($deposited_usd);

            for ($i=0;$i<$deposited_count;$i++){
                // i reduced i coz the list [] starts from 0 but count counts from 1


                //first check if both are not changed if that is the condition then continue without inserting new deposite item
                if($deposited_usd[$i]==0.00){
                    if($deposited_ern[$i]==0.00){
                        continue;

                    }

                }
                $deposit_list=DepositList::create([
                    'deposit_id'=>$deposit_id->id,
                    'amount_ern'=>$deposited_ern[$i],
                    'amount_usd'=>$deposited_usd[$i],
                    'receipt_id'=>$receipt[$i],
                    'company'=>$company

                ]);
                $reciept=DB::table('receipts')->where('id',$receipt[$i])->first();

                DB::table('receipts')
                    ->where('id',$receipt[$i])
                    ->update(
                        [
                            'deposited_ern'=>$deposited_ern[$i]+$reciept->deposited_ern,
                            'deposited_usd'=>$deposited_usd[$i]+$reciept->deposited_usd,
                        ]);

            }
            $deposits=Deposit::where('status','active')->get();
            return view('deposits.index',['deposits'=>$deposits]);

            DB::commit ();

        }catch (\Exception $e){

            DB::rollback();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function show(Deposit $deposit)
    {
        //

        $deposit=Deposit::where('id',$deposit->id)->first();
        //$depositlist=DepositList::where('deposit_id',$deposit->id)->get();
        $depositlist = DB::table ('deposit_lists')
            ->join ('receipts','receipts.id','=','deposit_lists.receipt_id')
            ->where ('deposit_lists.deposit_id','=',$deposit->id)
            ->select('receipts.*','deposit_lists.*')
            ->get();
        return view('deposits.show',['deposit'=>$deposit,'depositlist'=>$depositlist]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function edit(Deposit $deposit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Deposit $deposit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deposit $deposit)
    {
        //

        DB::beginTransaction ();

//        try{
//
//            DB::commit ();
//
//        }catch (\Exception $e){
//
//            DB::rollback();
//            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
//        }


        try{


        $depos=Deposit::where('id',$deposit->id)->first();

        Deposit::where('id',$deposit->id)->update(['status'=>'inactive']);
        $account= BankAccount::where('account',$depos->account_num)->where('branch',$depos->deposited_to)->first();
        BankAccount::where('account',$depos->account_num)
            ->where('branch',$depos->deposited_to)->update(
                [
                    'balance_usd'=>$account->balance_usd-$depos->total_amount_usd,
                    'balance_ern'=>$account->balance_ern-$depos->total_amount_ern
                ]
            );


        $depositlist=DepositList::where('deposit_id',$deposit->id)->get();
        foreach($depositlist as $list){

            $reciept=DB::table('receipts')->where('id',$list->receipt_id)->first();

            DB::table('receipts')
                ->where('id',$list->receipt_id)
                ->update(
                    [
                        'deposited_ern'=>$reciept->deposited_ern-$list->amount_ern,
                        'deposited_usd'=>$reciept->deposited_usd-$list->amount_usd,
                    ]);

        }

        $deposits=Deposit::where('status','active')->get();
        return view('deposits.index',['deposits'=>$deposits]);

            DB::commit ();

        }catch (\Exception $e){

            DB::rollback();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

    }
}
