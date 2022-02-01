<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expense as Expense;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $tmw=new Carbon('+1 days');
        $formatted_tmw=$tmw->toDateString();
        $today=new Carbon('-1 days');
        $formatted_today=$today->toDateString();


        $data['expenses']  = DB::table('expenses')->where('status','<>','paied')->get();
        $data['expense_count']=count($data['expenses'] );
        $data['ticket_orders']  = DB::table('ticket_orders')
            ->where('ticket_status','=','Booked')
            ->whereBetween('booking_expire_date', [$formatted_today, $formatted_tmw])
            ->get();
        $data['all_ticket_orders']  = DB::table('ticket_orders')
            ->where('status','=','active')
            ->get();
        $data['all_ticket_count']=count($data['all_ticket_orders'] );

        $data['ticket_count']=count($data['ticket_orders'] );
        $data['guest_orders']  = DB::table('guest_house_orders')->where('status','<>','active')->get();
        $data['guest_count']=count($data['guest_orders'] );

        $data['guest_orders']  = DB::table('guest_house_orders')->where('status','<>','active')->get();

        $data['sales_list'] = DB::table('sales')
            ->where ('status','!=','Void')
            ->select(DB::raw('SUM(total_amount_usd-amount_paid_usd) as total_unpaid_usd'),
                DB::raw('SUM(total_amount_ern-amount_paid_ern) as total_unpaid_ern'))
            ->first();

        $data['sales_list'] = DB::table('sales')
            ->where ('status','!=','Void')
            ->select(DB::raw('SUM(total_amount_usd-amount_paid_usd) as total_unpaid_usd'),
                DB::raw('SUM(total_amount_ern-amount_paid_ern) as total_unpaid_ern'))
            ->first();

        $data['receipts_list'] = DB::table('receipts')
            ->where ('status','!=','Void')
            ->select(DB::raw('SUM(total_amount_usd-deposited_usd) as total_undeposited_usd'),
                DB::raw('SUM(total_amount_ern-deposited_ern) as total_undeposited_ern'))
            ->first();

        //dd($data['sales_list']);


        return view('layouts.home',$data);
    }
    public function notification_index()
    {
        $data = [];

        $data['expenses'] =DB::table('expenses')->where('status','<>','paied')->get();


        return view('expenses/index', $data);
    }
    public function notification_ticket(){

        $tmw=new Carbon('+1 days');
        $formatted_tmw=$tmw->toDateString();
        $today=new Carbon();
        $formatted_today=$today->toDateString('-1 days');
        $data = [];
        $data['tickets'] = DB::table ('customers')
            ->join ('ticket_orders', 'customers.id', '=', 'ticket_orders.customer_id')
            ->where('ticket_orders.ticket_status','=','Booked')
            ->whereBetween('ticket_orders.booking_expire_date', [$formatted_today, $formatted_tmw])
            ->get();
        return view ('tickets/booking_expire_index', $data);
    }
    public function all_ticket_orders(){
        $data['tickets'] = DB::table ('customers')
            ->join ('ticket_orders', 'customers.id', '=', 'ticket_orders.customer_id')
            ->where('ticket_orders.status','=','active')
            ->get();
        return view ('tickets/index', $data);

    }
}
