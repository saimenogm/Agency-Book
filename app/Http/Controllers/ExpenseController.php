<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expense as Expense;
use App\Supplier as Supplier;
use Illuminate\Support\Facades\DB;

use PdfReport;
use PDF;


class ExpenseController extends Controller
{
    //
    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
    }

    public function index()
    {
        $data = [];

        $data['expenses'] =DB::table('expenses')->where('status','<>','inactive')->get();


        return view('expenses/index', $data);
        $data['from_date']='01-02-2019';
        $data['end_date']='30-02-2019';
        //return view('expenses/index_expenses', $data);

    }

    public function change_date_to_standard($date)
    {
        $date = str_replace('/','-',$date);
        $array = explode('-',$date);
        $year=$array[2];
        $month=$array[1];
        $day=$array[0];
        $date=join('-',[$year,$month,$day]);
        $date=date("Y-m-d",strtotime($date));
        return $date;

    }

    public function newExpense(Request $request, Expense $expense)
    {
        $data = [];
        DB::beginTransaction();

        try
        {
            if  ($request->input('with_receipt')=='yes'){
                $data['with_receipt'] = $request->input('with_receipt');
            }
            $data['supplier'] = $request->input('supplier');
            $data['name'] = $request->input('name');
            $data['amount'] = $request->input('amount');
            $data['date'] = $request->input('date');
            $data['remark'] = $request->input('remark');
            $data['due_date'] = $request->input('due_date');
            $data['currency'] = $request->input('currency');
            $data['reference_num'] = $request->input('reference_no');
            $data['status'] = 'unpaid';
            $data['category'] = $request->input('category');
            $data['company'] = $request->input('company');


                $expense->insert($data);

                $supplier=$request->input('supplier');
                $supp=Supplier::where('id',$supplier)->first();
                if ($request->input('currency')=='USD'){
                    Supplier::where('id',$supplier)->update([
                        'balance_usd'=>$supp->balance_usd - $request->input('amount')
                    ]);
                }

                elseif($request->input('currency')=='ERN'){
                    Supplier::where('id',$supplier)->update([
                        'balance_ern'=>$supp->balance_ern - $request->input('amount')
                    ]);
                }

                DB::commit();
                return redirect('expenses/');
        }

            //this is for catch
        catch(Exception $e){
            DB::rollBack();
            return view('expenses/form', $data);



        }





    }

    public function modify( Request $request, $expense_id, Expense $expense )
    {
        $data = [];
        DB::beginTransaction();

        try
        {

            if( $request->isMethod('post') )
            {
                //dd($data);
                $this->validate(
                    $request,
                    [

                    ]
                );


                $expense_data = $this->expense->find($expense_id);
                $expense_data->supplier = $request->input('supplier');
                $expense_data->name = $request->input('name');
                $expense_data->amount= $request->input('amount');
                $expense_data->date= $request->input('date');
                $expense_data->remark = $request->input('remark');
                $expense_data->due_date = $request->input('due_date');
                $expense_data->currency = $request->input('currency');
                $expense_data->reference_num = $request->input('reference_no');
                if  ($request->input('with_receipt')=='yes'){
                    $expense_data->with_receipt= $request->input('with_receipt');
                }
                else{
                    $expense_data->with_receipt= 'no';
                }



                $expense_data->save();
                $supplier=$request->input('supplier');
                $supp=Supplier::where('id',$supplier)->first();
                if ($request->input('currency')=='USD'){
                    Supplier::where('id',$supplier)->update([
                        'balance_usd'=>$supp->balance_usd+$request->input('amount')
                    ]);


                }
                elseif($request->input('currency')=='ERN'){
                    Supplier::where('id',$supplier)->update([
                        'balance_ern'=>$supp->balance_ern + $request->input('amount')
                    ]);


                }

                DB::commit();

                return redirect('expenses');
            }

            return view('expense/detail', $data);


        }
        catch(Exception $e){
            DB::rollBack();
        }
    }

    public function show($expense_id)
    {
        $data = [];
        $data['expense_id'] = $expense_id;
        $data['modify'] = 1;
        $expense_data = $this->expense->find($expense_id);

        $data['supp'] = $expense_data->supplier;
        $data['id'] = $expense_data->id;
        $data['name'] = $expense_data->name;
        $data['currency'] = $expense_data->currency;
        $data['amount'] = $expense_data->amount;
        $data['date'] = $expense_data->date;
        $data['payed_amount'] = $expense_data->payed_amount;
        $data['due_date'] = $expense_data->due_date;
        $data['remark'] = $expense_data->remark;
        $data['reference_num'] = $expense_data->reference_num;
        $data['status'] = $expense_data->status;
        $data['with_receipt'] = $expense_data->with_receipt;
        $data['suppliers'] = Supplier::where('suppliers.active','=','1')->get();
        return view('expenses/detail', $data);
    }

    public function edit($expense_id)
    {

        $data = [];
        $data['expense_id'] = $expense_id;
        $data['modify'] = 1;
        $expense_data = $this->expense->find($expense_id);

        $data['supp'] = $expense_data->supplier;
        $data['name'] = $expense_data->name;
        $data['currency'] = $expense_data->currency;
        $data['amount'] = $expense_data->amount;
        $data['date'] = $expense_data->date;
        $data['due_date'] = $expense_data->due_date;
        $data['remark'] = $expense_data->remark;
        $data['reference_num'] = $expense_data->reference_num;
        $data['status'] = $expense_data->status;
        $data['with_receipt'] = $expense_data->with_receipt;
        $data['suppliers'] = Supplier::where('suppliers.active','=','1')->get();
//        return view('expenses/edit', $data);
//        $supplier=$request->input('supplier');
//        $supp=Supplier::where('id',$supplier)->first();
//        if ($request->input('currency')=='usd'){
//            Supplier::where('id',$supplier)->update([
//                'balance_usd'=>$supp->balance_usd-$request->input('amount')
//            ]);
//
//
//        }
//        elseif($request->input('currency')=='nkf'){
//            Supplier::where('id',$supplier)->update([
//                'balance_ern'=>$supp->balance_ern - $request->input('amount')
//            ]);
//
//
//        }

    }

    public function createExpense(Request $request, Expense $expense)
    {
        $data['suppliers'] = Supplier::where('suppliers.active','=','1')->get();
        $data['categorys'] =DB::table('categorys')->get();
        $data['companys'] = DB::table ('companys')
            ->get ();


        return view('expenses/form',$data);
    }

    public function create()
    {
        $data['suppliers'] = DB::table('suppliers')
            ->where('suppliers.active','=','1')->get();
        $data['categorys'] =DB::table('categorys')->get();

        return view('expenses/create',$data);
    }

    public function reporter(Request $request)
    {
        $data = [];

        $data['users'] = DB::table('users')
            ->get();
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $print = $request->input('print');

        //dd("hiiiliht");


        if(isset($start_date) && isset($end_date) ){

            //   //  dd("hiiili");
            //     $start_date = str_replace('/','-',$start_date);
            // dd($start_date);
            // $array = explode('-',$start_date);
            // $year=$array[2];
            // $month=$array[1];
            // $day=$array[0];
            // $start_date=join('-',[$year,$month,$day]);


            // // $end_date = str_replace('/','-',$end_date);
            // $array = explode('-',$end_date);
            // $year=$array[2];
            // $month=$array[1];
            // $day=$array[0];
            // $end_date=join('-',[$year,$month,$day]);

            //$d1=new date_format($start_date,"d/m/Y");


            // $data['d1']=date("d/m/Y",strtotime($start_date));
            // $data['d2']=date("d/m/Y",strtotime($end_date));
            // dd($end_date);
            $user = $request->input('user');

            if($request->input('user')!=null){
                //dd($request->input('user'));
                $data['expenses'] = DB::table('expenses')
                    ->Where('user',$user)
                    ->whereBetween('date', [$start_date, $end_date])
                    ->get();

            }else{
                $data['expenses'] = DB::table('expenses')
                    ->join('users','users.id','=','expenses.user')
                    ->whereBetween('date', [$start_date, $end_date])->get();
// dd($data);

            }

            //$data['sales'] = $this->sale->all();
            //  dd($data);
            $data['start_date']=$start_date;
            $data['end_date']=$end_date;


            //$data['customers'] = $this->customer->all();

            if(isset($print))
            {

                $start_date = $this->change_date_to_standard( $request->input('start_date')) ;
                $end_date = $this->change_date_to_standard( $request->input('end_date')) ;

                //dd($request->input('start_date'));



                $data['expenses'] = DB::table('expenses')
                    ->join('users','users.id','=','expenses.user')
                    ->whereBetween('date', [$start_date, $end_date])
                    ->get();
                //dd($start_date);

                $pdf = PDF::loadView('reports/expenses_report', $data);
                $pdf->save(storage_path().'_filename.pdf');
                return $pdf->stream('expenses.pdf');

            }else{
                return view('expenses/report_index_expense', $data);
            }

        }

        return view('expenses/report_index_expense', $data);

    }

    public function delete_expense($expense_id){

        $expense=Expense::where('id',$expense_id)->first();
        if($expense->status == 'unpaid'){
            Expense::where('id',$expense_id)->update([
                'status'=>'inactive'
            ]);
        }
        $supp=Supplier::where('id',$expense->supplier)->first();
        if ($expense->currency=='USD'){
            Supplier::where('id',$expense->supplier)->update([
                'balance_usd'=>$supp->balance_usd - $expense->amount
            ]);


        }
        elseif($expense->currency =='ERN'){
            Supplier::where('id',$expense->supplier)->update([
                'balance_ern'=>$supp->balance_ern - $expense->amount
            ]);


        }

        $data['expenses'] =DB::table('expenses')->where('status','<>','inactive')->get();

        return view('expenses/index', $data);

    }

}
