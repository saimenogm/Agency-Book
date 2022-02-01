<?php

namespace App\Http\Controllers;

use App\Place;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
class PlacesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $places=Place::where('status','active')->get();
        return view('places.index',['places'=>$places]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('places.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Place $place)
    {
        //
        $data = [];
        DB::beginTransaction ();

        try {
            $data['place_name'] = $request->input ('place_name');
            $data['address'] = $request->input ('address');
            $data['description'] = $request->input ('description');
            $data['telephone'] = $request->input ('telephone');

            if ($request->isMethod ('post')) {
                $this->validate (
                    $request,
                    [
                    ]
                );

                $place->insert ($data);
                DB::commit ();
                return redirect ('places');
            }
        } catch (Exception $e) {
            DB::rollBack ();
            return view ('places/form', $data);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Place  $place
     * @return \Illuminate\Http\Response
     */
    public function show(Place $place)
    {
        //
        $place=Place::where('id',$place->id)->first();
        return view('places.detail',['place'=>$place]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Place  $place
     * @return \Illuminate\Http\Response
     */
    public function edit(Place $place)
    {
        //
        $place=Place::where('id',$place->id)->first();
        return view('places.edit',['place'=>$place]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Place  $place
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Place $place)
    {
        //
        DB::beginTransaction ();

        try {
            Place::where('id',$place->id)->update([
                'place_name'=> $request->input ('place_name'),
                'address'=> $request->input ('address') ,
                'description'=> $request->input ('description'),
                'telephone' => $request->input ('telephone'),

            ]);

            DB::commit ();
            return redirect ('places');
        }

        catch (Exception $e) {
            DB::rollBack ();


        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Place  $place
     * @return \Illuminate\Http\Response
     */
    public function destroy(Place $place)
    {
        //
        Place::where('id',$place->id)->update([
            'status'=> ('inactive')]);
        return redirect ('places');

    }
}
