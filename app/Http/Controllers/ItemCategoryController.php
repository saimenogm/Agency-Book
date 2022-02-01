<?php

namespace App\Http\Controllers;

use App\ItemCategory;
use Illuminate\Http\Request;

class ItemCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $categorys=ItemCategory::where('status','active')->get();
        return view('item_categorys.index',['itemCategorys'=>$categorys]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('item_categorys.form');

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
        ItemCategory::create([
            'category_name'=>$request->input('category_name'),
            'category_code'=>$request->input('category_code'),
            'category_description'=>$request->input('description'),
        ]);
        $categorys=ItemCategory::where('status','active')->get();
        return view('item_categorys.index',['itemCategorys'=>$categorys]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ItemCategory  $itemCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ItemCategory $itemCategory)
    {
        //
        $itemCategory_data=ItemCategory::where('id',$itemCategory->id)->first();

        $data['id'] = $itemCategory_data->id;
        $data['category_name'] = $itemCategory_data->category_name;
        $data['itemCategory_code'] = $itemCategory_data->category_code;
        $data['description'] = $itemCategory_data->category_description;
        return view('item_categorys/detail', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ItemCategory  $itemCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ItemCategory $itemCategory)
    {
        //
        $itemCategory_data=ItemCategory::where('id',$itemCategory->id)->first();

        $data['id'] = $itemCategory_data->id;
        $data['category_name'] = $itemCategory_data->category_name;
        $data['itemCategory_code'] = $itemCategory_data->category_code;
        $data['description'] = $itemCategory_data->category_description;
        return view('item_categorys/edit', $data);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ItemCategory  $itemCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ItemCategory $itemCategory)
    {
        //
        ItemCategory::where('id',$itemCategory->id)->update([
            'category_name'=>$request->input('category_name'),
            'category_code'=>$request->input('category_code'),
            'category_description'=>$request->input('description'),
        ]);
        $categorys=ItemCategory::where('status','active')->get();
        return view('item_categorys.index',['itemCategorys'=>$categorys]);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ItemCategory  $itemCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemCategory $itemCategory)
    {
        //
        ItemCategory::where('id',$itemCategory->id)->update([
            'status'=>'inactive']);
        $categorys=ItemCategory::where('status','active')->get();
        return view('item_categorys.index',['itemCategorys'=>$categorys]);

    }
}
