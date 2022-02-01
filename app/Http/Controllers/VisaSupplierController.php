<?php

namespace App\Http\Controllers;

use App\VisaSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisaSupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['suppliers'] = DB::table('visa_suppliers')
            ->select('visa_suppliers.*')
            ->where('visa_suppliers.status','=','active')
            ->get();

        //dd($data['suppliers']);
        return view('visa_suppliers/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('visa_suppliers/form');
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



        if($request->input('balance_type_usd')=="unpaid")
        {
            $balance = -1* $request->input('balance_amount_usd');
        }else if($request->input('balance_type_usd')=="overpaid")
        {
            $balance = $request->input('balance_amount_usd');
        }

        $balance_usd = $balance;
        if($request->input('balance_type_ern')=="unpaid")
        {
            $balance = -1* $request->input('balance_amount_ern');
        }else if($request->input('balance_type_ern')=="overpaid")
        {
            $balance =  $request->input('balance_amount_ern');
        }

        $balance_ern= $balance;
        VisaSupplier::create(
            [
                'supplier_name'=>$request->input('supplier_name'),
                'email'=>$request->input('email'),
                'telephone'=>$request->input('telephone'),
                'address'=>$request->input('address'),
                'account_number'=>$request->input('account_number'),
                'remark'=>$request->input('remark'),
                'balance_usd'=>$balance_usd,
                'balance_ern'=> $balance_ern,
                'mobile'=>$request->input('mobile')
            ]
        );
        return redirect('visaSuppliers');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VisaSupplier  $visaSupplier
     * @return \Illuminate\Http\Response
     */
    public function show(VisaSupplier $visaSupplier)
    {
        //
        $supplier_id=$visaSupplier->id;
        $data = [];
        $data['supplier_id'] = $supplier_id;
        $data['modify'] = 1;
        $supplier_data = VisaSupplier::find($supplier_id);
        $data['supplier_name'] = $supplier_data->supplier_name;
        $data['address'] = $supplier_data->address;
        $data['telephone'] = $supplier_data->telephone;
        $data['account_number'] = $supplier_data->account_number;
        $data['mobile'] = $supplier_data->mobile;
        $data['remark'] = $supplier_data->remark;
        $data['email'] = $supplier_data->email;

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
        return view('visa_suppliers/detail', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\VisaSupplier  $visaSupplier
     * @return \Illuminate\Http\Response
     */
    public function edit(VisaSupplier $visaSupplier)
    {
        //
        $supplier_id=$visaSupplier->id;
        $data = [];
        $data['supplier_id'] = $supplier_id;
        $data['modify'] = 1;
        $supplier_data = VisaSupplier::find($supplier_id);
        $data['supplier_name'] = $supplier_data->supplier_name;
        $data['address'] = $supplier_data->address;
        $data['telephone'] = $supplier_data->telephone;
        $data['account_number'] = $supplier_data->account_number;
        $data['mobile'] = $supplier_data->mobile;
        $data['remark'] = $supplier_data->remark;
        $data['email'] = $supplier_data->email;

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
        return view('visa_suppliers/edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VisaSupplier  $visaSupplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VisaSupplier $visaSupplier)
    {
        //
        if($request->input('balance_type_usd')=="unpaid")
        {
            $balance = -1*  $request->input('balance_amount_usd');
        }else if($request->input('balance_type_usd')=="overpaid")
        {
            $balance = $request->input('balance_amount_usd');
        }

        $balance_usd = $balance;
        if($request->input('balance_type_ern')=="unpaid")
        {
            $balance = -1* $request->input('balance_amount_ern');
        }else if($request->input('balance_type_ern')=="overpaid")
        {
            $balance = $request->input('balance_amount_ern');
        }

        $balance_ern = $balance;
        VisaSupplier::where('id',$visaSupplier->id)->update(
            [
                'supplier_name'=>$request->input('supplier_name'),
                'email'=>$request->input('email'),
                'account_number'=>$request->input('account_number'),
                'telephone'=>$request->input('telephone'),
                'address'=>$request->input('address'),
                'remark'=>$request->input('remark'),
                'balance_usd'=>$balance_usd,
                'balance_ern'=> $balance_ern,
                'mobile'=>$request->input('mobile')
            ]
        );
        return redirect('visaSuppliers');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VisaSupplier  $visaSupplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(VisaSupplier $visaSupplier)
    {
        //
        VisaSupplier::where('id',$visaSupplier->id)->update(
            [
                'status'=>'inactive']);
        return redirect('visaSuppliers');

    }
}
