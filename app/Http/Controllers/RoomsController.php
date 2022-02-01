<?php

namespace App\Http\Controllers;

use App\Room;
use App\RoomCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $rooms=DB::table('rooms')
            ->join('room_categories','room_categories.id','rooms.room_category')
            ->where('room_categories.status','active')
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
        // dd($request->input('room_category'));
        Room::create(
            [
                'room_name'=>$request->input('room_name'),
                'place'=>$request->input('place'),
                'room_category'=>$request->input('room_category'),
                'unit_price'=>$request->input('unit_price'),
                'floor'=>$request->input('floor'),
                'room_description'=>$request->input('description'),
                'status'=>$request->input('status'),
            ]
        );
        $rooms=DB::table('rooms')
            ->join('room_categories','room_categories.id','rooms.room_category')
            ->where('room_categories.status','active')
            ->get();
        return view('rooms.index',['rooms'=>$rooms]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room)
    {
        //

        $rooms=DB::table('rooms')
            ->join('room_categories','room_categories.id','rooms.room_category')
            ->where('room_categories.status','active')
            ->where('rooms.id',$room->id)
            ->first();
        $categories=RoomCategory::where('status','active')
            ->get();
        return view('rooms.detail',['room'=>$rooms,'categorys'=>$categories]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function edit(Room $room)
    {
        //
        $rooms=DB::table('rooms')
            ->join('room_categories','room_categories.id','rooms.room_category')
            ->where('room_categories.status','active')
            ->where('rooms.id',$room->id)
            ->first();
        $categories=RoomCategory::where('status','active')
            ->get();
        return view('rooms.edit',['room'=>$rooms,'categorys'=>$categories]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Room $room)
    {
        //
        Room::where('id',$room->id)->update(
            [
                'room_name'=>$request->input('room_name'),
                'place'=>$request->input('place'),
                'room_category'=>$request->input('room_category'),
                'unit_price'=>$request->input('unit_price'),
                'floor'=>$request->input('floor'),
                'room_description'=>$request->input('description'),
                'status'=>$request->input('status'),
            ]
        );
        $rooms=DB::table('rooms')
            ->join('room_categories','room_categories.id','rooms.room_category')
            ->where('room_categories.status','active')
            ->get();
        return view('rooms.index',['rooms'=>$rooms]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        //

    }
}
