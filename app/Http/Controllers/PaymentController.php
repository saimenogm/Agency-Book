<?php

namespace App\Http\Controllers;

use App\PaymentItem;
use App\Payment;
use App\Supplier;
use App\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $payments=DB::table('suppliers')
            ->join('payments','suppliers.id','payments.supplier')
            ->where('status','active')
            ->select('payments.id','payments.name','suppliers.supplier_name','payments.amount_usd','payments.amount_ern')
            ->get();
        return view('payments.index',['payments'=>$payments]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()

    {

        //
        $accounts=BankAccount::all();
        $data['suppliers'] = Supplier::where('active',1)->get();
        return view('payments/form',['accounts'=>$accounts])->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name=$request->input('name');
        $supplier=$request->input('supplier');
        $amount_ern=$request->input('amount_ern');
        $amount_usd=$request->input('amount_usd');
        $mode=$request->input('mode');
        $deposit_by=$request->input('deposit_by');
        $date=$request->input('date');
        $remark=$request->input('remark');
        $paid_from=$request->input('paid_from');
        $temp=explode('=>',$paid_from);
        if(count($temp)==2){
            $account_num=$temp[0];
            $paid_from=$temp[1];

            //updating the bank balance if the payment is made from one of the banks
            $account= BankAccount::where('account',$account_num)->where('branch',$paid_from)->first();
            BankAccount::where('account',$account_num)
                ->where('branch',$paid_from)->update(
                    [
                        'balance_usd'=>$account->balance_usd-$amount_usd,
                        'balance_ern'=>$account->balance_ern-$amount_ern
                    ]
                );
        }
        else{
            $account_num=null;

        }
        //updting the suppliers balance

        $supp=Supplier::where('id',$supplier)->first();
        if (($supp->balance_ern <= 0) and ($supp->balance_usd <= 0))
        {
        Supplier::where('id',$supplier)->update([
            'balance_usd'=>$supp->balance_usd - $amount_usd,
            'balance_ern'=>$supp->balance_ern - $amount_ern,
        ]);
             }
        elseif (($supp->balance_ern >= 0) and ($supp->balance_usd >= 0))
        {
            Supplier::where('id',$supplier)->update([
                'balance_usd'=>$supp->balance_usd - $amount_usd,
                'balance_ern'=>$supp->balance_ern - $amount_ern,
            ]);
        }

           elseif (($supp->balance_ern > 0)){
        Supplier::where('id',$supplier)->update([
            'balance_ern'=>$supp->balance_ern-$amount_ern,
            'balance_ern'=>$supp->balance_usd-$amount_usd,
        ]      );
              }
        elseif (($supp->balance_ern < 0)){
            Supplier::where('id',$supplier)->update([
                'balance_ern'=>$supp->balance_usd-$amount_usd,
                'balance_ern'=>$supp->balance_ern-$amount_ern,
            ]      );
        }



        $payment=Payment::create([
            'supplier'=>$supplier,
            'name'=>$name,
            'date'=>$date,
            'payment_from'=>$paid_from,
            'payment_account'=>$account_num,
            'amount_ern'=>$amount_ern,
            'amount_usd'=>$amount_usd,
            'mode'=>$mode,
            'remark'=>$remark,
            'company'=>$request->session()->get('company')

        ]);
        //insert the expense information to the payment list
        $paid=$request->input('paid');
        $expense=$request->input('expense');
        $paid_amount=$request->input('paid_amount');
        $paid_count=count($paid_amount);
        for ($i=0;$i<$paid_count;$i++){
            // i reduced i coz the list [] starts from 0 but count counts from 1

            if($paid_amount[$i]==null){
                continue;
            }
            else{
                $amount=$paid_amount[$i];
            }

            PaymentItem::create([
                'expense_id'=>$expense[$i],
                'amount'=>$amount,
                'payment_id'=>$payment->id
            ]);

            $exp=DB::table('expenses')->where('id',$expense[$i])->first();
            DB::table('expenses')->where('id',$expense[$i])
                ->update(
                    [
                        'payed_amount'=>$exp->payed_amount+$amount,
                        'status'=>'partially paid'
                    ]
                );
            //update the expense
            $exp=DB::table('expenses')->where('id',$expense[$i])->first();
            if($exp->amount == $exp->payed_amount)
            {
                DB::table('expenses')->where('id',$expense[$i])
                    ->update(
                        [
                            'status'=>'payed',
                        ]
                    );
            }
        }
        $payments=DB::table('suppliers')
            ->join('payments','suppliers.id','payments.supplier')
            ->where('status','active')
            ->select('payments.id','payments.name','suppliers.supplier_name','payments.amount_usd','payments.amount_ern')
            ->get();
        return view('payments.index',['payments'=>$payments]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PaymentItem  $paymentItem
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        //
        // $payments=Payment::where('id',$payment->id)->first();
        $payments=DB::table('suppliers')
            ->join('payments','suppliers.id','payments.supplier')
            ->where('status','active')
            ->where('payments.id',$payment->id)
            ->first();
        $paymentlist=PaymentItem::where('payment_id',$payment->id)->get();
        return view('payments.detail',['payments'=>$payments,'paymentlist'=>$paymentlist]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PaymentItem  $paymentItem
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentItem $paymentItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PaymentItem  $paymentItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentItem $paymentItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentItem  $paymentItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {

        //
        $pay=Payment::where('id',$payment->id)->first();

        Payment::where('id',$payment->id)->update(['status'=>'inactive']);
        if ($pay->payment_account == ''){}
        else{
        $account= BankAccount::where('account',$pay->payment_account)->where('branch',$pay->payment_from)->first();


        BankAccount::where('account',$pay->payment_account)
            ->where('branch',$pay->payment_from)->update(
                [
                    'balance_usd'=>$account->balance_usd-$pay->amount_usd,
                    'balance_ern'=>$account->balance_ern-$pay->amount_ern
                ]
            );

        }
        $paymentlist=PaymentItem::where('payment_id',$payment->id)->get();
        foreach($paymentlist as $list){

            $expense=DB::table('expenses')->where('id',$list->expense_id)->first();
            //first check the money inorder to also update the status of the expense

            $remaining=$expense->payed_amount-$list->amount;
            if ($remaining==0){
                $payment_status='payed';
            }
            elseif($remaining < $expense->amount){
                $payment_status='partially paid';
            }
            else{
                $payment_status='overpaid';
            }
            DB::table('expenses')
                ->where('id',$list->expense_id)
                ->update(
                    [
                        'payed_amount'=>$expense->amount-$list->amount,
                        'status'=>$payment_status
                    ]);

        }
//        $supp=Supplier::where('id',$payment->supplier)->first();
//        Supplier::where('id',$payment->supplier)->update([
//            'balance_usd'=>$supp->balance_usd+$pay->amount_usd,
//            'balance_ern'=>$supp->balance_ern+$pay->amount_ern,
//        ]);


        $supp=Supplier::where('id',$payment->supplier)->first();
        if (($supp->balance_ern <= 0) and ($supp->balance_usd <= 0))
        {
//            dd('in');
            Supplier::where('id',$payment->supplier)->update([
                'balance_usd'=>$supp->balance_usd+$pay->amount_usd,
                'balance_ern'=>$supp->balance_ern+$pay->amount_ern,
            ]);
        }
        elseif (($supp->balance_ern > 0) and ($supp->balance_usd > 0))
        {
            Supplier::where('id',$payment->supplier)->update([
                'balance_usd'=>$supp->balance_usd+$pay->amount_usd,
                'balance_ern'=>$supp->balance_ern+$pay->amount_ern,
            ]);
        }

        elseif (($supp->balance_ern > 0)){
            Supplier::where('id',$payment->supplier)->update([
                'balance_ern'=>$supp->balance_ern+$pay->amount_ern,
                'balance_ern'=>$supp->balance_usd+$pay->amount_ern,
            ]      );
        }
        elseif (($supp->balance_ern < 0)){
            Supplier::where('id',$payment->supplier)->update([
                'balance_ern'=>$supp->balance_usd+$pay->amount_usd,
                'balance_ern'=>$supp->balance_ern+$pay->amount_ern,
            ]      );
        }

        $payments=DB::table('payments')
            ->join('suppliers','suppliers.id','payments.supplier')
            ->where('status','active')
            ->select('payments.id','payments.name','suppliers.supplier_name','payments.amount_usd','payments.amount_ern')
            ->get();
        return view('payments.index',['payments'=>$payments]);
    }
    public function payment_ajax(Request $request)
    {
        $input = $request->all();
        try{

            $id=$request->id;


            $payments = DB::table('suppliers')
                ->join('expenses','suppliers.id','=','expenses.supplier')
                ->where('suppliers.id', '=', $id)
                ->where('expenses.status','<>','paid')
                ->get();

            $return_data = "";
            $c=1;


            foreach($payments as $payment){
                $to_be_paid=$payment->amount-$payment->payed_amount;
                $return_data .= "<tr><td>".$c."<input type='hidden' name='q_num[]' value='".$c."'><input type='hidden' name='expense[]' value='".$payment->id."'></td>";
                $return_data .= "<td>".$payment->supplier_name."</td>";
                $return_data .= "<td>".$payment->account_number."</td>";
                $return_data .= "<td>".$payment->date."</td>";
                $return_data .= "<td>".$payment->currency."<input type='hidden' value='".$payment->currency."'></td>";
                $return_data .= "<td>".$payment->amount."</td>";
                $return_data .= "<td>".$to_be_paid."</td>";
                $return_data .= "<td><input name='paid_amount[]' type='number' onchange='deposit(this)' max='".$to_be_paid."'><input onchange='convertor(this)' value='0.00' disabled> </td>";
                $return_data .= "<td><input type='checkbox' name='paid[]' value='".$c."' onchange='enabler(this)'></td></tr>";
                $c=$c+1;
            }

            return response()->json(['bills'=>$return_data]);


        }


        catch(\Exception $e){

            DB::rollBack();

            return response()->json(['bills'=>$e,'item'=>'error'.$e]);
        }
        return response()->json(['bills'=>'generic','item'=>'item_success']);


    }
}
