<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;
use App\RoomCategory;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $rooms=DB::table('room_categories')
            ->join('rooms','room_categories.id','rooms.room_category')
            ->where('room_categories.status','active')
            ->select('room_categories.*','rooms.*')
            ->get();
        return view('rooms.index',['rooms'=>$rooms]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $categories=RoomCategory::where('status','active')
            ->get();

        $places=DB::table('places')
            ->get();

        return view('rooms.form',['category'=>$categories,'places'=>$places]);
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
        Room::create(
            [
                'room_name'=>$request->input('room_name'),
                'place'=>$request->input('place'),
                'room_category'=>$request->input('room_category'),
                'unit_price_day'=>$request->input('unit_price_day'),
                'floor'=>$request->input('floor'),
                'description'=>$request->input('description'),
                'status'=>$request->input('status'),
                'unit_price_month'=>$request->input('unit_price_month')
            ]
        );
        $rooms=DB::table('room_categories')
            ->join('rooms','room_categories.id','rooms.room_category')
            ->where('room_categories.status','active')
            ->select('room_categories.*','rooms.*')
            ->get();
        return view('rooms.index',['rooms'=>$rooms]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $rooms=DB::table('room_categories')
            ->join('rooms','room_categories.id','rooms.room_category')
            ->where('room_categories.status','active')
            ->where('rooms.id',$id)
            ->first();
//        $rooms=Room::where('id',$id)->first();
        $categories=RoomCategory::where('status','active')
            ->get();
//        dd($rooms);
        return view('rooms.detail',['room'=>$rooms,'categorys'=>$categories]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $rooms=DB::table('room_categories')
            ->join('rooms','room_categories.id','rooms.room_category')
            ->where('room_categories.status','active')
            ->where('rooms.id',$id)
            ->first();
        $categories=RoomCategory::where('status','active')
            ->get();
        return view('rooms.edit',['room'=>$rooms,'categorys'=>$categories]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        Room::where('id',$id)->update(
            [
                'room_name'=>$request->input('room_name'),
                'place'=>$request->input('place'),
                'room_category'=>$request->input('room_category'),
                'unit_price_month'=>$request->input('unit_price_month'),
                'floor'=>$request->input('floor'),
                'description'=>$request->input('description'),
                'status'=>$request->input('status'),
                'unit_price_day'=>$request->input('unit_price_day'),
            ]
        );
        $rooms=DB::table('room_categories')
            ->join('rooms','room_categories.id','rooms.room_category')
            ->where('room_categories.status','active')
            ->select('room_categories.*','rooms.*')
            ->get();
        return view('rooms.index',['rooms'=>$rooms]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
