<?php

namespace App\Http\Controllers;

use App\RoomCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomCategorysController extends Controller
{

    public function __construct(RoomCategory $roomCategory)
    {
        $this->roomCategory = $roomCategory;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $roomcategories=RoomCategory::where('status','active')->get();
        return view('room_categories.index',['category'=>$roomcategories]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('room_categories.form');
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
        RoomCategory::create([
            'category_name'=>$request->input('category_name'),
            'description'=>$request->input('description'),
        ]);
        $roomcategories=RoomCategory::where('status','active')->get();
        return view('room_categories.index',['category'=>$roomcategories]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RoomCategory  $roomCategory
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$category_id)
    {
        //
        $roomcategories=RoomCategory::where('id',$category_id)->first();
        return view('room_categories.detail',['category'=>$roomcategories]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RoomCategory  $roomCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $category_id)
    {
        //
        //dd($category_id);
        $roomcategories=RoomCategory::where('id',$category_id)->first();

        return view('room_categories.edit',['category'=>$roomcategories]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RoomCategory  $roomCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RoomCategory $roomCategory,$id)
    {
        //
        RoomCategory::where('id',$id)->update([
            'category_name'=>$request->input('category_name'),
            'description'=>$request->input('description'),
        ]);
        $roomcategories=RoomCategory::where('status','active')->get();
        return view('room_categories.index',['category'=>$roomcategories]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RoomCategory  $roomCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($category_id)
    {
        //
        RoomCategory::where('id',$category_id)->update([
            'status'=>'inactive',
        ]);
        $roomcategories=RoomCategory::where('status','active')->get();
        return view('room_categories.index',['category'=>$roomcategories]);
    }
}
