<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CategoryController extends Controller
{

    public function index()
    {

        $data = [];
        $data['categorys'] =DB::table('categorys')->get();
        return view('categorys/index',$data);

    }

    public function newCategory(Request $request)
    {
        $data = [];
        $data['category_name'] = $request->input('category_name');
        $data['category_code'] = $request->input('category_code');
        $data['description'] = $request->input('description');

        if( $request->isMethod('post') )
        {
            DB::table('categorys')->insert($data);
            return redirect('expense_categorys/');
        }

        return view('expense_categorys/form', $data);
    }

    public function createCategory(Request $request)
    {
        return view('categorys/form');
    }

    public function show($category_id)
    {
        $data = [];
        $data['category_id']=$category_id;
        $customer_data = DB::table('categorys')
            ->where('id',$category_id)
            ->first();

        $data['modify'] = 1;

        $data['category_name'] = $customer_data->category_name;
        $data['category_code'] = $customer_data->category_code;
        $data['description'] = $customer_data->description;


        return view('categorys/detail', $data);
    }

    public function modify(Request $request, $category_id )
    {
        $data = [];

        $data['category_name'] = $request->input('category_name');
        $data['category_code'] = $request->input('category_code');
        $data['description'] = $request->input('description');
        if( $request->isMethod('post') )
        {
            //dd($data);
            $this->validate(
                $request,
                [

                ]
            );


            DB::table('categorys')
                ->where('id',$category_id)
                ->update(['category_name'=>$request->input('category_name'),'category_code'=>$request->input('category_code'),'description'=>$request->input('description')]);
            return redirect()->route('category_index');
        }

        return view('categorys/detail', $data);
    }

//yordi_25@yahoo.ca

}



