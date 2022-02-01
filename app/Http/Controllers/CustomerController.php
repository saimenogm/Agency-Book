<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer as Customer;
use App\Airline;

use Illuminate\Support\Facades\DB;

use App\Sale as Sale;

use PdfReport;
use PDF;


class CustomerController extends Controller
{
    //

    public function __construct(Customer $customer, Sale $sale )
    {
        $this->customer = $customer;
        $this->sale = $sale;
    }

    public function index()
    {
        $data = [];

        $data['customers'] = $this->customer->where('status','active')->get();

        return view('customers/index',$data);
        $data['from_date']='01-02-2019';
        $data['end_date']='30-02-2019';
        //return view('customers/index_customers', $data);

    }


    public function newCustomer(Request $request, Customer $customer)
    {
        $data = [];
        DB::beginTransaction();

        try
        {
            if($request->file('image')){

                $image = $request->file('image');
                $imageName= $image->getClientOriginalName();
                //dd($cover);
                request()->file('image')->move(public_path('images/products/customers'), $imageName);
            }
            else{
                $imageName='';
            }
            $data['first_name'] = strtoupper($request->input('first_name'));
            $data['middle_name'] = strtoupper($request->input('middle_name'));
            $data['last_name'] = strtoupper($request->input('last_name'));
            $data['image'] = $imageName;
            $data['dob'] = $request->input('dob');
            $data['doi'] = $request->input('doi');
            $data['doe'] = $request->input('doe');
            $data['telephone'] = $request->input('telephone');
            $data['address'] = $request->input('address');
            $data['account_number'] = $request->input('account_number');
            $data['mobile'] = $request->input('mobile');
            $data['remark'] = $request->input('remark');
            $data['passport_no'] = $request->input('passport_number');
            $data['email'] = $request->input('email');
            $data['contact_person'] = $request->input('contact_person');
            $data['company'] = $request->session()->get('company');
            $data['gender'] = $request->input('gender');


            if($request->input('balance_type_ern')=="unpaid")
            {
                $balance = $request->input('balance_amount_ern');
            }else if($request->input('balance_type_ern')=="overpaid")
            {
                $balance = -1* $request->input('balance_amount_ern');
            }

            $data['balance_ern'] = $balance;
            if($request->input('balance_type_usd')=="unpaid")
            {
                $balance = $request->input('balance_amount_usd');
            }else if($request->input('balance_type_usd')=="overpaid")
            {
                $balance = -1* $request->input('balance_amount_usd');
            }
            $data['balance_usd'] = $balance;

            if( $request->isMethod('post') )
            {
                //dd($data);

                $this->validate(
                    $request,
                    [
                    ]
                );


                $customer->insert($data);
                DB::commit();

                return redirect('customers/');
            }

            return view('customers/form', $data);

        }
            //this is for catch
        catch(Exception $e){
            DB::rollBack();

        }
    }


    public function modify( Request $request, $customer_id, Customer $customer )
    {
        $data = [];
        DB::beginTransaction();

        try
        {
            //find the customer

            if($request->file('image')){

                $image = $request->file('image');
                $imageName= $image->getClientOriginalName();
                //dd($cover);
                request()->file('image')->move(public_path('images/products/customers'), $imageName);
            }
            else{
                $imageName='';
            }
            $customer_data = $this->customer->find($customer_id);

            $customer_data->first_name = $request->input('first_name');
            $customer_data->middle_name = $request->input('middle_name');
            $customer_data->last_name = $request->input('last_name');
            $customer_data->dob = $request->input('dob');
            $customer_data->doi = $request->input('doi');
            $customer_data->doe = $request->input('doe');
            $customer_data->telephone = $request->input('telephone');
            $customer_data->address = $request->input('address');
            $customer_data->account_number = $request->input('account_number');
            $customer_data->mobile = $request->input('mobile');
            $customer_data->remark = $request->input('remark');
            $customer_data->passport_no = $request->input('passport_number');
            $customer_data->email = $request->input('email');
            $customer_data->contact_person = $request->input('contact_person');
            $customer_data->gender = $request->input('gender');
            $customer_data->image = $imageName;


            if($request->input('balance_type_usd')=="unpaid")
            {
                $balance = $request->input('balance_amount_usd');
            }else if($request->input('balance_type_usd')=="overpaid")
            {
                $balance = -1* $request->input('balance_amount_usd');
            }

            $customer_data->balance_usd = $balance;
            if($request->input('balance_type_ern')=="unpaid")
            {
                $balance = $request->input('balance_amount_ern');
            }else if($request->input('balance_type_ern')=="overpaid")
            {
                $balance = -1* $request->input('balance_amount_ern');
            }
            $customer_data->balance_ern = $balance;


            $customer_data->save();
            DB::commit();


            return redirect('customers');
        }

            //this is for catch
        catch(Exception $e){
            DB::rollBack();



        }

        return view('customer/detail', $data);



    }

    public function show($customer_id)
    {
        $data = [];
        $data['customer_id'] = $customer_id;
        $data['modify'] = 1;
        $customer_data = $this->customer->find($customer_id);
        $data['first_name'] = $customer_data->first_name;
        $data['middle_name'] = $customer_data->middle_name;
        $data['last_name'] = $customer_data->last_name;
        $data['dob'] = $customer_data->dob;
        $data['doi'] = $customer_data->doi;
        $data['doe'] = $customer_data->doe;
        $data['telephone'] = $customer_data->telephone;
        $data['address'] = $customer_data->address;
        $data['account_number'] = $customer_data->account_number;
        $data['mobile'] = $customer_data->mobile;
        $data['remark'] = $customer_data->remark;
        $data['passport_number'] = $customer_data->passport_no;
        $data['email'] = $customer_data->email;
        $data['contact_person'] = $customer_data->contact_person;
        $data['image'] = $customer_data->image;
        $data['gender'] = $customer_data->gender;



        if($customer_data->balance_ern>=0)
        {
            $data['balance_amount_ern'] =  abs($customer_data->balance_ern);
            $data['balance_type_ern'] = 'unpaid';
        }else if($customer_data->balance_ern<0)
        {
            $data['balance_amount_ern'] =  abs($customer_data->balance_ern);
            $data['balance_type_ern'] = 'overpaid';
        }
        if($customer_data->balance_usd >= 0)
        {
            $data['balance_amount_usd'] =  abs($customer_data->balance_usd);
            $data['balance_type_usd'] = 'unpaid';
        }else if($customer_data->balance_usd < 0)
        {
            $data['balance_amount_usd'] =  abs($customer_data->balance_usd);
            $data['balance_type_usd'] = 'overpaid';
        }
        $airlines=Airline::all();
        $freqs=DB::table('airlines')
            ->join('customer_freq_num','airlines.id','customer_freq_num.airline')
            ->where('customer_freq_num.customer',$customer_id)
            ->get();
        return view('customers/detail',['airlines'=>$airlines,'freqs'=>$freqs])->with( $data);
    }

    public function createCustomer(Request $request, Customer $customer)
    {

        return view('customers/form');

    }


    public function create()
    {
        return view('customers/create');
    }


    public function reporter(Request $request)
    {
        //$data = Customer::get();
        $data['customers'] = $this->customer->all();
        //dd($data);

        $data['from_date']='01-02-2019';
        $data['end_date']='30-02-2019';

        $pdf = PDF::loadView('customers/index_customers', $data);
        //return $pdf->download('customers.pdf');
        //$link = asset('css/additional_styles.css');
        //$pdf->set_base_path($link);

        $pdf->save(storage_path().'_filename.pdf');

        // Finally, you can download the file using download function
        return $pdf->stream('customers.pdf');
        //$pdf->render();
        //$pdf->stream();

    }

    public function reporter2(Request $request)
    {
        require_once "C:\xampp\htdocs\temp\sape\config";
        $dompdf = new DOMPDF();

        $html ="
            <html>
            <head>
            <link type='text/css' href='localhost/exampls/style.css' rel='stylesheet' />
            </head>
            <body>
            <table>
            <tr >
                <td class='abc'>testing table</td>
                </tr>
            <tr>
            <td class='def'>Testng header</td>
            </tr>
            </table>
            </body>
            </html>";

        $dompdf->load_html($html);
        $dompdf->render();
        $dompdf->set_base_path('localhost/exampls/style.css');
        $dompdf->stream("hello.pdf");
    }

    public function sales_report(Request $request)
    {
        //$data = Customer::get();
        $data['sales'] = $this->sale->all();
        //dd($data);
        $pdf = PDF::loadView('reports/sales_report', $data);
        //return $pdf->download('customers.pdf');

        $pdf->save(storage_path().'_filename.pdf');

        // Finally, you can download the file using download function
        return $pdf->stream('customers.pdf');
        //$pdf->render();
        //$pdf->stream();

    }

    public function delete_customer(Request $request,$customer_id)
    {
        DB::beginTransaction();

        try
        {

            $customer_data = $this->customer->find($customer_id);
            $customer_data->status='inactive';
            $data = [];

            $data['customers'] = $this->customer->where('id','active')->get();

            return view('customers/index', $data);


            DB::commit();

        }
            //this is for catch
        catch(Exception $e){
            DB::rollBack();



        }
    }

    public function add_feq(Request $request){


        $input = $request->all();

        try{

            $id=$request->id;
            $airline_name= $request->airline_name;
            $freq_num = $request->freq_num;
            $customer_id=$request->customer_id;
            DB::table('customer_freq_num')->insert(
                [
                    'customer'=>$id,
                    'airline'=>$airline_name,
                    'freq'=>$freq_num
                ]);


            $freqs=DB::table('airlines')
                ->join('customer_freq_num','airlines.id','customer_freq_num.airline')
                ->where('customer_freq_num.customer',$id)
                ->get();


            //dd($interations);

            $return_data = "";

            foreach($freqs as $freq){
                $return_data .= "<tr><td>".$freq->airline_name."</td>";
                $return_data .= "<td>".$freq->airline_tigrigna."</td>";
                $return_data .= "<td>".$freq->freq."</td>";
                $return_data.="<td><button class='btn btn-danger' onclick= 'del_freq(this)' value='".$freq->id."'><span class='icon-pencil5'> Delete</span></button></td></tr> ";

            }

            return response()->json(['freq'=>$return_data]);

        }
        catch(\Exception $e){

            DB::rollBack();
            return response()->json(['freq'=>$e,'item'=>'error'.$e]);
        }
        return response()->json(['freq'=>'generic','item'=>'item_success']);

    }
    public function del_freq(Request $request){

        try{

            $id=$request->id;
            $customer_id=$request->customer_id;
            $freq=DB::table('customer_freq_num')->where('id',$id);
            $freq->delete();

            $freqs=DB::table('airlines')
                ->join('customer_freq_num','airlines.id','customer_freq_num.airline')
                ->where('customer_freq_num.customer',$customer_id)
                ->get();


            //dd($interations);

            $return_data = "";

            foreach($freqs as $freq){
                $return_data .= "<tr><td>".$freq->airline_name."</td>";
                $return_data .= "<td>".$freq->airline_tigrigna."</td>";
                $return_data .= "<td>".$freq->freq."</td>";
                $return_data.="<td><button class='btn btn-danger' onclick= 'del_freq(this)' value='".$freq->id."'><span class='icon-pencil5'> Delete</span></button></td></tr> ";

            }

            return response()->json(['freq'=>$return_data]);

        }catch(\Exception $e){

            DB::rollBack();
            //throw $e;
            return response()->json(['freq'=>$e,'item'=>'error'.$e]);
        }
        return response()->json(['freq'=>'generic','item'=>'item_success']);

    }


}
