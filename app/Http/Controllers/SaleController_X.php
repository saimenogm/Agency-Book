<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Sale as Sale;
use App\Product as Product;
use App\SaleItem as SaleItem;
use App\Item as Item;
use App\Ticket as Ticket;
use App\Visa as Visa;
use App\SaleOrder as SaleOrder;
use App\SaleOrderItem as SaleOrderItem;
use App\Customer as Customer;
use App\TicketOrder as TicketOrder;
use App\VisaOrder as VisaOrder;
use App\Airline as Airline;
use App\GuestHouseOrder as GuestHouseOrder;
use App\GuestHouse as GuestHouse;


use PdfReport;
use PDF;

use Illuminate\Support\Facades\DB;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

class SaleController extends Controller
{
    //
    public function __construct(Sale $sale, SaleOrder $sale_order, Customer $customer, Item $item)
    {
        $this->sale = $sale;
        $this->sale_order = $sale_order;
        $this->customer = $customer;
        $this->item = $item;
    }

    public function index()
    {
        $data = [];
        $data['sales'] = DB::table ('customers')
            ->join ('sales', 'customers.id', '=', 'sales.customer')
            ->get ();
        return view ('sales/index', $data);
    }

    public function index_order()
    {
        $data = [];
        $data['sales'] = DB::table ('customers')
            ->join ('sale_orders', 'customers.id', '=', 'sale_orders.customer')
            ->get ();
        return view ('sales/index_order', $data);
    }

    public function index_order_active()
    {
        $data = [];
        $data['sales'] = DB::table ('customers')
            ->join ('sale_orders', 'customers.id', '=', 'sale_orders.customer')
            ->where('sale_orders.status','=','active')
            ->get ();
        return view ('sales/index_order_active', $data);
    }

    public function createSale(Request $request, Sale $sale)
    {
        $data = [];

        $data['customers'] = DB::table ('customers')
            ->get ();
        $data['airlines'] = DB::table ('airlines')
            ->get ();
        $data['suppliers'] = DB::table ('suppliers')
            ->get ();
        $data['visa_suppliers'] = DB::table ('visa_suppliers')
            ->get ();

        $data['places'] = DB::table ('places')
            ->get ();

        $data['rooms'] = DB::table ('rooms')
            ->get ();

        $data['items'] = DB::table ('items')
            ->get ();


        // Prepared by

        return view ('sales/new_sale', $data);
    }

    public function createSaleCustomer(Request $request, $customer_id)
    {
        $data = [];

        $data['customers'] = DB::table ('customers')
            ->get ();

        $data['customer'] = DB::table ('customers')
            ->where("id","=",$customer_id)
            ->get ();

        $data['airlines'] = DB::table ('airlines')
            ->get ();
        $data['suppliers'] = DB::table ('suppliers')
            ->get ();
        $data['visa_suppliers'] = DB::table ('visa_suppliers')
            ->get ();

        $data['places'] = DB::table ('places')
            ->get ();

        $data['rooms'] = DB::table ('rooms')
            ->get ();

        $data['items'] = DB::table ('items')
            ->get ();
        return view ('sales/new_sale_customer', $data);
    }

    public function createSaleOrder(Request $request, Sale $sale)
    {
        $data = [];

        $data['customers'] = $this->customer->all ();

        $data['products'] = DB::table ('items')
            ->select ('items.*')
            ->get ();

        $data['customers'] = DB::table ('customers')
            ->get ();
        $data['airlines'] = DB::table ('airlines')
            ->get ();
        $data['suppliers'] = DB::table ('suppliers')
            ->get ();
        $data['visa_suppliers'] = DB::table ('visa_suppliers')
            ->get ();

        $data['places'] = DB::table ('places')
            ->get ();

        $data['rooms'] = DB::table ('rooms')
            ->get ();

        $data['items'] = DB::table ('items')
            ->get ();

        return view ('sales/new_sale_order', $data);
    }

    public function createSaleOrderCustomer(Request $request, $customer_id)
    {
        $data = [];

        $data['customers'] = $this->customer->all ();

        $data['customer'] = DB::table ('customers')
            ->where("id","=",$customer_id)
            ->get ();

        $data['products'] = DB::table ('items')
            ->select ('items.*')
            ->get ();

        $data['customers'] = DB::table ('customers')
            ->get ();
        $data['airlines'] = DB::table ('airlines')
            ->get ();
        $data['suppliers'] = DB::table ('suppliers')
            ->get ();
        $data['visa_suppliers'] = DB::table ('visa_suppliers')
            ->get ();

        $data['places'] = DB::table ('places')
            ->get ();

        $data['rooms'] = DB::table ('rooms')
            ->get ();

        $data['items'] = DB::table ('items')
            ->get ();

        return view ('sales/new_sale_order_customer', $data);
    }

    public function createSaleFromOrder(Request $request, $sales_id)
    {
        $data = [];

        $data['customers'] = $this->customer->all ();

        $data['products'] = DB::table ('items')
            ->select ('items.*')
            ->get ();

        $data['customers'] = DB::table ('customers')
            ->get ();
        $data['airlines'] = DB::table ('airlines')
            ->get ();
        $data['suppliers'] = DB::table ('suppliers')
            ->get ();
        $data['visa_suppliers'] = DB::table ('visa_suppliers')
            ->get ();

        $data['places'] = DB::table ('places')
            ->get ();

        $data['rooms'] = DB::table ('rooms')
            ->get ();

        $data['items'] = DB::table ('items')
            ->get ();

        $data['modify'] = 1;
        $sales_date = $this->sale_order->find ($sales_id);
        $d = $sales_date->date;
        $date_converted = date ("d/m/Y", strtotime ($d));

        $data['sales'] = DB::table ('customers')
            ->join ('sale_orders', 'customers.id', '=', 'sale_orders.customer')
            ->where ('sale_orders.id', $sales_id)->first ();

        $data['sale_id'] = $sales_id;

        $sales_item2 = DB::table ('sale_order_items')
            ->where ('sale_order_items.sale_id', $sales_id)
            ->get ();

        //dd($sales_item2);
        $data['sales_item'] = $sales_item2;


        return view ('sales/new_sale_from_order', $data);
    }

    public function newSale(Request $request, Sale $sale)
    {

        $data = [];
        $total_amount = 0.00;

        DB::beginTransaction ();

        try {

            $input = $request->all ();

            $unit_price_list = $request->unit_price_list;
            $customer_id = $request->customer_id;
            $airline_list = $request->airline_list;
            $unit_cost_list = $request->unit_cost_list;
            $item_list = $request->items_list;
            $qty_list = $request->qty_list;
            $discount_list = $request->discount_list;
            $ticket_list = $request->ticket_list;
            $item_type_list = $request->item_type_list;


            $user = auth ()->user ();


            // Save Sale
            $sale = new Sale();
            $sale->customer = $customer_id;
            $sale->ref = "";
            $sale->date = '2018-01-02';
            $sale->total_amount = 0.00;
            $sale->user = $customer_id;
            $sale->user_name = 'Simon';
            $sale->save ();

            $count = count ($qty_list);
            $total_amount = 0.00;

            for ($i = 1; $i < $count; $i++) {
                $saleItem = new SaleItem();
                $saleItem->item = 1;
                $saleItem->qty = 2;
                $saleItem->date = '2018-01-02';
                $saleItem->unit_price = 0.00;
                $saleItem->discount = 0.00;
                $saleItem->sub_total = 0.00;//($saleItem->qty * $saleItem->unit_price) - $saleItem->discount;
                $saleItem->unit_cost = $unit_cost_list[$i];
                $saleItem->variant = 0;
                $saleItem->item_type = $item_type_list[$i];
                $total_amount += 0.00;//$saleItem->sub_total;
                $saleItem->sale ()->associate ($sale);
                $saleItem->save ();

                if ($saleItem->item_type == "Ticket") {
                    $newTicket = new Ticket();
                    $newTicket->selling_price = 100;
                    $newTicket->flight_date = '2018-01-02';
                    $newTicket->return_date = '2018-01-02';
                    $newTicket->purchase_date = '2018-01-02';
                    $newTicket->save ();
                } else if ($saleItem->item_type == "Visa") {
                    $newVisa = new Visa();
                    /*
                     *
                     *             $newVisa->selling_price = 100;
                                $newTicket->flight_date = '2018-01-02';
                                $newTicket->return_date = '2018-01-02';
                                $newTicket->purchase_date = '2018-01-02';
                                $newTicket->save();

                     */
                }
            }

            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => '']);

        } catch (\Exception $e) {

            //    DB::rollBack();
            //throw $e;
            return response ()->json (['success' => 'UN SUCCESSFUL' . $unit_price_list, 'item' => 'error' . $e, 'barcode' => $e]);

        }
    }

    public function newAgencySale(Request $request, Sale $sale)
    {
        DB::beginTransaction();

        try {

            $total_amount_usd = 0.00;
            $total_amount_ern = 0.00;

            $input = $request->all ();

            $unit_price_list = $request->unit_price_list;
            $selling_price_list = $request->selling_price_list;
            $unit_cost_list = $request->airline_payed_list;
            $customer_id = $request->customer_id;
            $date_sale = $request->date_sale;
            $payment_id = $request->payment_id;

            //$user = auth()->user();

            // Save Sale
            $sale = new Sale();
            $sale->customer = $customer_id;
            $sale->ref = "";
            $sale->date = $date_sale;
            $sale->total_amount_ern = 0.00;
            $sale->total_amount_usd = 0.00;
            $sale->user = $customer_id;
            $sale->user_name = 'Simon';
            $sale->payment_mode = $payment_id;
            $sale->company = session ()->get('company');

            if ($payment_id == "Cash") {
                $sale->status = "Paid";
            }
            $sale->save ();

            $item_list = $request->items_list;
            $qty_list = $request->qty_list;
            $airline_list = $request->airline_list;
            $ticket_number_list = $request->ticket_number_list;
            $po_number_list = $request->po_number_list;
            $item_type_list = $request->item_type_list;
            $flight_date_list = $request->flight_date_list;
            $return_date_list = $request->return_date_list;
            $ticket_status_list = $request->ticket_status_list;
            $laguage_list = $request->laguage_list;
            $remark_list = $request->remark_list;
            $sector_list = $request->sector_list;
            $travel_agency_list = $request->travel_agency_list;
            $commision_list = $request->commision_list;
            $airline_payed_list = $request->airline_payed_list;
            $selling_price_list = $request->selling_price_list;
            $discount_list = $request->discount_list;
            $currency_list = $request->currency_list;
            $base_fare_list = $request->base_fare_list;
            $tax_list = $request->tax_list;
            $addition_list = $request->addition_list;
            $purchase_date_list = $request->purchase_date_list;
            $gross_list = $request->gross_list;
            $service_tax_list = $request->service_tax_list;
            $net_profit_list = $request->net_profit_list;
            $unit_price_list = $request->unit_price_list;
            $visa_supplier_list = $request->visa_supplier_list;
            $visa_period_list = $request->visa_period_list;
            $visa_date_of_issue_list = $request->visa_date_of_issue_list;
            $visa_valid_until_list = $request->visa_valid_until_list;
            $visa_status_list = $request->visa_status_list;
            $sub_total_list = $request->sub_total_list;
            $place_list = $request->place_list;
            $room_no_list = $request->room_no_list;
            $invoice_num_list = $request->invoice_num_list;
            $sale_order_item_list = $request->sale_order_item_list;


            $items_count = count ($item_list);
            $count = count ($item_list);

            $total_amount = 0.00;


            for ($i = 1; $i < $count; $i++) {

                $saleItem = new SaleItem();
                $saleItem->item = $item_list[$i];
                $saleItem->qty = $qty_list[$i];
                $saleItem->date = $date_sale;
                $saleItem->currency = $currency_list[$i];
                $saleItem->unit_price = $selling_price_list[$i];
                $saleItem->discount = $discount_list[$i];
                $saleItem->sub_total = $sub_total_list[$i];//($saleItem->qty * $saleItem->unit_price) - $saleItem->discount;
                $saleItem->unit_cost = $unit_cost_list[$i];
//                $saleItem->tax = $service_tax_list[$i];
                $saleItem->variant = 0;
                $saleItem->item_type = $item_type_list[$i];
                if($saleItem->item_type == "Ticket"){
                    $saleItem->gross = $gross_list[$i];
                }else{
                    $saleItem->gross = $gross_list[$i];
                }
                $total_amount += $saleItem->sub_total;
                $saleItem->company = session ()->get('company');
                $saleItem->sale ()->associate ($sale);
                $saleItem->save ();

                if ($currency_list[$i]=="USD") {
                    $total_amount_usd += $sub_total_list[$i];
                } else {
                    $total_amount_ern += $sub_total_list[$i];
                }


                if ($saleItem->item_type == "Ticket") {

                    $newTicket = new Ticket();
                    $newTicket->customer_id = $customer_id;
                    $newTicket->airline = $airline_list[$i];
                    $newTicket->travel_agency = $travel_agency_list[$i];
                    $newTicket->commision_amount = $commision_list[$i];
                    $newTicket->selling_price = $selling_price_list[$i];
                    $newTicket->flight_date = $flight_date_list[$i];
                    $newTicket->return_date = $return_date_list[$i];
                    $newTicket->purchase_date = $purchase_date_list[$i];
                    $newTicket->selling_price = $selling_price_list[$i];
                    $newTicket->po_number = $po_number_list[$i];
                    $newTicket->currency = $currency_list[$i];
                    $newTicket->base_fare = floatval ($base_fare_list[$i]);
                    $newTicket->tax = $tax_list[$i];
                    $newTicket->total_fare = $unit_price_list[$i];
                    $newTicket->addition = $addition_list[$i];
                    $newTicket->discount = $discount_list[$i];
                    $newTicket->gross = $gross_list[$i];
                    $newTicket->ticket_number = $ticket_number_list[$i];
                    $newTicket->inland_tax = $service_tax_list[$i];
                    $newTicket->sector = $sector_list[$i];
                    $newTicket->airline_payed = $airline_payed_list[$i];
                    $newTicket->net_commission = $net_profit_list[$i];
                    $newTicket->ticket_status = $ticket_status_list[$i];
                    $newTicket->sale_item_id = $saleItem->id;
                    $newTicket->remark = $remark_list[$i];
                    $newTicket->invoice_num = $invoice_num_list[$i];
                    $newTicket->company = session ()->get('company');

                    $newTicket->save ();

//                    if ($currency_list[$i]=="USD") {
//                        $total_amount_usd += $newTicket->selling_price;
//                    } else {
//                        $total_amount_ern += $newTicket->selling_price;
//                    }
                }

                if ($saleItem->item_type == "Visa") {

                    $newVisa = new Visa();
                    $newVisa->visa_supplier = $visa_supplier_list[$i];
                    $newVisa->customer_id = $customer_id;
                    $newVisa->visa_period = $visa_period_list[$i];
                    $newVisa->visa_date_of_issue = $visa_date_of_issue_list[$i];
                    $newVisa->visa_valid_until = $visa_valid_until_list[$i];
                    $newVisa->visa_status = $visa_status_list[$i];
                    $newVisa->purchase_date = $purchase_date_list[$i];
                    $newVisa->currency = $currency_list[$i];

                    $newVisa->unit_price = $selling_price_list[$i];
                    $newVisa->unit_cost = $unit_cost_list[$i];
                    $newVisa->gross = $gross_list[$i];
                    $newVisa->sale_item_id = $saleItem->id;
                    $newVisa->company = session ()->get('company');


                    $newVisa->save ();


                }

                if($saleItem->item_type=="Guest House")
                {
                    $GuestHouse = new GuestHouse();
                    $GuestHouse->room_no = $room_no_list[$i];
                    $GuestHouse->place = $place_list[$i];
                    $GuestHouse->currency = $currency_list[$i];
                    $GuestHouse->price = $unit_price_list[$i];
                    $GuestHouse->gross = $gross_list[$i];
                    $GuestHouse->from_date = $visa_date_of_issue_list[$i];
                    $GuestHouse->to_date = $visa_valid_until_list[$i];
                    $GuestHouse->sale_id = $sale->id;
                    $GuestHouse->sale_item_id =$saleItem->id;
                    $GuestHouse->company = session ()->get('company');

                    $GuestHouse->save();

//                    if ($currency_list[$i]=="USD") {
//                        $total_amount_usd += $unit_price_list[$i];
//                    } else {
//                        $total_amount_ern += $unit_price_list[$i];
//                    }
                }

                if(count($sale_order_item_list)>0){

                    // Update table sale order items
                    if($sale_order_item_list[$i]!=null){
                        DB::table('sale_order_items')
                            ->where('id', $sale_order_item_list[$i] )
                            ->update(['status'=>"Sold"
                            ]);
                    }

                    //
                }

            }

            $sale->total_amount_ern = $total_amount_ern;
            $sale->total_amount_usd = $total_amount_usd;


            $customer_data = DB::table('customers')
                ->where('id', $customer_id)
                ->first();

            $customer_balance_usd = $customer_data->balance_usd;
            $customer_balance_ern = $customer_data->balance_ern;

            if ($payment_id == "Cash") {

                $sale->status = "Paid";

                $sale->amount_paid_ern = $total_amount_ern;
                $sale->amount_paid_usd = $total_amount_usd;

            }else if($payment_id == "Credit"){

                $customer_balance_usd+=$total_amount_usd;
                $customer_balance_ern+=$total_amount_ern;

                DB::table('customers')
                    ->where('id', $customer_id )
                    ->update(['balance_usd'=>$customer_balance_usd,
                        'balance_ern'=>$customer_balance_ern
                    ]);
            }

            // Insert into payment


            $sale->save ();

            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => '', 'unit_price' => $sale_order_item_list]);

        } catch (\Exception $e) {

            return response ()->json (['success' => 'UN SUCCESSFUL' . $e, 'item' => 'error' . $e, 'unit_price' => $e]);

        }
    }

    public function newAgencySaleOrder(Request $request, Sale $sale)
    {
        DB::beginTransaction();

        try
        {
            $input = $request->all ();

            $customer_id = $request->customer_id;
            $date_sale = $request->date_sale;
            $payment_id = $request->payment_id;

            $item_list = $request->items_list;
            $qty_list = $request->qty_list;
            $airline_list = $request->airline_list;
            $discount_list = $request->discount_list;
            $ticket_number_list = $request->ticket_number_list;
            $po_number_list = $request->po_number_list;
            $item_type_list = $request->item_type_list;
            $flight_date_list = $request->flight_date_list;
            $return_date_list = $request->return_date_list;
            $laguage_list = $request->laguage_list;
            $commision_list = $request->commision_list;
            $airline_payed_list = $request->airline_payed_list;
            $selling_price_list = $request->selling_price_list;
            $currency_list = $request->currency_list;
            $base_fare_list = $request->base_fare_list;
            $tax_list = $request->tax_list;
            $addition_list = $request->adition_list;
            $purchase_date_list = $request->purchase_date_list;
            $ticket_status_list = $request->ticket_status_list;
            $gross_list = $request->gross_list;
            $remark_list = $request->remark_list;
            $service_tax_list = $request->service_tax_list;
            $net_profit_list = $request->net_profit_list;
            $unit_price_list = $request->unit_price_list;
            $sector_list = $request->sector_list;
            $visa_supplier_list = $request->visa_supplier_list;
            $visa_period_list = $request->visa_period_list;
            $visa_date_of_issue_list = $request->visa_date_of_issue_list;
            $visa_valid_until_list = $request->visa_valid_until_list;
            $visa_status_list = $request->visa_status_list;
            $travel_agency_list = $request->travel_agency_list;
            $place_list = $request->place_list;
            $room_no_list = $request->room_no_list;
            $booking_number_list = $request->booking_number_list;
            $description_list = $request->description_list;
            $item_id_list = $request->item_id_list;
            $sale_remark = $request->sale_remark;


            $items_count = count($item_list);
            $count = count($item_list);

            $total_amount=0.00;

            // Save Sale
            $sale_order = new SaleOrder();

            $sale_order->customer =$customer_id;
            $sale_order->ref = "";
            $sale_order->date = $date_sale;
            $sale_order->total_amount = 0.00;
            $sale_order->user = $customer_id;
            $sale_order->user_name = 'Simon';
            $sale_order->payment_mode = $payment_id;
            $sale_order->company = session ()->get('company');
            $sale_order->remark = $sale_remark;
            $sale_order->save();


            for($i=1;$i<$count;$i++)
            {
                $saleItem = new SaleOrderItem();
                $saleItem->item = $item_list[$i];
                $saleItem->qty = $qty_list[$i];
                $saleItem->date = $date_sale;
                //$saleItem->unit_price = $unit_price_list[$i];
                $saleItem->discount = 0.00;
                $saleItem->sub_total = 0.00;//($saleItem->qty * $saleItem->unit_price) - $saleItem->discount;
                $saleItem->unit_cost = 100.00;
                $saleItem->currency = $currency_list[$i];
                $saleItem->item_type = $item_type_list[$i];
                $saleItem->description = $description_list[$i];
                $saleItem->company = session ()->get('company');

                if(count($item_id_list)>0){
                    if($item_id_list[$i]=='' || $item_id_list[$i]==null){
                    }else{
                        $saleItem->item_id = $item_id_list[$i];
                    }
                }
                $total_amount +=0.00;//$saleItem->sub_total;
                $saleItem->sale()->associate($sale_order);
                $saleItem->save();

                if($saleItem->item_type=="Ticket"){

                    $newTicket = new TicketOrder();
                    $newTicket->customer_id = $customer_id;
                    $newTicket->airline = $airline_list[$i];
                    $newTicket->travel_agency = $travel_agency_list[$i];
                    $newTicket->flight_date = $flight_date_list[$i];
                    $newTicket->return_date = $return_date_list[$i];
                    $newTicket->purchase_date = $purchase_date_list[$i];
                    $newTicket->po_number = $po_number_list[$i];
                    $newTicket->currency = $currency_list[$i];
                    $newTicket->sector = $sector_list[$i];
                    $newTicket->ticket_status = $ticket_status_list[$i];
                    $newTicket->remark = $remark_list[$i];
                    $newTicket->booking_number = $booking_number_list[$i];
                    $newTicket->sale_item_id =$saleItem->id;
                    $newTicket->company = session ()->get('company');
                    $newTicket->save();
                }

                if($saleItem->item_type=="Visa")
                {
                    $newVisa = new VisaOrder();
                    $newVisa->visa_supplier = $visa_supplier_list[$i];
                    //$newTicket->sector = $sector_list[$i];
                    $newVisa->visa_period =$visa_period_list[$i];
                    $newVisa->visa_date_of_issue =$visa_date_of_issue_list[$i];
                    $newVisa->visa_valid_until =$visa_valid_until_list[$i];
                    $newVisa->visa_status = $visa_status_list[$i];
                    $newVisa->remark = $remark_list[$i];
                    $newVisa->sale_item_id =$saleItem->id;
                    $newVisa->company = session ()->get('company');
                    $newVisa->currency = $currency_list[$i];
                    //$newVisa->purchase_date = $purchase_date_list[$i];
                    $newVisa->save();
                }

                if($saleItem->item_type=="Guest House")
                {
                    $GuestHouse = new GuestHouseOrder();
                    $GuestHouse->room_no = $room_no_list[$i];
                    $GuestHouse->place = $place_list[$i];
                    $GuestHouse->from_date = $visa_date_of_issue_list[$i];
                    $GuestHouse->to_date = $visa_valid_until_list[$i];
                    $GuestHouse->sale_id = $sale_order->id;
                    $GuestHouse->sale_item_id =$saleItem->id;
                    $GuestHouse->company = session ()->get('company');
                    $GuestHouse->save();
                }

            }

            DB::commit();

            return response()->json(['success'=>'DONE','item'=>'BBBBBBBBBBB','sale_id'=>'','unit_price'=>$visa_date_of_issue_list[0]]);

        }catch(\Exception $e){

            return response()->json(['success'=>'UN SUCCESSFUL '.$e,'error: '.$e,'item'=>'error'.$e,'unit_price'=>$e]);

        }


        return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => '', 'unit_price' => "hello"]);

    }

    public function show($sales_id)
    {
        $data = [];

        $data['customers'] = $this->customer->all ();

        $data['airlines'] = DB::table ('airlines')
            ->get ();
        $data['suppliers'] = DB::table ('suppliers')
            ->get ();
        $data['visa_suppliers'] = DB::table ('visa_suppliers')
            ->get ();

        $data['places'] = DB::table ('places')
            ->get ();

        $data['rooms'] = DB::table ('rooms')
            ->get ();

        $data['items'] = DB::table ('items')
            ->get ();


        $data['modify'] = 1;
        $sales_date = $this->sale->find ($sales_id);
        $d = $sales_date->date;
        $date_converted = date ("d/m/Y", strtotime ($d));

        $data['sales'] = DB::table ('customers')
            ->join ('sales', 'customers.id', '=', 'sales.customer')
            ->where ('sales.id', $sales_id)->first ();

        $data['sale_id'] = $sales_id;

        $sales_item2 = DB::table ('sale_items')
            ->where ('sale_items.sale_id', $sales_id)
            ->get ();

        //dd($sales_item2);
        $data['sales_item'] = $sales_item2;
        return view ('sales/detail', $data)->with ('conv_date', $date_converted);
    }

    public function showItem(Request $request)
    {

        $input = $request->all ();

        $sale_item_id = $request->item_id;

        $data = [];

        $data['customers'] = $this->customer->all ();

        $data['airlines'] = DB::table ('airlines')
            ->get ();
        $data['suppliers'] = DB::table ('suppliers')
            ->get ();
        $data['visa_suppliers'] = DB::table ('visa_suppliers')
            ->get ();

        $data['places'] = DB::table ('places')
            ->get ();

        $data['rooms'] = DB::table ('rooms')
            ->get ();

        $data['items'] = DB::table ('items')
            ->get ();


        $item_detail = DB::table ('sale_items')
            ->where ('sale_items.id', $sale_item_id)->first ();



        if($item_detail->item=="Ticket"){
            $sales_item2 = DB::table ('tickets')
                ->where ('tickets.sale_item_id', $sale_item_id)
                ->get ();
        }elseif ($item_detail->item=="Visa"){
            $sales_item2 = DB::table ('visas')
                ->where ('visas.sale_item_id', $sale_item_id)
                ->get ();
        }elseif ($item_detail->item=="Guest House"){
            $sales_item2 = DB::table ('guest_houses')
                ->where ('guest_houses.sale_item_id', $sale_item_id)
                ->get ();
        }

        //dd($sales_item2);
        $data['sales_item'] = $sales_item2;
        $data['item_type'] = $item_detail->item;
        return response ()->json (['success' => 'DONE', 'item' => $sales_item2,'item_type'=>$item_detail->item, 'sale_id' => '']);
    }

    public function showItemOrder(Request $request)
    {

        $input = $request->all ();

        DB::beginTransaction ();

        try {

            $sale_item_id = $request->item_id;

            $data = [];

            $data['customers'] = $this->customer->all ();

            $data['airlines'] = DB::table ('airlines')
                ->get ();
            $data['suppliers'] = DB::table ('suppliers')
                ->get ();
            $data['visa_suppliers'] = DB::table ('visa_suppliers')
                ->get ();

            $data['places'] = DB::table ('places')
                ->get ();

            $data['rooms'] = DB::table ('rooms')
                ->get ();

            $data['items'] = DB::table ('items')
                ->get ();


            $item_detail = DB::table ('sale_order_items')
                ->where ('sale_order_items.id', $sale_item_id)->first ();

            if($item_detail->item=="Ticket"){
                $sales_item2 = DB::table ('ticket_orders')
                    ->where ('ticket_orders.sale_item_id', $sale_item_id)
                    ->get ();
            }elseif ($item_detail->item=="Visa"){
                $sales_item2 = DB::table ('visa_orders')
                    ->where ('visa_orders.sale_item_id', $sale_item_id)
                    ->get ();
            }elseif ($item_detail->item=="Guest House"){
                $sales_item2 = DB::table ('guest_house_orders')
                    ->where ('guest_house_orders.sale_item_id', $sale_item_id)
                    ->get ();
            }elseif ($item_detail->item=="Other Item"){
                $sales_item2 = DB::table ('sale_order_items')
                    ->where ('sale_order_items.id', $sale_item_id)
                    ->get ();
            }


            //dd($sales_item2);
            $data['sales_item'] = $sales_item2;
            $data['item_type'] = $item_detail->item;

            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => $sales_item2,'item_type'=>$item_detail->item, 'sale_id' => $sale_item_id]);

        } catch (\Exception $e) {

            //    DB::rollBack();
            //throw $e;
            return response ()->json (['success' => 'UN SUCCESSFUL', 'item' => 'error' . $e, 'barcode' => $e]);

        }

    }

    public function updateItem(Request $request)
    {
        DB::beginTransaction ();

        try {

            $input = $request->all ();

            $sale_item_id = $request->item_id;

            $airline = $request->airline;
            $ticket_number = $request->ticket_number;
            $po_number = $request->po_number;
            $flight_date = $request->flight_date;
            $return_date = $request->return_date;
            $ticket_status = $request->ticket_status;
            $remark = $request->remark;
            $sector = $request->sector;
            $travel_agency = $request->travel_agency;
            $invoice_num = $request->invoice_num;
            $purchase_date = $request->purchase_date;


            DB::table('tickets')
                ->where('sale_item_id', $sale_item_id )
                ->update(['airline'=>$airline,
                    'travel_agency'=>$travel_agency,
                    'sector'=>$sector,
                    'flight_date'=>$flight_date,
                    'return_date'=>$return_date,
                    'po_number'=>$po_number,
                    'ticket_number'=>$ticket_number,
                    'invoice_num'=>$invoice_num,
                    'purchase_date'=>$purchase_date,
                    'ticket_status'=>$ticket_status,
                    'remark'=>$remark
                ]);


            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => $sale_item_id, 'unit_price' => 10]);

        } catch (\Exception $e) {

            return response ()->json (['success' => 'UN SUCCESSFUL' . $e, 'item' => 'error' . $e, 'sale_id' => $e]);

        }
    }

    public function updateItemOrder(Request $request)
    {
        DB::beginTransaction ();

        try {

            $input = $request->all ();

            $sale_item_id = $request->item_id;

            $airline = $request->airline;
//            $ticket_number = $request->ticket_number;
            $po_number = $request->po_number;
            $flight_date = $request->flight_date;
            $return_date = $request->return_date;
            $ticket_status = $request->ticket_status;
            $remark = $request->remark;
            $sector = $request->sector;
            $currency = $request->currency;
            $travel_agency = $request->travel_agency;
            $booking_number = $request->booking_number;
            $po_number = $request->po_number;
            $purchase_date = $request->purchase_date;


            DB::table('ticket_orders')
                ->where('sale_item_id', $sale_item_id )
                ->update(['airline'=>$airline,
                    'travel_agency'=>$travel_agency,
                    'sector'=>$sector,
                    'flight_date'=>$flight_date,
                    'return_date'=>$return_date,
                    'ticket_status'=>$ticket_status,
                    'currency'=>$currency,
                    'booking_number'=>$booking_number,
                    'po_number'=>$po_number,
                    'purchase_date'=>$purchase_date,
                    'remark'=>$remark
                ]);


            DB::table('sale_order_items')
                ->where('id', $sale_item_id )
                ->update(['description'=>$sector
                ]);

            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => $sale_item_id, 'unit_price' => 10]);

        } catch (\Exception $e) {

            return response ()->json (['success' => 'UN SUCCESSFUL' . $e, 'item' => 'error' . $e, 'sale_id' => $e]);

        }
    }

    public function updateItemOrderVisa(Request $request)
    {
        DB::beginTransaction ();

        try {

            $input = $request->all ();

            $sale_item_id = $request->item_id;


            $visa_supplier = $request->visa_supplier;
            $visa_period = $request->visa_period;
            $visa_date_of_issue = $request->visa_date_of_issue;
            $visa_valid_until = $request->visa_valid_until;
            $visa_currency = $request->visa_currency;
            $visa_status = $request->visa_status;
            $remark = $request->visa_remark;


            DB::table('visa_orders')
                ->where('sale_item_id', $sale_item_id )
                ->update(['visa_supplier'=>$visa_supplier,
                    'visa_period'=>$visa_period,
                    'visa_date_of_issue'=>$visa_date_of_issue,
                    'visa_valid_until'=>$visa_valid_until,
                    'currency'=>$visa_currency,
                    'visa_status'=>$visa_status,
                    'remark'=>$remark
                ]);


            DB::table('sale_order_items')
                ->where('id', $sale_item_id )
                ->update(['description'=>$visa_period
                ]);

            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => $sale_item_id, 'unit_price' => 10]);

        } catch (\Exception $e) {

            return response ()->json (['success' => 'UN SUCCESSFUL' . $e, 'item' => 'error' . $e, 'sale_id' => $e]);

        }
    }

    public function updateItemVisa(Request $request)
    {
        DB::beginTransaction ();

        try {

            $input = $request->all ();

            $sale_item_id = $request->item_id;


            $visa_supplier = $request->visa_supplier;
            $visa_period = $request->visa_period;
            $visa_date_of_issue = $request->visa_date_of_issue;
            $visa_valid_until = $request->visa_valid_until;
            $visa_currency = $request->visa_currency;
            $visa_status = $request->visa_status;
            $remark = $request->visa_remark;
            $purchase_date = $request->visa_purchase_date;


            DB::table('visas')
                ->where('sale_item_id', $sale_item_id )
                ->update(['visa_supplier'=>$visa_supplier,
                    'visa_period'=>$visa_period,
                    'visa_date_of_issue'=>$visa_date_of_issue,
                    'visa_valid_until'=>$visa_valid_until,
                    'currency'=>$visa_currency,
                    'visa_status'=>$visa_status,
                    'remark'=>$remark,
                    'purchase_date'=>$purchase_date
                ]);


            DB::table('sale_order_items')
                ->where('id', $sale_item_id )
                ->update(['description'=>$visa_period
                ]);

            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => $sale_item_id, 'unit_price' => 10]);

        } catch (\Exception $e) {

            return response ()->json (['success' => 'UN SUCCESSFUL' . $e, 'item' => 'error' . $e, 'sale_id' => $e]);

        }
    }

    public function updateVisa(Request $request)
    {
        DB::beginTransaction ();

        try {

            $input = $request->all ();

            $sale_item_id = $request->item_id;


            $flight_date = $request->flight_date;
            $return_date = $request->return_date;
            $ticket_status = $request->ticket_status;
            $remark = $request->remark;
//            $sector = $request->sector;
//            $travel_agency = $request->travel_agency;


            DB::table('visas')
                ->where('id', 448 )
                ->update(['airline'=>3,
                    'travel_agency'=>$travel_agency,
                    'sector'=>$sector,
                    'flight_date'=>$flight_date,
                    'return_date'=>$return_date,
                    'purchase_date'=>$purchase_date
                ]);


            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => $sale_item_id, 'unit_price' => 10]);

        } catch (\Exception $e) {

            return response ()->json (['success' => 'UN SUCCESSFUL' . $e, 'item' => 'error' . $e, 'sale_id' => $e]);

        }
    }

    public function showCustomerHistory($customer_id)
    {
        $data = [];

        $sales_item2 = DB::table ('sale_items')
            ->where ('sale_items.customer_id', $customer_id)->get();

        $data['sales_item'] = $sales_item2;
        return view ('sales/customer_history', $data);
    }

    public function showOrder($sales_id)
    {
        $data = [];

        $data['customers'] = $this->customer->all ();

        $data['airlines'] = DB::table ('airlines')
            ->get ();
        $data['suppliers'] = DB::table ('suppliers')
            ->get ();
        $data['visa_suppliers'] = DB::table ('visa_suppliers')
            ->get ();

        $data['places'] = DB::table ('places')
            ->get ();

        $data['rooms'] = DB::table ('rooms')
            ->get ();

        $data['items'] = DB::table ('items')
            ->get ();


        $data['modify'] = 1;
        $sales_date = $this->sale_order->find ($sales_id);
        $d = $sales_date->date;
        $date_converted = date ("d/m/Y", strtotime ($d));

        $data['sales'] = DB::table ('customers')
            ->join ('sale_orders', 'customers.id', '=', 'sale_orders.customer')
            ->where ('sale_orders.id', $sales_id)->first ();

        $data['sale_id'] = $sales_id;

        $sales_item2 = DB::table ('sale_order_items')
            ->where ('sale_order_items.sale_id', $sales_id)
            ->get ();

        //dd($sales_item2);
        $data['sales_item'] = $sales_item2;
        return view ('sales/order_detail', $data)->with ('conv_date', $date_converted);
    }

    public function voidSale(Request $request)
    {
        DB::beginTransaction ();

        try {

            $input = $request->all ();

            $sale_id = $request->sale_id;


            DB::table('sales')
                ->where('id', $sale_id )
                ->update(['status'=>'Void'
                ]);

            DB::table('sale_items')
                ->where('sale_id', $sale_id )
                ->update(['status'=>'Void'
                ]);

            DB::commit ();

//            $this->show($sale_id);
            //$this->show ()
            //$this->show ($sale_id);


            $data = [];

            $data['customers'] = $this->customer->all ();

            $data['airlines'] = DB::table ('airlines')
                ->get ();
            $data['suppliers'] = DB::table ('suppliers')
                ->get ();
            $data['visa_suppliers'] = DB::table ('visa_suppliers')
                ->get ();

            $data['places'] = DB::table ('places')
                ->get ();

            $data['rooms'] = DB::table ('rooms')
                ->get ();

            $data['items'] = DB::table ('items')
                ->get ();


            $data['modify'] = 1;
            $sales_date = $this->sale->find ($sale_id);
            $d = $sales_date->date;
            $date_converted = date ("d/m/Y", strtotime ($d));

            $data['sales'] = DB::table ('customers')
                ->join ('sales', 'customers.id', '=', 'sales.customer')
                ->where ('sales.id', $sale_id)->first ();

            $data['sale_id'] = $sale_id;

            $sales_item2 = DB::table ('sale_items')
                ->where ('sale_items.sale_id', $sale_id)
                ->get ();

            //dd($sales_item2);
            $data['sales_item'] = $sales_item2;
            return view ('sales/detail', $data)->with ('conv_date', $date_converted);


        } catch (\Exception $e) {

            return response ()->json (['success' => 'UN SUCCESSFUL' . $e, 'item' => 'error' . $e, 'sale_id' => $e]);

        }
    }

    public function voidSaleOrder(Request $request)
    {
        DB::beginTransaction ();

        // $sale_id = $request->sale_id;

        // dd($sale_id);

        try {

            $input = $request->all ();

            $sale_id = $request->sale_id;


            $sales_id =$sale_id;

            DB::table('sale_orders')
                ->where('id', $sale_id )
                ->update(['status'=>'Void'
                ]);

            DB::table('sale_order_items')
                ->where('sale_id', $sale_id )
                ->update(['status'=>'Void'
                ]);

            DB::commit ();

            $data = [];

            $data['customers'] = $this->customer->all ();

            $data['airlines'] = DB::table ('airlines')
                ->get ();
            $data['suppliers'] = DB::table ('suppliers')
                ->get ();
            $data['visa_suppliers'] = DB::table ('visa_suppliers')
                ->get ();

            $data['places'] = DB::table ('places')
                ->get ();

            $data['rooms'] = DB::table ('rooms')
                ->get ();

            $data['items'] = DB::table ('items')
                ->get ();


            $data['modify'] = 1;
            $sales_date = $this->sale_order->find ($sales_id);
            $d = $sales_date->date;
            $date_converted = date ("d/m/Y", strtotime ($d));

            $data['sales'] = DB::table ('customers')
                ->join ('sale_orders', 'customers.id', '=', 'sale_orders.customer')
                ->where ('sale_orders.id', $sales_id)->first ();

            $data['sale_id'] = $sales_id;

            $sales_item2 = DB::table ('sale_order_items')
                ->where ('sale_order_items.sale_id', $sales_id)
                ->get ();

            //dd($sales_item2);
            $data['sales_item'] = $sales_item2;
            return view ('sales/order_detail', $data)->with ('conv_date', $date_converted);



        } catch (\Exception $e) {

            dd($e);
//            return response ()->json (['success' => 'UN SUCCESSFUL' . $e, 'item' => 'error' . $e, 'sale_id' => $e]);

        }
    }

    public function profit_loss(Request $request)
    {
        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');

        //dd($start_date);

        if (isset($start_date) && isset($end_date)) {


            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));


            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            $data['cost_profits'] = DB::table ('sale_items')
                ->join ('sales','sales.id','=','sale_items.sale_id')
                ->join ('customers','customers.id','=','sales.customer')
                ->join ('companys','companys.id','=','sale_items.company')
                ->select ('customers.*','companys.company_name','sale_items.*')
                ->whereBetween ('sale_items.date', [$start_date, $end_date])
                ->get ();

            //dd($data['sale_items']);

            if (isset($print)) {
                $start_date = $this->change_date_to_standard ($request->input ('start_date'));
                $end_date = $this->change_date_to_standard ($request->input ('end_date'));

                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));


                $data['cost_profits'] = DB::table ('sale_items')
                    ->join ('sales','sales.id','=','sale_items.sale_id')
                    ->join ('customers','customers.id','=','sales.customer')
                    ->join ('companys','companys.id','=','sale_items.company')
                    ->select ('customers.*','companys.company_name','sale_items.*')
                    ->whereBetween ('sale_items.date', [$start_date, $end_date])
                    ->get ();
                //dd( $data['cost_profits'] );

                //dd($data['cost_profits']);

                $pdf = PDF::loadView ('reports/cost_profit_report', $data);
                $pdf->setPaper('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');

            } else {
                return view ('reports/profit_loss_index', $data);
            }

        }
        return view ('reports/profit_loss_index', $data);
    }

    public function profit_loss_tickets(Request $request)
    {
        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');

        //dd($start_date);

        if (isset($start_date) && isset($end_date)) {


            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));


            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            $data['cost_profits'] = DB::table ('tickets')
                ->join ('customers','customers.id','=','tickets.customer_id')
                ->select ('customers.*','tickets.*')
                ->whereBetween ('purchase_date', [$start_date, $end_date])
                ->get ();

            //dd($data['sale_items']);

            if (isset($print)) {
                $start_date = $this->change_date_to_standard ($request->input ('start_date'));
                $end_date = $this->change_date_to_standard ($request->input ('end_date'));

                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));


                $data['cost_profits'] = DB::table ('tickets')
                    ->join ('customers','customers.id','=','tickets.customer_id')
                    ->select ('customers.*','tickets.*')
                    ->whereBetween ('purchase_date', [$start_date, $end_date])
                    ->get ();
                //dd( $data['cost_profits'] );

                //dd($data['cost_profits']);

                $pdf = PDF::loadView ('reports/tickets_report_print', $data);
                $pdf->setPaper('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');

            } else {
                return view ('reports/ticket_report_list', $data);
            }

        }
        return view ('reports/profit_loss_index', $data);
    }

    public function profit_loss_visas(Request $request)
    {
        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');

        //dd($start_date);

        if (isset($start_date) && isset($end_date)) {


            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));


            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            $data['cost_profits'] = DB::table ('visas')
                ->join ('customers','customers.id','=','visas.customer_id')
                ->join ('companys','companys.id','=','visas.company')
                ->select ('customers.*','visas.*')
                ->whereBetween ('purchase_date', [$start_date, $end_date])
                ->get ();

            //dd($data['sale_items']);

            if (isset($print)) {
                $start_date = $this->change_date_to_standard ($request->input ('start_date'));
                $end_date = $this->change_date_to_standard ($request->input ('end_date'));

                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));


                $data['cost_profits'] = DB::table ('visas')
                    ->join ('customers','customers.id','=','visas.customer_id')
                    ->join ('companys','companys.id','=','visas.company')
                    ->select ('customers.*','visas.*')
                    ->whereBetween ('purchase_date', [$start_date, $end_date])
                    ->get ();

                //dd( $data['cost_profits'] );
                //dd($data['cost_profits']);

                $pdf = PDF::loadView ('reports/visas_report_print', $data);
                $pdf->setPaper('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');

            } else {
                return view ('reports/visas_report_list', $data);
            }

        }
        return view ('reports/profit_loss_index', $data);
    }

    public function profit_loss_guest_houses(Request $request)
    {
        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');

        //dd($start_date);

        if (isset($start_date) && isset($end_date)) {


            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));


            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            $data['cost_profits'] = DB::table ('guest_houses')
                ->join ('customers','customers.id','=','guest_houses.customer')
                ->join ('companys','companys.id','=','guest_houses.company')
                ->join ('places','places.id','=','guest_houses.place')
                ->join ('rooms','rooms.id','=','guest_houses.room_no')
                ->select ('customers.*','places.place_name','rooms.room_name','guest_houses.*')
                ->whereBetween ('purchase_date', [$start_date, $end_date])
                ->get ();

            //dd($data['sale_items']);

            if (isset($print)) {
                $start_date = $this->change_date_to_standard ($request->input ('start_date'));
                $end_date = $this->change_date_to_standard ($request->input ('end_date'));

                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));


                $data['cost_profits'] = DB::table ('guest_houses')
                    ->join ('customers','customers.id','=','guest_houses.customer')
                    ->join ('companys','companys.id','=','guest_houses.company')
                    ->join ('places','places.id','=','guest_houses.place')
                    ->join ('rooms','rooms.id','=','guest_houses.room_no')
                    ->select ('customers.*','rooms.room_name','places.place_name','guest_houses.*')
                    ->whereBetween ('purchase_date', [$start_date, $end_date])
                    ->get ();

                //dd( $data['cost_profits'] );
                //dd($data['cost_profits']);

                $pdf = PDF::loadView ('reports/guest_houses_report_print', $data);
                $pdf->setPaper('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');

            } else {
                return view ('reports/guest_houses_report_list', $data);
            }

        }
        return view ('reports/profit_loss_index', $data);
    }

    public function change_date_to_standard($date)
    {
        $date = str_replace ('/', '-', $date);
        $array = explode ('-', $date);
        $year = $array[2];
        $month = $array[1];
        $day = $array[0];
        $date = join ('-', [$year, $month, $day]);
        $date = date ("Y-m-d", strtotime ($date));
        return $date;

    }

    public function tickets_index()
    {
        $data = [];
        $data['tickets'] = DB::table ('customers')
            ->join ('tickets', 'customers.id', '=', 'tickets.customer_id')
            ->get ();
        return view ('tickets/index', $data);
    }

    public function profit_loss_expenses(Request $request)
    {
        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');

        //dd($start_date);

        if (isset($start_date) && isset($end_date)) {


            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));


            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            $data['cost_profits'] = DB::table ('expenses')
                ->whereBetween ('date', [$start_date, $end_date])
                ->get ();

            //dd($data['sale_items']);

            if (isset($print)) {
                $start_date = $this->change_date_to_standard ($request->input ('start_date'));
                $end_date = $this->change_date_to_standard ($request->input ('end_date'));

                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));


                $data['cost_profits'] = DB::table ('expenses')
                    ->whereBetween ('date', [$start_date, $end_date])
                    ->get ();
                //dd( $data['cost_profits'] );

                //dd($data['cost_profits']);

                $pdf = PDF::loadView ('reports/expenses_report_print', $data);
                $pdf->setPaper('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');

            } else {
                return view ('reports/expenses_report_list', $data);
            }

        }
        return view ('reports/expenses_report_list', $data);
    }


}
