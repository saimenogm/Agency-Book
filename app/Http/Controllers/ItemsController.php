<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;
use App\ItemCategory;
use Illuminate\Support\Facades\DB;
class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $items=DB::table('item_categories')
            ->join('items','items.item_category','item_categories.id')
            ->where('items.status','active')
            ->get();
        return view('items.index',['items'=>$items]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $categorys=ItemCategory::where('status','active')->get();
        return view('items.form',['categorys'=>$categorys]);
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
        Item::create(
            [
                'item_name'=>$request->input('item_name'),
                'item_category'=>$request->input('item_category'),
                'unit_cost'=>$request->input('unit_cost'),
                'unit_price'=>$request->input('unit_price'),
                'description'=>$request->input('description')
            ]
        );
        $items=DB::table('item_categories')
            ->join('items','items.item_category','item_categories.id')
            ->where('items.status','active')
            ->get();
        return view('items.index',['items'=>$items]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        //
        $categorys=ItemCategory::where('status','active')->get();
        $item=Item::where('id',$item->id)->first();
        return view('items.detail',['item'=>$item,'categorys'=>$categorys]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        //
        $categorys=ItemCategory::where('status','active')->get();
        $item=Item::where('id',$item->id)->first();
        return view('items.edit',['item'=>$item,'categorys'=>$categorys]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        //
        Item::where('id',$item->id)->update(
            [
                'item_name'=>$request->input('item_name'),
                'item_category'=>$request->input('item_category'),
                'unit_cost'=>$request->input('unit_cost'),
                'unit_price'=>$request->input('unit_price'),
                'description'=>$request->input('description')
            ]
        );
        $items=DB::table('item_categories')
            ->join('items','items.item_category','item_categories.id')
            ->where('items.status','active')
            ->get();
        return view('items.index',['items'=>$items]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        //
        Item::where('id',$item->id)->update(
            [
                'status'=>'inactive']);
        $items=DB::table('item_categories')
            ->join('items','items.item_category','item_categories.id')
            ->where('items.status','active')
            ->get();
        return view('items.index',['items'=>$items]);

    }
}
