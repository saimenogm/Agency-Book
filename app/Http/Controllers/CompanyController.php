<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    //
    public function index()
    {
        $data = [];

        $data['companys'] =DB::table('companys')->get();
        //return view('customers/index_customers', $data);
        return view('companys/index',$data);

    }

    public function newCompany(Request $request)
    {
        $data = [];
        $data['company_name'] = $request->input('company_name');
        $data['company_code'] = $request->input('company_code');
        $data['description'] = $request->input('description');
        if( $request->isMethod('post') )
        {
            //dd($data);

            /** @var TYPE_NAME $this */
            $this->validate(
                $request,
                [
                ]
            );
            

            DB::table('companys')->insert($data);

            return redirect('companys/');
        }
        
        return view('companys/form', $data);

    }

    public function show($company_id)
    {
        $data = []; 
       $data['company_id']=$company_id;
        $customer_data = DB::table('companys')
        ->where('id',$company_id)
        ->first();
        
        $data['modify'] = 1;

        $data['company_name'] = $customer_data->company_name;
        $data['company_code'] = $customer_data->company_code;
        $data['description'] = $customer_data->description;


        return view('companys/detail', $data);
    }

    public function modify(Request $request, $company_id )
    {
        $data = [];

        $data['company_name'] = $request->input('company_name');
        $data['company_code'] = $request->input('company_code');
        $data['description'] = $request->input('description');
        if( $request->isMethod('post') )
        {
            //dd($data);
            $this->validate(
                $request,
                [
                    
                ]
            );
            

            DB::table('companys')
            ->where('id',$company_id)
            ->update(['company_name'=>$request->input('company_name'),'company_code'=>$request->input('company_code'),'description'=>$request->input('description')]);
            return redirect('companys');
        }
        
        return view('companys/detail', $data);
    }

    public function createCompany(Request $request)
    {
        return view('companys/form');
    }

}
