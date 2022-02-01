<?php

namespace App\Http\Controllers;

use App\Airline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AirlinesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $airlines=Airline::where('active','active')->get();
        return view('airlines.index',['airlines'=>$airlines]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('airlines.form');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Airline $airline)
    {
        //
        $data = [];
        DB::beginTransaction ();

        try {
            $data['airline_name'] = $request->input ('airline_name');
            $data['airline_tigrigna'] = $request->input ('airline_tigrigna');
            $data['airline_code'] = $request->input ('airline_code');
            $data['email'] = $request->input ('email');
            $data['telephone'] = $request->input ('telephone');
            $data['mobile'] = $request->input ('mobile');
            $data['asmara_address'] = $request->input ('asmara_address');
            $data['description'] = $request->input ('description');

            if ($request->isMethod ('post')) {
                $this->validate (
                    $request,
                    [
                    ]
                );

                $airline->insert ($data);
                DB::commit ();
                $airlines=Airline::where('active','active')->get();
                return view('airlines.index',['airlines'=>$airlines]);
            }
        } catch (Exception $e) {
            DB::rollBack ();
            return view ('airlilnes/form', $data);

        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Airline  $airline
     * @return \Illuminate\Http\Response
     */
    public function show(Airline $airline)
    {
        //
        $airlines=Airline::where('id',$airline->id)->first();
        return view('airlines.detail',['airlines'=>$airlines]);


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Airline  $airline
     * @return \Illuminate\Http\Response
     */
    public function edit(Airline $airline)
    {
        //
        $airlines=Airline::where('id',$airline->id)->first();
        return view('airlines.edit',['airlines'=>$airlines]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Airline  $airline
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Airline $airline)
    {
        //
        DB::beginTransaction ();

        try {
            Airline::where('id',$airline->id)->update([

                'airline_name'=>$request->input ('airline_name'),
                'airline_tigrigna'=>$request->input ('airline_tigrigna'),
                'airline_code'=>$request->input ('airline_code'),
                'email'=>$request->input ('email'),
                'telephone'=>$request->input ('telephone'),
                'mobile'=>$request->input ('mobile'),
                'asmara_address'=>$request->input ('asmara_address'),
                'description'=>$request->input ('description'),


            ]);


            DB::commit ();
            $airlines=Airline::where('active','active')->get();
            return view('airlines.index',['airlines'=>$airlines]);
        }

        catch (Exception $e) {
            DB::rollBack ();
            return view ('airlilnes/form', $data);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Airline  $airline
     * @return \Illuminate\Http\Response
     */
    public function destroy(Airline $airline)
    {
        //
        Airline::where('id',$airline->id)->update([

            'active'=>'inactive']);

        $airlines=Airline::where('active','active')->get();
        return view('airlines.index',['airlines'=>$airlines]);

    }
}
