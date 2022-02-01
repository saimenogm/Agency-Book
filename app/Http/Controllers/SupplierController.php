<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplier as Supplier;
use Illuminate\Support\Facades\DB;


class SupplierController extends Controller
{
    //

    public function __construct(Supplier $supplier )
    {
        $this->supplier = $supplier;
    }

    public function index()
    {
        $data = [];

        $data['suppliers'] = DB::table('suppliers')
            ->select('suppliers.*')
            ->where('suppliers.active','=','1')
            ->get();

        //dd($data['suppliers']);
        return view('suppliers/index', $data);
    }

    public function newSupplier(Request $request, Supplier $supplier)
    {
        $data = [];
        $data['supplier_name'] = $request->input('supplier_name');
        $data['telephone'] = $request->input('telephone');
        $data['address'] = $request->input('address');
        $data['account_number'] = $request->input('account_number');
        $data['mobile'] = $request->input('mobile');
        $data['remark'] = $request->input('remark');
        //$data['last_name'] = $request->input('last_name');


        if($request->input('balance_type_usd')=="unpaid")
        {
            $balance = -1* $request->input('balance_amount_usd');
        }else if($request->input('balance_type_usd')=="overpaid")
        {
            $balance = $request->input('balance_amount_usd');
        }

        $data['balance_usd'] = $balance;
        if($request->input('balance_type_ern')=="unpaid")
        {
            $balance = -1* $request->input('balance_amount_ern');
        }else if($request->input('balance_type_ern')=="overpaid")
        {
            $balance =  $request->input('balance_amount_ern');
        }

        $data['balance_ern'] = $balance;

        if( $request->isMethod('post') )
        {
            //dd($data);

            $this->validate(
                $request,
                [

                ]
            );


            $supplier->insert($data);

            return redirect('suppliers');
        }

        return view('suppliers/form', $data);

    }


    public function modify( Request $request, $supplier_id, Supplier $supplier )
    {
        $data = [];

        //dd('vvvvv'.$supplier_id);

        $data['supplier_name'] = $request->input('supplier_name');
        $data['telephone'] = $request->input('telephone');
        $data['address'] = $request->input('address');
        $data['account_number'] = $request->input('account_number');
        $data['mobile'] = $request->input('mobile');
        $data['remark'] = $request->input('remark');



        if( $request->isMethod('post') )
        {
            //dd($data);
            $this->validate(
                $request,
                [

                ]
            );



            $supplier_data = $this->supplier->find($supplier_id);

            $supplier_data->supplier_name = $request->input('supplier_name');
            $supplier_data->address = $request->input('address');
            $supplier_data->account_number = $request->input('account_number');
            $supplier_data->telephone = $request->input('telephone');
            $supplier_data->mobile = $request->input('mobile');
            $supplier_data->remark = $request->input('remark');


            //merhawi's code

            if($request->input('balance_type_usd')=="unpaid")
            {
                $balance = -1*  $request->input('balance_amount_usd');
            }else if($request->input('balance_type_usd')=="overpaid")
            {
                $balance = $request->input('balance_amount_usd');
            }

            $supplier_data->balance_usd = $balance;
            if($request->input('balance_type_ern')=="unpaid")
            {
                $balance = -1* $request->input('balance_amount_ern');
            }else if($request->input('balance_type_ern')=="overpaid")
            {
                $balance = $request->input('balance_amount_ern');
            }

            $supplier_data->balance_ern = $balance;


            $supplier_data->save();

            return redirect('suppliers');
        }

        return view('supplier/detail', $data);
    }

    public function show($supplier_id)
    {
        $data = []; $data['supplier_id'] = $supplier_id;
        $data['modify'] = 1;
        $supplier_data = $this->supplier->find($supplier_id);
        $data['supplier_name'] = $supplier_data->supplier_name;
        $data['address'] = $supplier_data->address;
        $data['telephone'] = $supplier_data->telephone;
        $data['account_number'] = $supplier_data->account_number;
        $data['mobile'] = $supplier_data->mobile;
        $data['remark'] = $supplier_data->remark;

        if($supplier_data->balance_ern<0)
        {
            $data['balance_amount_ern'] =  abs($supplier_data->balance_ern);
            $data['balance_type_ern'] = 'unpaid';
        }else if($supplier_data->balance_ern>=0)
        {
            $data['balance_amount_ern'] =  abs($supplier_data->balance_ern);
            $data['balance_type_ern'] = 'overpaid';
        }
        if($supplier_data->balance_usd<0)
        {
            $data['balance_amount_usd'] =  abs($supplier_data->balance_usd);
            $data['balance_type_usd'] = 'unpaid';
        }else if($supplier_data->balance_usd>=0)
        {
            $data['balance_amount_usd'] =  abs($supplier_data->balance_usd);
            $data['balance_type_usd'] = 'overpaid';
        }
        return view('suppliers/detail', $data);
    }

    public function createSupplier(Request $request, Supplier $supplier)
    {

        return view('suppliers/form');

    }


    public function create()
    {
        return view('suppliers/create');
    }
    public function delete_supplier(Request $request,$supplier_id)
    {
        $supplier = $this->supplier->find($supplier_id);
        $supplier->active=0;
        $supplier->save();
        $data = [];

        $data['suppliers'] = DB::table('suppliers')
            ->where('suppliers.active','=','1')
            ->select('suppliers.*')
            ->get();

        return view('suppliers/index', $data);
    }

}
