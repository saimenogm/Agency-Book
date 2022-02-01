<?php

namespace App\Http\Controllers;

use App\Receipt;
use App\ReceiptItem;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $receipts= DB::table('customers')
            ->join('receipts', 'customers.id', '=', 'receipts.customer')
            ->where('receipts.status','<>','inactive')
            ->select('customers.first_name','customers.middle_name','customers.last_name','receipts.*')
            ->get();
        return view('receipts.index',['receipts'=>$receipts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $customers=Customer::where('status','active')->get();

        $companys= DB::table ('companys')
            ->get ();

        return view('receipts/form',['customers'=>$customers,'companys'=>$companys]);

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
        $customer=$request->input('customer');
        $amount_ern=$request->input('amount_ern');
        $amount_usd=$request->input('amount_usd');
        $mode=$request->input('mode');
        $received_by=$request->input('received_by');
        $date=$request->input('date');
        $ref=$request->input('ref');
        $remark=$request->input('remark');
        $ref=$request->input('ref');
        $paid_amount_usd=$request->input('paied_amount_usd');
        $paid_amount_ern=$request->input('paied_amount_ern');
        $sales_id=$request->input('sales_id');
        $company=$request->input('company');
        //dd($request->input('company'));
        $count_paid=count($request->input('paied_amount_usd'));
        //first check if the mode is check or not if its check then the status will be 'approved' and also it will not be added to sales

        if($request->input('mode')=='Check')
        {
            $check_by= $request->input('check_by');
            $check_num= $request->input('check_num');
            $status='approve';
            $receipt=Receipt::create([
                'name'=>$name,
                'customer'=>$customer,
                'date'=>$date,
                'reference'=>$ref,
                'total_amount_usd'=>$amount_usd,
                'total_amount_ern'=>$amount_ern,
                'received_by'=>$received_by,
                'mode'=>$mode,
                'check_by'=>$check_by,
                'check_num'=>$check_num,
                'status'=>$status,
                'remark'=>$remark,
                'company'=>$company,
                'user_id'=>session ()->get ('user')
            ]);
            for($i=0;$i<$count_paid;$i++)
            {
                if ($paid_amount_ern[$i]==null)
                {
                    if($paid_amount_usd[$i]==null){
                        continue;
                    }
                    else{
                        $ern=0.00;
                        $usd=$paid_amount_usd[$i];

                    }}
                else{
                    if($paid_amount_usd[$i]==null){
                        $ern=$paid_amount_ern[$i];
                        $usd=0.00;
                    }
                    else{
                        $ern=$paid_amount_ern[$i];
                        $usd=$paid_amount_usd[$i];
                    }


                }
                ReceiptItem::create([
                    'receipt_id'=>$receipt->id,
                    'sales_id'=>$sales_id[$i],
                    'amount_ern'=>$ern,
                    'amount_usd'=>$usd,
                    'user_id'=>session ()->get ('user')

                ]);
            }

        }
        //inserts the full information of receipt

        else{

//updates the customer
            $cust=Customer::where('id',$customer)->first();
            $customer_updated=Customer::where('id',$customer)->update(
                [
                    'balance_usd'=>$cust->balance_usd-$amount_usd,
                    'balance_ern'=>$cust->balance_ern-$amount_ern,

                ]
            );

//this code below inserts the receipts list
            $receipt=Receipt::create([
                'name'=>$name,
                'customer'=>$customer,
                'date'=>$date,
                'reference'=>$ref,
                'total_amount_usd'=>$amount_usd,
                'total_amount_ern'=>$amount_ern,
                'received_by'=>$received_by,
                'mode'=>$mode,
                'remark'=>$remark,
                'company'=>$request->input('company'),
                'user_id'=>session ()->get ('user')

            ]);
            for($i=0;$i<$count_paid;$i++)
            {
                if ($paid_amount_ern[$i]==null)
                {
                    if($paid_amount_usd[$i]==null){
                        continue;
                    }
                    else{
                        $ern=0.00;
                        $usd=$paid_amount_usd[$i];

                    }}
                else{
                    if($paid_amount_usd[$i]==null){
                        $ern=$paid_amount_ern[$i];
                        $usd=0.00;
                    }
                    else{
                        $ern=$paid_amount_ern[$i];
                        $usd=$paid_amount_usd[$i];
                    }


                }
                ReceiptItem::create([
                    'receipt_id'=>$receipt->id,
                    'sales_id'=>$sales_id[$i],
                    'amount_ern'=>$ern,
                    'amount_usd'=>$usd,
                    'user_id'=>session ()->get ('user')

                ]);
                $sale= DB::table('sales')->where('id',$sales_id[$i])->first();
                DB::table('sales')->where('id',$sales_id[$i])
                    ->update([
                        'amount_paid_usd'=>$sale->amount_paid_usd+$usd,
                        'amount_paid_ern'=>$sale->amount_paid_ern+$ern,
                    ]);
            }
        }
        $receipts= DB::table('customers')
            ->join('receipts', 'customers.id', '=', 'receipts.customer')
            ->where('receipts.status','<>','inactive')
            ->select('customers.first_name','customers.middle_name','customers.last_name','receipts.*')
            ->get();
        return view('receipts.index',['receipts'=>$receipts]);

                        DB::commit ();

        }catch (\Exception $e){

            DB::rollback();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        }

    /**
     * Display the specified resource.
     *
     * @param  \App\Receipt  $receipt
     * @return \Illuminate\Http\Response
     */
    public function show(Receipt $receipt)
    {
        // //show the detail of the receipt
        // $receipts=Receipt::where('id',$receipt->id)->first();
        $receipts= DB::table('customers')
            ->join('receipts', 'customers.id', '=', 'receipts.customer')
            ->where('receipts.status','<>','inactive')
            ->where('receipts.id',$receipt->id)
            ->select('customers.first_name','customers.middle_name','customers.last_name','receipts.*')
            ->first();
        $receiptlist=DB::table('receipt_items')
            ->join('sales','sales.id','receipt_items.sales_id')
            ->where('receipt_items.receipt_id',$receipt->id)
            ->get();
        return view('receipts.detail',['receipts'=>$receipts,'receiptlist'=>$receiptlist]);



    }
    /**
     * this function below performs approval if the check is approved
     */
    public function approve_receipt(Request $request, $receipt_id)
    {

        DB::beginTransaction ();

        try{


        $receipt=Receipt::where('id',$receipt_id)->first();
        $cust=Customer::where('id',$receipt->customer)->first();
        $customer=Customer::where('id',$receipt->customer)->update(
            [
                'balance_usd'=>$cust->balance_usd-$receipt->total_amount_usd,
                'balance_ern'=>$cust->balance_ern-$receipt->total_amount_ern,

            ]
        );
        $receiptList=ReceiptItem::where('receipt_id',$receipt->id)->get();
        foreach ($receiptList as $list){
            $sale= DB::table('sales')->where('id',$list->sales_id)->first();
            DB::table('sales')->where('id',$list->sales_id)
                ->update([
                    'amount_paid_usd'=>$sale->amount_paid_usd+$list->amount_usd,
                    'amount_paid_ern'=>$sale->amount_paid_ern+$list->amount_ern,
                ]);
        }
        $receipts=Receipt::where('id',$receipt_id)->update([
            'status'=>'active'

        ]);
        $receipts= DB::table('customers')
            ->join('receipts', 'customers.id', '=', 'receipts.customer')
            ->where('receipts.status','<>','inactive')
            ->select('customers.first_name','customers.middle_name','customers.last_name','receipts.*')
            ->get();
        return view('receipts.index',['receipts'=>$receipts]);

            DB::commit ();

        }catch (\Exception $e){

            DB::rollback();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }


    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Receipt  $receipt
     * @return \Illuminate\Http\Response
     */
    public function edit(Receipt $receipt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Receipt  $receipt
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Receipt $receipt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Receipt  $receipt
     * @return \Illuminate\Http\Response
     */
    public function destroy(Receipt $receipt)
    {
        //

        DB::beginTransaction ();

        try{

        $rec=Receipt::where('id',$receipt->id)->first();

        Receipt::where('id',$receipt->id)->update(['status'=>'Void']);

        $customer=Customer::where('id',$receipt->customer)->first();
        $customer=Customer::where('id',$receipt->customer)->update(
            [
                'balance_usd'=>$customer->balance_usd+$rec->total_amount_usd,
                'balance_ern'=>$customer->balance_ern+$rec->total_amount_ern,

            ]
        );
        $receiptList=ReceiptItem::where('receipt_id',$receipt->id)->get();
        foreach ($receiptList as $list){
            $sale= DB::table('sales')->where('id',$list->sales_id)->first();
            DB::table('sales')->where('id',$list->sales_id)
                ->update([
                    'amount_paid_usd'=>$sale->amount_paid_usd-$list->amount_usd,
                    'amount_paid_ern'=>$sale->amount_paid_ern-$list->amount_ern,
                ]);
        }
        $receipts= DB::table('customers')
            ->join('receipts', 'customers.id', '=', 'receipts.customer')
            ->where('receipts.status','<>','inactive')
            ->select('customers.first_name','customers.middle_name','customers.last_name','receipts.*')
            ->get();
        return view('receipts.index',['receipts'=>$receipts]);

                        DB::commit ();

        }catch (\Exception $e){

            DB::rollback();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        }

    public function createReceiptSales2(Request $request)
    {
        $data = [];

        $input = $request->all();
        $output='';


        try{
            $sales = DB::table('sales')
                ->where('status', 'unpaid')
                ->where('customer',$request->customer_id)
                ->get();
            $c=1;
            if($sales)
            {
                foreach ($sales as $sale) {
                    $usd=$sale->total_amount_usd- $sale->amount_paid_usd;
                    $ern=$sale->total_amount_ern- $sale->amount_paid_ern;
                    if ($usd==0 && $ern==0){
                        continue;
                    }
                    $output.='<tr>'.
                             '<td>'.$sale->id.'<input type="hidden" name="sales_id[]" value="'.$sale->id.'"></td>'.
                             '<td>'.$sale->ref.'</td>'.
                             '<td>'.$sale->date.'</td>'.
                             '<td>'.$usd.'</td>'.
                             '<td>'.$ern.'</td>'.
                             "<td><input name='paied_amount_usd[]' type='number' onchange='receipt_usd(this)' max='".$usd."'><input value='0.00' disabled></td> ".
                             "<td><input name='paied_amount_ern[]' type='number' onchange='receipt_ern(this)' max='".$ern."'><input value='0.00' disabled></td> ".
                             '<td><input type="checkbox" onchange="test(this)" value="'.$c.'" name="paid[]"></td>'.
                             '</tr>';
                    $c=$c+1;
                }

            }
            return response()->json(['failure'=>'success','data'=>$output]);

        }catch(\Exception $e){

//    DB::rollBack();
//    throw $e;
            return response()->json(['failure'=>'success','item'=>'error'.$e]);
//$output = $e;
        }
        return response()->json(['failure'=>'success','data'=>$output]);
//   }
//   return Response($request->customer_id);
//  echo "sdkfjsdlkjf";
    }     //return view('Receipts/form', $data);

}

