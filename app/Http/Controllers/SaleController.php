<?php


namespace App\Http\Controllers;

use App\OtherItem;
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
use App\BankAccount;
use App\Receipt;
use App\ReceiptItem;


use PdfReport;
use PDF;


use Illuminate\Support\Facades\DB;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Carbon;


class SaleController extends Controller
{

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

    public function credit_index()
    {
        $data = [];
        $data['sales'] = DB::table ('customers')
            ->join ('sales', 'customers.id', '=', 'sales.customer')
            ->where ('sales.status','=','unpaid')
            ->get ();
        return view ('sales/credit_sales_index', $data);
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
            ->where ('sale_orders.status', '=', 'active')
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

        $data['accounts'] = BankAccount::where ('status', 'active')->get ();

        $data['companys'] = DB::table ('companys')
            ->get ();


        return view ('sales/new_sale', $data);
    }

    public function createSaleCustomer(Request $request, $customer_id)
    {
        $data = [];

        $data['customers'] = DB::table ('customers')
            ->get ();

        $data['customer'] = DB::table ('customers')
            ->where ("id", "=", $customer_id)
            ->first ();

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
        $data['accounts'] = BankAccount::where ('status', 'active')->get ();

        $data['companys'] = DB::table ('companys')
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
        $data['accounts'] = BankAccount::where ('status', 'active')->get ();

        $data['companys'] = DB::table ('companys')
            ->get ();

        return view ('sales/new_sale_order_angular', $data);
    }

    public function createSaleOrderCustomer(Request $request, $customer_id)
    {
        $data = [];

        $data['customers'] = $this->customer->all ();

        $data['customer'] = DB::table ('customers')
            ->where ("id", "=", $customer_id)
            ->first ();

//        dd($data['customer']);

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

        $data['accounts'] = BankAccount::where ('status', 'active')->get ();

        $data['companys'] = DB::table ('companys')
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

        $data['accounts'] = BankAccount::where ('status', 'active')->get ();

        $data['companys'] = DB::table ('companys')
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
            ->where ('status','=','active')
            ->get ();

        //dd($sales_item2);
        $data['sales_item'] = $sales_item2;


        return view ('sales/new_sale_from_order', $data);
    }

    public function createSaleAngular(Request $request, Sale $sale)
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

        $data['accounts'] = BankAccount::where ('status', 'active')->get ();

        $data['companys'] = DB::table ('companys')
            ->get ();

        // Prepared by

        return view ('sales/new_sale_angular', $data);
    }

    public function newAgencySale(Request $request, Sale $sale)
    {
        DB::beginTransaction ();

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
            $sale->company = $request->company;

//            if ($payment_id == "Cash") {
//                $sale->status = "Paid";
//            }
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
            $ticket_type_list = $request->ticket_type_list;
            $departure_time_list = $request->departure_time_list;
            $arrival_time_list = $request->arrival_time_list;
            $departure_time_return_list = $request->departure_time_return_list;
            $arrival_time_return_list = $request->arrival_time_return_list;
            $ticket_refund_list = $request->ticket_refund_list;


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
                $saleItem->tax = $service_tax_list[$i];
                $saleItem->item_type = $item_type_list[$i];
                if ($saleItem->item_type == "Ticket") {
                    $saleItem->gross = $gross_list[$i];
                } else {
                    $saleItem->gross = $gross_list[$i];
                }
                $total_amount += $saleItem->sub_total;
                $saleItem->company = session ()->get ('company');
                $saleItem->sale ()->associate ($sale);
                $saleItem->save ();

                if ($currency_list[$i] == "USD") {
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
                    $newTicket->company = session ()->get ('company');
                    $newTicket->departure_time = $departure_time_list[$i];
                    $newTicket->refund_amount = $ticket_refund_list[$i];
                    $newTicket->ticket_type = $ticket_type_list[$i];
                    $newTicket->arrival_time = $arrival_time_list[$i];
                    $newTicket->departure_time_return = $departure_time_return_list[$i];
                    $newTicket->arrival_time_return = $arrival_time_return_list[$i];
                    $newTicket->sale_id = $sale->id;


//                    $arrival_time_list = $request->arrival_time_list;
//                    $departure_time_return_list = $request->departure_time_return_list;
//                    $arrival_time_return_list = $request->arrival_time_return_list;

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
                    $newVisa->company = session ()->get ('company');
                    $newVisa->sale_id = $sale->id;

                    $newVisa->save ();


                }

                if ($saleItem->item_type == "Guest House") {
                    $GuestHouse = new GuestHouse();
                    $GuestHouse->customer = $customer_id;
                    $GuestHouse->room_no = $room_no_list[$i];
                    $GuestHouse->place = $place_list[$i];
                    $GuestHouse->currency = $currency_list[$i];
                    $GuestHouse->unit_price = $unit_price_list[$i];
                    $GuestHouse->gross = $gross_list[$i];
                    $GuestHouse->from_date = $visa_date_of_issue_list[$i];
                    $GuestHouse->to_date = $visa_valid_until_list[$i];
                    $GuestHouse->sale_id = $sale->id;
                    $GuestHouse->sale_item_id = $saleItem->id;
                    $GuestHouse->purchase_date = $date_sale;
                    $GuestHouse->company = session ()->get ('company');

                    $GuestHouse->save ();

//                    if ($currency_list[$i]=="USD") {
//                        $total_amount_usd += $unit_price_list[$i];
//                    } else {
//                        $total_amount_ern += $unit_price_list[$i];
//                    }
                }

                if (count ($sale_order_item_list) > 0) {

                    // Update table sale order items

                    //
                }

            }

            $sale->total_amount_ern = $total_amount_ern;
            $sale->total_amount_usd = $total_amount_usd;


            $customer_data = DB::table ('customers')
                ->where ('id', $customer_id)
                ->first ();

            $customer_balance_usd = $customer_data->balance_usd;
            $customer_balance_ern = $customer_data->balance_ern;

            if ($payment_id == "Cash") {

                $sale->status = "Paid";

                $sale->amount_paid_ern = $total_amount_ern;
                $sale->amount_paid_usd = $total_amount_usd;

            } else if ($payment_id == "Credit") {

                $customer_balance_usd += $total_amount_usd;
                $customer_balance_ern += $total_amount_ern;

                DB::table ('customers')
                    ->where ('id', $customer_id)
                    ->update (['balance_usd' => $customer_balance_usd,
                        'balance_ern' => $customer_balance_ern
                    ]);
            }

            // Insert into payment


            $sale->save ();

            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => $departure_time_return_list[1],
                'unit_price' => $departure_time_return_list[1]]);

        } catch (\Exception $e) {

            return response ()->json (['success' => 'UN SUCCESSFUL' . $e, 'item' => 'error' . $e, 'unit_price' => $e]);

        }
    }

    public function newAgencySaleAngular(Request $request, Sale $sale)
    {

        DB::beginTransaction ();

        $input = $request->all ();

        try {

            $input = $request->all ();

            $total_amount_usd = 0.00;
            $total_amount_ern = 0.00;


            $sale = new Sale();

            $sale->customer = $request->customer_id;
            $sale->ref = $request->ref_num;  //????
            $sale->date = $request->sale_date;
            $sale->total_amount_ern = 0.00;
            $sale->total_amount_usd = 0.00;
            $sale->user = session ()->get ('user');
            $sale->user_name = 'Simon';
            $sale->payment_mode = $request->payment_id;
            $sale->remark = $request->sale_remark;
            $sale->company = $request->company;
            $sale->ref = $request->ref_num;

            $sale->user_id = session ()->get ('user');
            $sale->save ();


            $count_items = count ($request->cars);
            $counter_items = 0;

            $all_sales = $request->cars;

            for ($i = 0; $i < $count_items; $i++) {

                $car = $all_sales[$i];

                $counter_items++;

                $saleItem = new SaleItem();
                $saleItem->item = $car['item'];
                $saleItem->qty = $car['qty'];

                if($car['sale_order_item_id']!=null || $car['sale_order_item_id']!=""){
                    $saleItem->sale_order_item_id = $car['sale_order_item_id'];
                }

                $saleItem->prepared_by = $car['prepared_by'];

                // ???? Check the dates
                if ($car['item'] == "Guest House") {
                    $saleItem->date = $sale->date;
                    $saleItem->unit_price = $car['unit_price'];
                }
                else if ($car['item'] == "Other Item") {
                    // do nothing
                    $saleItem->date = $sale->date;
                    $saleItem->item_id = $car['item_id'];
                }
                else {
                    $carbon_date = new Carbon($car['purchase_date']);
//                    $carbon_date = $carbon_date->addDays (1);
                    $saleItem->date = $carbon_date->toDateString ();
                }

                $saleItem->customer_id = $request->customer_id;

                $carbon_date = new Carbon($request->sale_date);
 //               $carbon_date = $carbon_date->addDays (1);
                $saleItem->date = $carbon_date->toDateString ();

//                $saleItem->date= $sale_request['sale_date'];

                // ???? Check Sale Items details
                $saleItem->currency = $car['currency'];
                $saleItem->unit_price = $car['selling_price'];
                $saleItem->discount = $car['discount'];
                $saleItem->sub_total = $car['sub_total'];//($saleItem->qty * $saleItem->unit_price) - $saleItem->discount;
                $saleItem->tax = $car['service_tax'];
                $saleItem->item_type = $car['item'];

                if ($car['item'] == "Guest House") {
                    $saleItem->unit_price = $car['unit_price'];
                }


                if(isset($car['remark'])){
                    $saleItem->remark = $car['remark'];
                }else{
                    $saleItem->remark = "";
                }

                $saleItem->company = $request->company;
                $saleItem->user_id = session ()->get ('user');

                if ($saleItem->item_type == "Ticket") {
                    $saleItem->gross = $car['gross'];
                    $saleItem->unit_cost = $car['airline_payed'];
                    $saleItem->taxable_gross = $car['taxable_gross'];
                }
                else if ($saleItem->item_type == "Guest House") {
                    $saleItem->gross = $car['sub_total'];
                    $saleItem->unit_cost = $car['unit_cost'];
                    $saleItem->unit_price = $car['unit_price'];
                    $saleItem->taxable_gross = $car['sub_total'];
                } else {
                    // Other Items
                    $saleItem->gross = $car['gross'];
                    $saleItem->unit_cost = $car['unit_cost'];
                    $saleItem->taxable_gross = 0.00;
                }

                //$total_amount += $saleItem->sub_total;
                $saleItem->company = $request->company;
                $saleItem->sale ()->associate ($sale);
                $saleItem->save ();


                if ($car['item'] == "Ticket") {

                    $newTicket = new Ticket();

                    $newTicket->customer_id = $request->customer_id;
                    $newTicket->airline = $car['airline'];

                    if(isset($car['travel_agency'])){
                        $newTicket->travel_agency = $car['travel_agency'];
                    }else{
                        $newTicket->travel_agency = "";
                    }

                    $newTicket->commision_amount = $car['commision'];
                    $newTicket->selling_price = $car['selling_price'];

                    // ???? Check date both on system and on the ground

                    $carbon_date = new Carbon($car['purchase_date']);
                    $carbon_date = $carbon_date->addDays (1);
                    $newTicket->purchase_date = $carbon_date->toDateString ();

                    $carbon_date = new Carbon($car['flight_date']);
                    $carbon_date = $carbon_date->addDays (1);
                    $newTicket->flight_date = $carbon_date->toDateString ();

                    $carbon_date = new Carbon($car['return_date']);
                    $carbon_date = $carbon_date->addDays (1);
                    $newTicket->return_date = $carbon_date->toDateString ();

                    $newTicket->ticket_number = $car['ticket_number'];
                    $newTicket->po_number = $car['po_number'];
                    $newTicket->invoice_num = $car['invoice_num'];
                    $newTicket->sector = strtoupper ($car['sector']);

                    $newTicket->ticket_status = $car['ticket_status'];

                    if(isset($car['remark'])){
                        $newTicket->remark = $car['remark'];
                    }else{
                        $newTicket->remark = "";
                    }

                    $newTicket->ticket_type = $car['ticket_type'];

                    $newTicket->currency = $car['currency'];
                    $newTicket->base_fare = $car['base_fare'];
                    $newTicket->tax = $car['tax'];
                    $newTicket->total_fare = $car['unit_price'];
                    $newTicket->commision_amount = $car['commision'];
                    $newTicket->airline_payed = $car['airline_payed'];
                    $newTicket->selling_price = $car['selling_price'];
                    $newTicket->addition = $car['addition'];
                    $newTicket->discount = $car['discount'];
                    $newTicket->gross = $car['gross'];
                    $newTicket->inland_tax = $car['service_tax'];
                    $newTicket->net_commission = $car['net_profit'];
                    $newTicket->refund_amount = $car['ticket_refund'];
                    $newTicket->sale_item_id = $saleItem->id;
                    $newTicket->sale_id = $sale->id;
                    $newTicket->company = $request->company;

                    $newTicket->taxation_from = $car['taxation_from'];
                    $newTicket->taxable_gross = $car['taxable_gross'];
                    $newTicket->paid_through = $car['paid_through'];

                    $newTicket->departure_time = $car['departure_time'];
                    $newTicket->arrival_time = $car['arrival_time'];
                    $newTicket->departure_time_return = $car['departure_time_return'];
                    $newTicket->arrival_time_return = $car['arrival_time_return'];
                    $newTicket->prepared_by = $car['prepared_by'];
                    $newTicket->user_id = session ()->get ('user');

                    $newTicket->save ();

                    if($car['sale_order_item_id']!=null || $car['sale_order_item_id']!=""){

                        DB::table ('sale_order_items')
                            ->where ('id', $car['sale_order_item_id'])
                            ->update (['status' => 'Sold'
                            ]);

                        DB::table ('ticket_orders')
                            ->where ('sale_item_id', $car['sale_order_item_id'])
                            ->update (['ticket_status' => 'Sold'
                            ]);

                        $order_data = DB::table ('sale_order_items')
                            ->where ('id', $car['sale_order_item_id'])
                            ->first();

                        $order_counts = DB::table ('sale_order_items')
                            ->where ('sale_id', $order_data->sale_id)
                            ->where ('status','=', 'active')
                            ->count();

                        if($order_counts==0)
                        {
                            DB::table ('sale_orders')
                                ->where ('id', $order_data->sale_id)
                                ->update (['status' => 'Sold'
                                ]);
                        }

                    }


                    $account_data = DB::table ('bank_accounts')
                        ->where ('id', "=", $car['paid_through'])
                        ->first ();


                    $account = BankAccount::where ('id', $car['paid_through'])->first ();


                    $new_balance_usd = 0.00;
                    $new_balance_ern = 0.00;

                    if ($car['currency'] == "USD") {

                        $new_balance_usd = $account->balance_usd;
                        $new_balance_usd -= $newTicket->airline_payed;

                        DB::table ('bank_accounts')
                            ->where ('id', $account->id)
                            ->update (['balance_usd' => $new_balance_usd
                            ]);

                    } else {

                        $new_balance_ern = $account->balance_ern;
                        $new_balance_ern -= $newTicket->airline_payed;

                        DB::table ('bank_accounts')
                            ->where ('id', $account->id)
                            ->update (['balance_ern' => $new_balance_ern
                            ]);

                    }

                }
                else if ($car['item'] == "Visa") {

                    // Visa Insertion
                    $newVisa = new Visa();
                    $newVisa->visa_supplier = $car['visa_supplier'];
                    $newVisa->customer_id = $request->customer_id;
                    $newVisa->visa_period = $car['visa_period'];

                    $carbon_date = new Carbon($car['visa_date_of_issue']);
                    $carbon_date = $carbon_date->addDays (1);
                    $newVisa->visa_date_of_issue = $carbon_date->toDateString ();

                    $carbon_date = new Carbon($car['visa_valid_until']);
                    $carbon_date = $carbon_date->addDays (1);
                    $newVisa->visa_valid_until = $carbon_date->toDateString ();

                    $newVisa->visa_status = $car['visa_status'];

                    $carbon_date = new Carbon($car['purchase_date']);
                    $carbon_date = $carbon_date->addDays (1);
                    $newVisa->purchase_date = $carbon_date->toDateString ();

                    $newVisa->currency = $car['currency'];

                    $newVisa->unit_price = $car['selling_price'];
                    $newVisa->unit_cost = $car['unit_cost'];

                    $newVisa->gross = $car['gross'];
                    $newVisa->remark = $car['remark'];

                    $newVisa->sale_item_id = $saleItem->id;
                    $newVisa->prepared_by = $car['prepared_by'];

                    $newVisa->company = $request->company;
                    $newVisa->prepared_by = $car['prepared_by'];

                    $newVisa->sale_id = $sale->id;

                    $newVisa->user_id = session ()->get ('user');

                    $newVisa->save ();


                    if($car['sale_order_item_id']!=null || $car['sale_order_item_id']!=""){

                        DB::table ('sale_order_items')
                            ->where ('id', $car['sale_order_item_id'])
                            ->update (['status' => 'Sold'
                            ]);

                        DB::table ('visa_orders')
                            ->where ('sale_item_id', $car['sale_order_item_id'])
                            ->update (['visa_status' => 'Sold'
                            ]);

                        $order_data = DB::table ('sale_order_items')
                            ->where ('id', $car['sale_order_item_id'])
                            ->first();

                        $order_counts = DB::table ('sale_order_items')
                            ->where ('sale_id', $order_data->sale_id)
                            ->where ('status','=', 'active')
                            ->count();

                        if($order_counts==0)
                        {
                            DB::table ('sale_orders')
                                ->where ('id', $order_data->sale_id)
                                ->update (['status' => 'Sold'
                                ]);
                        }
                    }


                }
                else if ($car['item'] == "Guest House") {

                    // Guest House Insertion
                    $GuestHouse = new GuestHouse();
                    $GuestHouse->room_no = $car['room_no'];
                    $GuestHouse->place = $car['place'];
                    $GuestHouse->currency = $car['currency'];;
                    $GuestHouse->unit_price = $car['unit_price'];
                    $GuestHouse->gross = $car['gross'];
                    $GuestHouse->qty = $car['qty'];
                    $GuestHouse->sub_total = $car['sub_total'];
                    $GuestHouse->discount = $car['discount'];
                    $GuestHouse->service_tax = $car['service_tax'];
                    $GuestHouse->net_profit = $car['net_profit'];
                    $GuestHouse->pricing_method = $car['pricing_way'];


                    $carbon_date = new Carbon($car['visa_date_of_issue']);
//                    $carbon_date = $carbon_date->addDays (1);

                    $GuestHouse->from_date = $carbon_date->toDateString ();

                    $carbon_date = new Carbon($car['visa_valid_until']);
//                    $carbon_date = $carbon_date->addDays (1);

                    $GuestHouse->to_date = $carbon_date->toDateString ();
                    $GuestHouse->sale_id = $sale->id;
                    $GuestHouse->sale_item_id = $saleItem->id;
                    $GuestHouse->purchase_date = $saleItem->date;
                    $GuestHouse->remark = $car['remark'];
                    $GuestHouse->customer = $request->customer_id;
                    $GuestHouse->prepared_by = $car['prepared_by'];
                    $GuestHouse->company = $request->company;
                    $GuestHouse->user_id = session ()->get ('user');

                    $GuestHouse->save ();


                    if($car['sale_order_item_id']!=null || $car['sale_order_item_id']!=""){

                        DB::table ('sale_order_items')
                            ->where ('id', $car['sale_order_item_id'])
                            ->update (['status' => 'Sold'
                            ]);

                        DB::table ('guest_house_orders')
                            ->where ('sale_item_id', $car['sale_order_item_id'])
                            ->update (['status' => 'Sold'
                            ]);

                        $order_data = DB::table ('sale_order_items')
                            ->where ('id', $car['sale_order_item_id'])
                            ->first();

                        $order_counts = DB::table ('sale_order_items')
                            ->where ('sale_id', $order_data->sale_id)
                            ->where ('status','=', 'active')
                            ->count();

                        if($order_counts==0)
                        {
                            DB::table ('sale_orders')
                                ->where ('id', $order_data->sale_id)
                                ->update (['status' => 'Sold'
                                ]);
                        }

                    }

                }
                else if ($car['item'] == "Other Item") {

                    // Other Items Insertion
                    $otherItem = new OtherItem();
                    $otherItem->unit_cost = $car['unit_cost'];
                    $otherItem->unit_price = $car['unit_price'];
                    $otherItem->item_id = $car['item_id'];
                    $otherItem->gross = $car['gross'];
                    $otherItem->user_id = session ()->get ('user');
                    $otherItem->save ();
                }
                $counter_items++;

                if ($car['currency'] == "USD") {
                    $total_amount_usd += $car['sub_total'];
                } else {
                    $total_amount_ern += $car['sub_total'];
                }


                // If it is originated from an order
                if($car['sale_order_item_id']!=null || $car['sale_order_item_id']!="")
                {

                    DB::table ('sale_order_items')
                        ->where ('id', $car['sale_order_item_id'])
                        ->update (['status' => 'Sold'
                        ]);

                    $order_data = DB::table ('sale_order_items')
                        ->where ('id', $car['sale_order_item_id'])
                        ->first();

                    $order_counts = DB::table ('sale_order_items')
                        ->where ('sale_id', $order_data->sale_id)
                        ->where ('status','=', 'active')
                        ->count();

                    if($order_counts==0)
                    {
                        DB::table ('sale_orders')
                            ->where ('id', $order_data->sale_id)
                            ->update (['status' => 'Sold'
                            ]);
                    }
                }
            }

            $sale->total_amount_ern = $total_amount_ern;
            $sale->total_amount_usd = $total_amount_usd;
            $sale->save ();

            if($request->payment_id == "Cash"){

                $customer=$request->customer_id;
                $amount_ern=$total_amount_ern;
                $amount_usd=$total_amount_usd;
                $mode=$request->mode;
                $received_by= $request->received_by;
                $date=$sale->date;
                $remark=$sale->remark;
                $paid_amount_usd=$total_amount_usd;
                $paid_amount_ern=$total_amount_ern;
                $sales_id=$sale->id;
                $company=$sale->company;

                $receipt=Receipt::create([
                    'name'=>"",
                    'customer'=>$customer,
                    'date'=>$date,
                    'reference'=>$request->ref_num,
                    'total_amount_usd'=>$amount_usd,
                    'total_amount_ern'=>$amount_ern,
                    'received_by'=>$received_by,
                    'mode'=>$mode,
                    'remark'=>$remark,
                    'company'=>$company,
                    'reference'=>$sale->ref,
                'user_id'=>session ()->get ('user')
                ]);

                ReceiptItem::create([
                    'receipt_id'=>$receipt->id,
                    'sales_id'=>$sales_id,
                    'amount_ern'=>$total_amount_ern,
                    'amount_usd'=>$total_amount_usd,
                    'user_id'=>session ()->get ('user')
                ]);


                // Update Sale Details
                $sale->status = "paid";
                $sale->payment_status = "paid";
                $sale->amount_paid_usd =$total_amount_usd;
                $sale->amount_paid_ern =$total_amount_ern;

                $sale->save();


            }else if($request->payment_id == "Credit"){

                $customer_data = DB::table ('customers')
                    ->where ('id', $request->customer_id)
                    ->first ();

                $customer_balance_usd = $customer_data->balance_usd;
                $customer_balance_ern = $customer_data->balance_ern;

                // Increase Customer Balance

                $customer_balance_usd+=$total_amount_usd;
                $customer_balance_ern+=$total_amount_ern;

                DB::table('customers')
                    ->where('id', $request->customer_id )
                    ->update(['balance_usd'=>$customer_balance_usd,
                        'balance_ern'=>$customer_balance_ern
                    ]);

            }


            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => '',
                'unit_price' =>'']);

        } catch (\Exception $e) {

            DB::rollback();
            return response ()->json (['success' => 'UN SUCCESSFUL', 'item' => 'error' . $e, 'unit_price' => $e]);

        }

    }

    public function newAgencySaleOrder(Request $request, Sale $sale)
    {

        DB::beginTransaction ();

        try {
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
            $booking_expire_list = $request->booking_expire_list;


            $items_count = count ($item_list);
            $count = count ($item_list);

            $total_amount = 0.00;

            // Save Sale
            $sale_order = new SaleOrder();

            $sale_order->customer = $customer_id;
            $sale_order->ref = "";
            $sale_order->date = $date_sale;
            $sale_order->total_amount = 0.00;
            $sale_order->user = $customer_id;
            $sale_order->user_name = 'Simon';
            $sale_order->payment_mode = $payment_id;
            $sale_order->company = $request->company;
            $sale_order->remark = $sale_remark;
            $sale_order->user_id = session ()->get ('user');
            $sale_order->save ();


            for ($i = 1; $i < $count; $i++) {

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
                $saleItem->company = $request->company;
                $saleItem->user_id = session ()->get ('user');

                if (count ($item_id_list) > 0) {
                    if ($item_id_list[$i] == '' || $item_id_list[$i] == null) {
                    } else {
                        $saleItem->item_id = $item_id_list[$i];
                    }
                }
                $total_amount += 0.00;//$saleItem->sub_total;
                $saleItem->sale ()->associate ($sale_order);
                $saleItem->save ();

                if ($saleItem->item_type == "Ticket") {

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
                    $newTicket->sale_item_id = $saleItem->id;
                    $newTicket->booking_expire_date = $booking_expire_list[$i];
                    $newTicket->sale_id = $sale_order->id;
                    $newTicket->company = $request->company;
                    $newTicket->user_id = session ()->get ('user');
                    $newTicket->save ();
                }

                if ($saleItem->item_type == "Visa") {
                    $newVisa = new VisaOrder();
                    $newVisa->visa_supplier = $visa_supplier_list[$i];
                    //$newTicket->sector = $sector_list[$i];
                    $newVisa->visa_period = $visa_period_list[$i];
                    $newVisa->visa_date_of_issue = $visa_date_of_issue_list[$i];
                    $newVisa->visa_valid_until = $visa_valid_until_list[$i];
                    $newVisa->visa_status = $visa_status_list[$i];
                    $newVisa->remark = $remark_list[$i];
                    $newVisa->sale_item_id = $saleItem->id;
                    $newVisa->company = $request->company;
                    $newVisa->currency = $currency_list[$i];
                    $newVisa->sale_id = $sale_order->id;
                    $newVisa->user_id = session ()->get ('user');
                    //$newVisa->purchase_date = $purchase_date_list[$i];
                    $newVisa->save ();
                }

                if ($saleItem->item_type == "Guest House") {
                    $GuestHouse = new GuestHouseOrder();
                    $GuestHouse->room_no = $room_no_list[$i];
                    $GuestHouse->place = $place_list[$i];
                    $GuestHouse->from_date = $visa_date_of_issue_list[$i];
                    $GuestHouse->to_date = $visa_valid_until_list[$i];
                    $GuestHouse->sale_id = $sale_order->id;
                    $GuestHouse->sale_item_id = $saleItem->id;
                    $GuestHouse->currency = $currency_list[$i];
                    $GuestHouse->unit_price = $unit_price_list[$i];
                    $GuestHouse->remark = $remark_list[$i];
                    $GuestHouse->company = $request->company;
                    $GuestHouse->user_id = session ()->get ('user');
                    $GuestHouse->save ();
                }

            }

            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => '', 'unit_price' => ""]);

        } catch (\Exception $e) {

            DB::rollback();
            return response ()->json (['success' => 'UN SUCCESSFUL ' . $e, 'error: ' . $e, 'item' => 'error' . $e, 'unit_price' => $e]);

        }


        return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => '', 'unit_price' => "hello"]);

    }

    public function newAgencySaleOrderAngular(Request $request, Sale $sale)
    {

        DB::beginTransaction ();

        $user = auth()->user();

        try {

            $input = $request->all ();

            $customer_id = $request->customer_id;
            //$date_sale = $request->date_sale;
            $payment_id = $request->payment_id;


            $carbon_date = new Carbon($request->sale_date);
//            $carbon_date = $carbon_date->addDays (1);
            $date_sale = $carbon_date->toDateString ();

            $count_items = count ($request->cars);
            $counter_items = 0;
            $all_sales = $request->cars;

            $total_amount = 0.00;

            // Save Sale
            $sale_order = new SaleOrder();

            $sale_order->customer = $customer_id;
            $sale_order->ref = "";
            $sale_order->date = $date_sale;
            $sale_order->total_amount = 0.00;
            $sale_order->user = $user->id;
            $sale_order->payment_mode = $payment_id;
            $sale_order->company = $request->company;
            $sale_order->remark = $request->sale_remark;
            $sale_order->user_id = $user->id;

            $sale_order->save ();


            $count_items = count ($request->cars);
            $counter_items = 0;
            $all_sales = $request->cars;

            for ($i = 0; $i < $count_items; $i++) {

                $car = $all_sales[$i];

                $counter_items++;

                $saleItem = new SaleOrderItem();
                $saleItem->item = $car['item'];
                $saleItem->qty = $car['qty'];
                $saleItem->date = $date_sale;
                $saleItem->discount = 0.00;
                $saleItem->sub_total = 0.00;//($saleItem->qty * $saleItem->unit_price) - $saleItem->discount;



                $saleItem->unit_cost = 0.00;
                $saleItem->user_id = $user->id;

                $saleItem->currency = $car['currency'];
                $saleItem->item_type =  $car['item'];
                $saleItem->description =  $car['description'];
                $saleItem->remark = $car['remark'];
                $saleItem->company = $request->company;

                $saleItem->sale ()->associate ($sale_order);
                $saleItem->save ();

                if ($saleItem->item_type == "Ticket") {

                    $newTicket = new TicketOrder();
                    $newTicket->customer_id = $customer_id;
                    $newTicket->airline = $car['airline'];
                    $newTicket->travel_agency = $car['travel_agency'];

                    $carbon_date = new Carbon($car['return_date']);
//                    $carbon_date = $carbon_date->addDays (1);
                    $newTicket->return_date = $carbon_date->toDateString ();

                    $carbon_date = new Carbon($car['booking_expire_date']);
//                    $carbon_date = $carbon_date->addDays (1);
                    $newTicket->purchase_date = $carbon_date->toDateString ();

                    $carbon_date = new Carbon($car['flight_date']);
//                    $carbon_date = $carbon_date->addDays (1);
                    $newTicket->flight_date = $carbon_date->toDateString ();

                    $newTicket->po_number = $car['po_number'];
                    $newTicket->sector = $car['sector'];
                    $newTicket->ticket_status = $car['ticket_status'];
                    $newTicket->remark = $car['remark'];
                    $newTicket->currency = $car['currency'];
                    $newTicket->booking_number = $car['booking_number'];

                    $carbon_date = new Carbon($car['booking_expire_date']);
 //                   $carbon_date = $carbon_date->addDays (1);

                    $newTicket->booking_expire_date = $carbon_date->toDateString ();
                    $newTicket->sale_item_id = $saleItem->id;
                    $newTicket->sale_id = $sale_order->id;
                    $newTicket->company = $request->company;
                    $newTicket->user_id = $user->id;
                    $newTicket->save ();
                }


                if ($saleItem->item_type == "Visa") {

                    $newVisa = new VisaOrder();
                    $newVisa->visa_supplier = $car['visa_supplier'];
                    $newVisa->customer_id = $request->customer_id;
                    $newVisa->visa_period = $car['visa_period'];

                    $carbon_date = new Carbon($car['visa_date_of_issue']);
   //                 $carbon_date = $carbon_date->addDays (1);
                    $newVisa->visa_date_of_issue = $carbon_date->toDateString ();

                    $carbon_date = new Carbon($car['visa_valid_until']);
   //                 $carbon_date = $carbon_date->addDays (1);
                    $newVisa->visa_valid_until = $carbon_date->toDateString ();

                    $newVisa->visa_status = $car['visa_status'];

                    $newVisa->currency = $car['currency'];
                    $newVisa->remark = $car['remark'];

                    $newVisa->company = $request->company;
                    $newVisa->prepared_by = $car['prepared_by'];

                    $newVisa->sale_id = $sale_order->id;
                    $newVisa->sale_item_id = $saleItem->id;
                    $saleItem->user_id = $user->id;
                    $newVisa->user_id = $user->id;

                    $newVisa->save ();
                }
                if ($saleItem->item_type == "Guest House") {

                    $GuestHouse = new GuestHouseOrder();
                    $GuestHouse->room_no = $car['room_no'];
                    $GuestHouse->place = $car['place'];
                    $GuestHouse->currency = $car['currency'];;
                    $GuestHouse->unit_price = $car['unit_price'];

                    $carbon_date = new Carbon($car['visa_date_of_issue']);
     //               $carbon_date = $carbon_date->addDays (1);

                    $GuestHouse->from_date = $carbon_date->toDateString ();

                    $carbon_date = new Carbon($car['visa_valid_until']);
     //               $carbon_date = $carbon_date->addDays (1);
                    $GuestHouse->to_date = $carbon_date->toDateString ();

                    $GuestHouse->user_id = $user->id;

                    $GuestHouse->sale_id = $sale_order->id;
                    $GuestHouse->sale_item_id = $saleItem->id;
                    $GuestHouse->remark = $car['remark'];
                    $GuestHouse->customer = $request->customer_id;
                    $GuestHouse->prepared_by = $car['prepared_by'];
                    $GuestHouse->company = $request->company;
                    $GuestHouse->pricing_way = $car['pricing_way'];
                    $GuestHouse->save ();
                }

                if ($car['item'] == "Other Item") {
                    // Other Items Insertion
                    $otherItem = new OtherItem();
                    $otherItem->unit_cost = 0.00;
                    $otherItem->unit_price = $car['unit_price'];
                    $otherItem->item_id = $car['item_id'];
                    $otherItem->gross = $car['unit_price'];
                    $otherItem->save ();
                }
                $saleItem->save();
            }

            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => '', 'unit_price' => ""]);

        } catch (\Exception $e) {

            DB::rollback();
            return response ()->json (['success' => 'UN SUCCESSFUL ' . $e, 'error: ' . $e, 'item' => 'error' . $e, 'unit_price' => $e]);

        }


        return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => '', 'unit_price' => "hello"]);

    }

    public function getRoomPrice(Request $request, Sale $sale)
    {

        DB::beginTransaction ();

        try {

            $input = $request->all ();

            $room_id = $request->room_id;
            $pricing_way = $request->pricing_way;

            $room = DB::table ('rooms')
                ->where ('rooms.id', $room_id)->first ();

            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => '', 'unit_price' => "",'room'=>$room]);

        } catch (\Exception $e) {

            return response ()->json (['success' => 'UN SUCCESSFUL ' . $e, 'error: ' . $e, 'item' => 'error' . $e, 'unit_price' => $e]);

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

        $data['accounts'] = BankAccount::where ('status', 'active')->get ();


        $data['modify'] = 1;
        $sales_date = $this->sale->find ($sales_id);
        $d = $sales_date->date;
        $date_converted = date ("d/m/Y", strtotime ($d));

        $data['sales'] = DB::table ('customers')
            ->join ('sales', 'customers.id', '=', 'sales.customer')
            ->select('customers.first_name','customers.middle_name','customers.last_name','sales.*')
            ->where ('sales.id', $sales_id)
            ->first ();

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


        if ($item_detail->item == "Ticket") {
            $sales_item2 = DB::table ('tickets')
                ->where ('tickets.sale_item_id', $sale_item_id)
                ->get ();
        } else if ($item_detail->item == "Visa") {
            $sales_item2 = DB::table ('visas')
                ->where ('visas.sale_item_id', $sale_item_id)
                ->get ();
        } else if ($item_detail->item == "Guest House") {
            $sales_item2 = DB::table ('guest_houses')
                ->where ('guest_houses.sale_item_id', $sale_item_id)
                ->get ();
        }else{
            $sales_item2 = $item_detail;
        }

        //dd($sales_item2);
        $data['sales_item'] = $sales_item2;
        $data['item_type'] = $item_detail->item;
        return response ()->json (['success' => 'DONE', 'item' => $sales_item2, 'item_type' => $item_detail->item, 'sale_id' => '']);
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

            if ($item_detail->item == "Ticket") {
                $sales_item2 = DB::table ('ticket_orders')
                    ->where ('ticket_orders.sale_item_id', $sale_item_id)
                    ->get ();
            } else if ($item_detail->item == "Visa") {
                $sales_item2 = DB::table ('visa_orders')
                    ->where ('visa_orders.sale_item_id', $sale_item_id)
                    ->get ();
            } else if ($item_detail->item == "Guest House") {
                $sales_item2 = DB::table ('guest_house_orders')
                    ->where ('guest_house_orders.sale_item_id', $sale_item_id)
                    ->get ();
            } else if ($item_detail->item == "Other Item") {
                $sales_item2 = DB::table ('sale_order_items')
                    ->where ('sale_order_items.id', $sale_item_id)
                    ->get ();
            }


            //dd($sales_item2);
            $data['sales_item'] = $sales_item2;
            $data['item_type'] = $item_detail->item;

            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => $sales_item2, 'item_type' => $item_detail->item, 'sale_id' => $sale_item_id]);

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
            $ticket_type = $request->ticket_type;
            $departure_time = $request->departure_time;
            $arrival_time = $request->arrival_time;
            $departure_time_return = $request->departure_time_return;
            $arrival_time_return = $request->arrival_time_return;


            DB::table ('tickets')
                ->where ('sale_item_id', $sale_item_id)
                ->update (['airline' => $airline,
                    'travel_agency' => $travel_agency,
                    'sector' => $sector,
                    'flight_date' => $flight_date,
                    'return_date' => $return_date,
                    'po_number' => $po_number,
                    'ticket_number' => $ticket_number,
                    'invoice_num' => $invoice_num,
                    'purchase_date' => $purchase_date,
                    'ticket_status' => $ticket_status,
                    'ticket_type' => $ticket_type,
                    'departure_time' => $departure_time,
                    'arrival_time' => $arrival_time,
                    'remark' => $remark,
                    'departure_time_return' => $departure_time_return,
                    'arrival_time_return' => $arrival_time_return
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
            $booking_expire_date = $request->booking_expire_date;


            DB::table ('ticket_orders')
                ->where ('sale_item_id', $sale_item_id)
                ->update (['airline' => $airline,
                    'travel_agency' => $travel_agency,
                    'sector' => $sector,
                    'flight_date' => $flight_date,
                    'return_date' => $return_date,
                    'ticket_status' => $ticket_status,
                    'currency' => $currency,
                    'booking_number' => $booking_number,
                    'po_number' => $po_number,
                    'booking_expire_date' => $booking_expire_date,
                    'remark' => $remark
                ]);


            DB::table ('sale_order_items')
                ->where ('id', $sale_item_id)
                ->update (['description' => $sector
                ]);


            if($ticket_status=="Cancelled" || $ticket_status=="Void"|| $ticket_status=="Sold")
            {
                DB::table ('sale_order_items')
                    ->where ('id', $sale_item_id)
                    ->update (['status' => 'Void'
                    ]);
            }


            // get order id
            $sale_order = DB::table('sale_order_items')->where('id', $sale_item_id)->first();
            $order_id = $sale_order->sale_id;

            // get active orders count
            $active_orders = DB::table('sale_order_items')
                ->where('status', 'active')
                ->where ('sale_id',$order_id)
                ->count();

            if($active_orders==0){

                DB::table ('sale_orders')
                    ->where ('id', $order_id)
                    ->update (['status' => 'Cancelled'
                    ]);

            }



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


            DB::table ('visa_orders')
                ->where ('sale_item_id', $sale_item_id)
                ->update (['visa_supplier' => $visa_supplier,
                    'visa_period' => $visa_period,
                    'visa_date_of_issue' => $visa_date_of_issue,
                    'visa_valid_until' => $visa_valid_until,
                    'currency' => $visa_currency,
                    'visa_status' => $visa_status,
                    'remark' => $remark
                ]);


            DB::table ('sale_order_items')
                ->where ('id', $sale_item_id)
                ->update (['description' => $visa_period
                ]);

            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => $sale_item_id, 'unit_price' => 10]);

        } catch (\Exception $e) {

            return response ()->json (['success' => 'UN SUCCESSFUL' . $e, 'item' => 'error' . $e, 'sale_id' => $e]);

        }
    }

    public function updateItemOrderGuest(Request $request)
    {
        DB::beginTransaction ();

        try {

            $input = $request->all ();

            $sale_item_id = $request->item_id;


            $place = $request->place;
            $room_no = $request->room_no;
            $guest_from_date = $request->guest_from_date;
            $guest_to_date = $request->guest_to_date;
            $guest_currency = $request->guest_currency;
            $guest_unit_price = $request->guest_unit_price;
            $guest_prepared_by = $request->guest_prepared_by;
            $remark = $request->guest_remark;


            DB::table ('guest_house_orders')
                ->where ('sale_item_id', $sale_item_id)
                ->update (['place' => $place,
                    'room_no' => $room_no,
                    'from_date' => $guest_from_date,
                    'to_date' => $guest_to_date,
                    'currency' => $guest_currency,
                    'unit_price' => $guest_unit_price,
                    'prepared_by' => $guest_prepared_by,
                    'remark' => $remark
                ]);


            DB::table ('sale_order_items')
                ->where ('id', $sale_item_id)
                ->update (['description' => $room_no
                ]);

            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => $sale_item_id, 'unit_price' => 10]);

        } catch (\Exception $e) {

            return response ()->json (['success' => 'UN SUCCESSFUL', 'item' => 'error' . $e, 'sale_id' => $e]);

        }
    }

    public function updateItemGuest(Request $request)
    {
        DB::beginTransaction ();

        try {

            $input = $request->all ();

            $sale_item_id = $request->item_id;


            $place = $request->place;
            $room_no = $request->room_no;
            $guest_from_date = $request->guest_from_date;
            $guest_to_date = $request->guest_to_date;
            $guest_currency = $request->guest_currency;
            $guest_unit_price = $request->guest_unit_price;
            $guest_prepared_by = $request->guest_prepared_by;
            $remark = $request->guest_remark;


            DB::table ('guest_houses')
                ->where ('sale_item_id', $sale_item_id)
                ->update (['place' => $place,
                    'room_no' => $room_no,
                    'from_date' => $guest_from_date,
                    'to_date' => $guest_to_date,
                    'currency' => $guest_currency,
                    'unit_price' => $guest_unit_price,
                    'prepared_by' => $guest_prepared_by,
                    'remark' => $remark
                ]);


            DB::table ('sale_items')
                ->where ('id', $sale_item_id)
                ->update (['description' => $room_no
                ]);

            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => $sale_item_id, 'unit_price' => 10]);

        } catch (\Exception $e) {

            return response ()->json (['success' => 'UN SUCCESSFUL', 'item' => 'error' . $e, 'sale_id' => $e]);

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
            $prepared_by = $request->visa_prepared_by;


            DB::table ('visas')
                ->where ('sale_item_id', $sale_item_id)
                ->update (['visa_supplier' => $visa_supplier,
                    'visa_period' => $visa_period,
                    'visa_date_of_issue' => $visa_date_of_issue,
                    'visa_valid_until' => $visa_valid_until,
                    'currency' => $visa_currency,
                    'visa_status' => $visa_status,
                    'remark' => $remark,
                    'purchase_date' => $purchase_date,
                    'prepared_by' =>$prepared_by
                ]);


            DB::table ('sale_order_items')
                ->where ('id', $sale_item_id)
                ->update (['description' => $visa_period
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
            $sector = $request->sector;
            $travel_agency = $request->travel_agency;
            $airline = $request->airline;


            DB::table ('visas')
                ->where ('id', 448)
                ->update (['airline' => $airline,
                    'travel_agency' => $travel_agency,
                    'sector' => $sector,
                    'flight_date' => $flight_date,
                    'return_date' => $return_date,
                    //   'purchase_date'=>$purchase_date
                ]);


            DB::commit ();

            return response ()->json (['success' => 'DONE', 'item' => 'BBBBBBBBBBB', 'sale_id' => $sale_item_id, 'unit_price' => 10]);

        } catch (\Exception $e) {

            return response ()->json (['success' => 'UN SUCCESSFUL' . $e, 'item' => 'error' . $e, 'sale_id' => $e]);

        }
    }

    public function updateOtherItem(Request $request)
    {
        DB::beginTransaction ();

        try {

            $input = $request->all ();

            $sale_item_id = $request->item_id;


            $flight_date = $request->flight_date;
            $return_date = $request->return_date;
            $ticket_status = $request->ticket_status;
            $remark = $request->remark;
            $sector = $request->sector;
            $travel_agency = $request->travel_agency;


            DB::table ('sale_items')
                ->where ('id', 448)
                ->update (['airline' => 3,
                    'travel_agency' => $travel_agency,
                    'sector' => $sector,
                    'flight_date' => $flight_date,
                    'return_date' => $return_date,
                    //   'purchase_date'=>$purchase_date
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
            ->where ('sale_items.customer_id', $customer_id)->get ();

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
            $void_reason = $request->void_reason;

            $sale_data = DB::table ('sales')
                ->where ('id', $sale_id)
                ->first ();

            if($sale_data->status!="Void"){
                $customer_data = DB::table ('customers')
                    ->where ('id', $sale_data->customer)
                    ->first ();

                $customer_balance_usd = $customer_data->balance_usd-$sale_data->total_amount_usd;
                $customer_balance_ern = $customer_data->balance_ern-$sale_data->total_amount_ern;


                DB::table('customers')
                    ->where ('id', $sale_data->customer)
                    ->update(['balance_usd'=>$customer_balance_usd,
                        'balance_ern'=>$customer_balance_ern
                    ]);


                DB::table ('sales')
                    ->where ('id', $sale_id)
                    ->update (['status' => 'Void',
                        'void_reason'=>$void_reason
                    ]);

                DB::table ('tickets')
                    ->where ('sale_id', $sale_id)
                    ->update (['ticket_status' => 'Void'
                    ]);

                DB::table ('visas')
                    ->where ('sale_id', $sale_id)
                    ->update (['visa_status' => 'Void'
                    ]);

                DB::table ('guest_houses')
                    ->where ('sale_id', $sale_id)
                    ->update (['guest_house_status' => 'Void'
                    ]);

                DB::table ('sale_items')
                    ->where ('sale_id', $sale_id)
                    ->update (['status' => 'Void'
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

                $data['accounts'] = BankAccount::where ('status', 'active')->get ();


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

                $data['sales_item'] = $sales_item2;
                return view ('sales/detail', $data)->with ('conv_date', $date_converted);
            }else{
                return redirect()->route('show_sale',['id'=>$sale_data->id]);
            }


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


            $sales_id = $sale_id;

            DB::table ('sale_orders')
                ->where ('id', $sale_id)
                ->update (['status' => 'Void'
                ]);

            DB::table ('sale_order_items')
                ->where ('sale_id', $sale_id)
                ->update (['status' => 'Void'
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

            dd ($e);
//            return response ()->json (['success' => 'UN SUCCESSFUL' . $e, 'item' => 'error' . $e, 'sale_id' => $e]);

        }
    }

    // Done
    public function profit_loss(Request $request)
    {
        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');
        $data['companys'] = DB::table ('companys')->get ();

        if($request->input ('company')=="2&5"){
            $data['company'] = "2&5";

        }else{
            $data['company'] = intval($request->input ('company'));
            $company=intval($request->input('company'));
        }


        if (isset($start_date) && isset($end_date)) {

            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));

            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            if($request->input ('company')=="2&5") {
                $data['company'] = "2&5";
                $data['cost_profits'] = DB::table ('sale_items')
                    ->join ('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->join ('customers', 'customers.id', '=', 'sales.customer')
                    ->join ('companys','companys.id','=','sale_items.company')
                    ->select ( 'companys.*','customers.*', 'sale_items.*')
                    ->where('sale_items.company','=',"2")
                    ->orwhere('sale_items.company','=',"5")
                    ->whereBetween ('sale_items.date', [$start_date, $end_date])
                    ->get ();

                $data['company_name'] = "Durgam Keren and Barentu";

            }else{

                $data['company'] = intval($request->input ('company'));
                $company = intval($request->input ('company'));

                $data['cost_profits'] = DB::table ('sale_items')
                    ->join ('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->join ('customers', 'customers.id', '=', 'sales.customer')
                    ->join ('companys', 'companys.id', '=', 'sale_items.company')
                    ->select ('customers.*', 'companys.company_name', 'sale_items.*')
                    ->where('sale_items.company','=',$company)
                    ->whereBetween ('sale_items.date', [$start_date, $end_date])
                    ->get ();

                $company_data = DB::table ('companys')
                    ->where('companys.id','=',$request->input ('company'))
                    ->first();

               $data['company_name'] = $company_data->company_name;

            }

            //dd($data['sale_items']);

            if (isset($print)) {
                //$start_date = $this->change_date_to_standard ($request->input ('start_date'));
                //$end_date = $this->change_date_to_standard ($request->input ('end_date'));

              //  dd($start_date);

//                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
//                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));


//                $data['cost_profits'] = DB::table ('sale_items')
//                    ->join ('sales', 'sales.id', '=', 'sale_items.sale_id')
//                    ->join ('customers', 'customers.id', '=', 'sales.customer')
//                    ->join ('companys', 'companys.id', '=', 'sale_items.company')
//                    ->select ('customers.*', 'companys.company_name', 'sale_items.*')
//                    ->where('sales.company','=',$company)
//                    ->whereBetween ('sale_items.date', [$start_date, $end_date])
//                    ->get ();
//                //dd( $data['cost_profits'] );

                //dd($data['cost_profits']);

                $pdf = PDF::loadView ('reports/cost_profit_report', $data);
                $pdf->setPaper ('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');

            } else {
                //dd(intval($data['company']));
                return view ('reports/profit_loss_index', $data);
            }

        }
        return view ('reports/profit_loss_index', $data);
    }

    // Done
    public function profit_loss_overall(Request $request)
    {
        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');

        $data['companys'] = DB::table ('companys')->get ();

        if($request->input ('company')=="2&5"){
            $data['company'] = "2&5";

        }else{
            $data['company'] = intval($request->input ('company'));
            $company=intval($request->input('company'));
        }

        if (isset($start_date) && isset($end_date)) {

            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));


            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            if($request->input ('company')=="2&5") {
                $data['company'] = "2&5";


                $data['cost_profits'] = DB::table ('sale_items')
                    ->join ('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->join ('customers', 'customers.id', '=', 'sales.customer')
                    ->join ('companys', 'companys.id', '=', 'sale_items.company')
                    ->select ('customers.*', 'companys.company_name', 'sale_items.*')
                    ->where ('sale_items.company', '=', "2")
                    ->orwhere ('sale_items.company', '=', "5")
                    ->whereBetween ('sale_items.date', [$start_date, $end_date])
                    ->get ();

                $data['company_name'] = "Durgam Keren and Barentu";

            }else{

                $data['company'] = intval($request->input ('company'));
                $company = intval($request->input ('company'));
                //dd($company);

                $data['cost_profits'] = DB::table ('sale_items')
                    ->join ('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->join ('customers', 'customers.id', '=', 'sales.customer')
                    ->join ('companys', 'companys.id', '=', 'sale_items.company')
                    ->select ('customers.*', 'companys.company_name', 'sale_items.*')
                    ->where('sale_items.company','=',$company)
                    ->whereBetween ('sale_items.date', [$start_date, $end_date])
                    ->get ();

                $company_data = DB::table ('companys')
                    ->where('companys.id','=',$request->input ('company'))
                    ->first();

                $data['company_name'] = $company_data->company_name;

            }

            //dd($data['sale_items']);


            if (isset($print)) {

                $start_date = $this->change_date_to_standard ($request->input ('start_date'));
                $end_date = $this->change_date_to_standard ($request->input ('end_date'));

                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));


                if($request->input ('company')=="2&5") {
                    $data['company'] = "2&5";


                    $data['cost_profits'] = DB::table ('sale_items')
                        ->join ('sales', 'sales.id', '=', 'sale_items.sale_id')
                        ->join ('customers', 'customers.id', '=', 'sales.customer')
                        ->join ('companys', 'companys.id', '=', 'sale_items.company')
                        ->select ('customers.*', 'companys.company_name', 'sale_items.*')
                        ->where ('sale_items.company', '=', "2")
                        ->orwhere ('sale_items.company', '=', "5")
                        ->whereBetween ('sale_items.date', [$start_date, $end_date])
                        ->get ();

                    $data['company_name'] = "Durgam Keren and Barentu";

                }
                else{

                    $data['company'] = intval($request->input ('company'));
                    $company = intval($request->input ('company'));

                    $data['cost_profits'] = DB::table ('sale_items')
                        ->join ('sales', 'sales.id', '=', 'sale_items.sale_id')
                        ->join ('customers', 'customers.id', '=', 'sales.customer')
                        ->join ('companys', 'companys.id', '=', 'sale_items.company')
                        ->select ('customers.*', 'companys.company_name', 'sale_items.*')
                        ->where('sale_items.company','=',$company)
                        ->whereBetween ('sale_items.date', [$start_date, $end_date])
                        ->get ();

                    $company_data = DB::table ('companys')
                        ->where('companys.id','=',$request->input ('company'))
                        ->first();

                    $data['company_name'] = $company_data->company_name;

                }

                //dd($data['sale_items']);

                if($request->input ('company')=="2&5"){
                    $data['company'] = "2&5";
                    $data['cost_profits_expenses'] = DB::table ('expenses')
                        ->join ('companys','companys.id','=','expenses.company')
                        ->where('expenses.company','=',2)
                        ->orWhere ('expenses.company','=',5)
                        ->whereBetween ('date', [$start_date, $end_date])
                        ->select ('companys.*','expenses.*')
                        ->get ();

                }else{
                    $data['company'] = intval($request->input ('company'));
                    $data['companys'] = DB::table ('companys')->get ();
                    $company=intval($request->input('company'));
                    $data['cost_profits_expenses'] = DB::table ('expenses')
                        ->join ('companys','companys.id','=','expenses.company')
                        ->where('expenses.company','=',$company)
                        ->whereBetween ('date', [$start_date, $end_date])
                        ->select ('companys.*','expenses.*')
                        ->get ();
                }

                $pdf = PDF::loadView ('reports/over_all_report_print', $data);
                $pdf->setPaper ('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');

            } else {
                return view ('reports/profit_loss_index', $data);
            }

        }
        return view ('reports/over_all_report_index', $data);
    }

    // Done
    public function profit_loss_overall_external(Request $request)
    {
        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');

        $data['companys'] = DB::table ('companys')->get ();

        if($request->input ('company')=="2&5"){
            $data['company'] = "2&5";

        }else{
            $data['company'] = intval($request->input ('company'));
            $company=intval($request->input('company'));
        }


        if (isset($start_date) && isset($end_date)) {

            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));


            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            if($request->input ('company')=="2&5") {
                $data['company'] = "2&5";


                $data['cost_profits'] = DB::table ('sale_items')
                    ->join ('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->join ('customers', 'customers.id', '=', 'sales.customer')
                    ->join ('companys', 'companys.id', '=', 'sale_items.company')
                    ->select ('customers.*', 'companys.company_name', 'sale_items.*')
                    ->where ('sale_items.company', '=', "2")
                    ->orwhere ('sale_items.company', '=', "5")
                    ->whereBetween ('sale_items.date', [$start_date, $end_date])
                    ->get ();

                $data['company_name'] = "Durgam Keren and Barentu";

            }else{

                $data['company'] = intval($request->input ('company'));
                $company = intval($request->input ('company'));
                //dd($company);

                $data['cost_profits'] = DB::table ('sale_items')
                    ->join ('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->join ('customers', 'customers.id', '=', 'sales.customer')
                    ->join ('companys', 'companys.id', '=', 'sale_items.company')
                    ->select ('customers.*', 'companys.company_name', 'sale_items.*')
                    ->where('sale_items.company','=',$company)
                    ->whereBetween ('sale_items.date', [$start_date, $end_date])
                    ->get ();

                $company_data = DB::table ('companys')
                    ->where('companys.id','=',$request->input ('company'))
                    ->first();

                $data['company_name'] = $company_data->company_name;

            }

            //dd($data['sale_items']);


            if (isset($print)) {


                $start_date = $this->change_date_to_standard ($request->input ('start_date'));
                $end_date = $this->change_date_to_standard ($request->input ('end_date'));

                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));


                if($request->input ('company')=="2&5") {
                    $data['company'] = "2&5";


                    $data['cost_profits'] = DB::table ('sale_items')
                        ->join ('sales', 'sales.id', '=', 'sale_items.sale_id')
                        ->join ('customers', 'customers.id', '=', 'sales.customer')
                        ->join ('companys', 'companys.id', '=', 'sale_items.company')
                        ->select ('customers.*', 'companys.company_name', 'sale_items.*')
                        ->where ('sale_items.company', '=', "2")
                        ->orwhere ('sale_items.company', '=', "5")
                        ->whereBetween ('sale_items.date', [$start_date, $end_date])
                        ->get ();

                    $data['company_name'] = "Durgam Keren and Barentu";

                }
                else{

                    $data['company'] = intval($request->input ('company'));
                    $company = intval($request->input ('company'));

                    $data['cost_profits'] = DB::table ('sale_items')
                        ->join ('sales', 'sales.id', '=', 'sale_items.sale_id')
                        ->join ('customers', 'customers.id', '=', 'sales.customer')
                        ->join ('companys', 'companys.id', '=', 'sale_items.company')
                        ->select ('customers.*', 'companys.company_name', 'sale_items.*')
                        ->where('sale_items.company','=',$company)
                        ->whereBetween ('sale_items.date', [$start_date, $end_date])
                        ->get ();

                    $company_data = DB::table ('companys')
                        ->where('companys.id','=',$request->input ('company'))
                        ->first();

                    $data['company_name'] = $company_data->company_name;

                }

                //dd($data['sale_items']);

                if($request->input ('company')=="2&5"){
                    $data['company'] = "2&5";
                    $data['cost_profits_expenses'] = DB::table ('expenses')
                        ->where('expenses.company','=',2)
                        ->orWhere ('expenses.company','=',5)
                        ->whereBetween ('date', [$start_date, $end_date])
                        ->select ('expenses.*')
                        ->get ();

                }else{
                    $data['company'] = intval($request->input ('company'));
                    $data['companys'] = DB::table ('companys')->get ();
                    $company=intval($request->input('company'));
                    $data['cost_profits_expenses'] = DB::table ('expenses')
                        ->join ('companys','companys.id','=','expenses.company')
                        ->where('expenses.company','=',$company)
                        ->whereBetween ('date', [$start_date, $end_date])
                        ->select ('companys.*','expenses.*')
                        ->get ();
                }

                $pdf = PDF::loadView ('reports/over_all_report_external_print', $data);
                $pdf->setPaper ('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');


            } else {
                return view ('reports/profit_loss_index', $data);
            }

        }
        return view ('reports/over_all_report_external_index', $data);
    }

    // Done
    public function profit_loss_tickets(Request $request)
    {
        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');

        $data['companys'] = DB::table ('companys')->get ();

        if($request->input ('company')=="2&5"){
            $data['company'] = "2&5";

        }else{
            $data['company'] = intval($request->input ('company'));
            $company=intval($request->input('company'));
        }


        if (isset($start_date) && isset($end_date)) {


            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));


            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            if($request->input ('company')=="2&5") {
                $data['company'] = "2&5";


                $data['cost_profits'] = DB::table ('tickets')
                    ->join ('customers', 'customers.id', '=', 'tickets.customer_id')
                    ->join ('airlines', 'airlines.id', '=', 'tickets.airline')
                    ->select ('customers.*', 'tickets.*', 'airlines.airline_name', 'airlines.airline_code')
                    ->where ('tickets.company', '=', 2)
                    ->orWhere ('tickets.company', '=', 5)
                    ->whereBetween ('purchase_date', [$start_date, $end_date])
                    ->get ();
            }else{
                $data['company'] = intval($request->input ('company'));

                $data['cost_profits'] = DB::table ('tickets')
                    ->join ('customers', 'customers.id', '=', 'tickets.customer_id')
                    ->join ('airlines', 'airlines.id', '=', 'tickets.airline')
                    ->select ('customers.*', 'tickets.*', 'airlines.airline_name', 'airlines.airline_code')
                    ->where ('tickets.company', '=', $company)
                    ->whereBetween ('purchase_date', [$start_date, $end_date])
                    ->get();

            }

            //dd($data['sale_items']);

            if (isset($print))
            {
//                $start_date = $this->change_date_to_standard ($request->input ('start_date'));
//                $end_date = $this->change_date_to_standard ($request->input ('end_date'));
//
//                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
//                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));
//
//
//                $data['cost_profits'] = DB::table ('tickets')
//                    ->join ('customers', 'customers.id', '=', 'tickets.customer_id')
//                    ->join ('airlines', 'airlines.id', '=', 'tickets.airline')
//                    ->select ('customers.*', 'tickets.*','airlines.airline_name','airlines.airline_code')
//                    ->where('tickets.company','=',$company)
//                    ->whereBetween ('purchase_date', [$start_date, $end_date])
//                    ->get ();
                //dd( $data['cost_profits'] );

                //dd($data['cost_profits']);

                $pdf = PDF::loadView ('reports/tickets_report_print', $data);
                $pdf->setPaper ('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');

            } else {
                return view ('reports/ticket_report_list', $data);
            }

        }
        return view ('reports/profit_loss_index', $data);
    }

    // Done
    public function profit_loss_tickets_external(Request $request)
    {
        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');

        $data['companys'] = DB::table ('companys')->get ();

//        dd($request->input ('company'));
        if($request->input ('company')=="2&5"){
            $data['company'] = "2&5";

        }else{
            $data['company'] = intval($request->input ('company'));
            $company=intval($request->input('company'));
        }

        if (isset($start_date) && isset($end_date)) {


            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));


            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            if($request->input ('company')=="2&5") {
                $data['company'] = "2&5";


                $data['cost_profits'] = DB::table ('tickets')
                    ->join ('customers', 'customers.id', '=', 'tickets.customer_id')
                    ->join ('airlines', 'airlines.id', '=', 'tickets.airline')
                    ->select ('customers.*', 'tickets.*', 'airlines.airline_name', 'airlines.airline_code')
                    ->where ('tickets.company', '=', 2)
                    ->orWhere ('tickets.company', '=', 5)
                    ->whereBetween ('purchase_date', [$start_date, $end_date])
                    ->get ();
            }else{
                $data['company'] = intval($request->input ('company'));

                $data['cost_profits'] = DB::table ('tickets')
                    ->join ('customers', 'customers.id', '=', 'tickets.customer_id')
                    ->join ('airlines', 'airlines.id', '=', 'tickets.airline')
                    ->select ('customers.*', 'tickets.*', 'airlines.airline_name', 'airlines.airline_code')
                    ->where ('tickets.company', '=', $company)
                    ->whereBetween ('purchase_date', [$start_date, $end_date])
                    ->get();

            }

            //dd($data['sale_items']);

            if (isset($print)) {
                $start_date = $this->change_date_to_standard ($request->input ('start_date'));
                $end_date = $this->change_date_to_standard ($request->input ('end_date'));

                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));


                if($request->input ('company')=="2&5") {
                    $data['company'] = "2&5";


                    $data['cost_profits'] = DB::table ('tickets')
                        ->join ('customers', 'customers.id', '=', 'tickets.customer_id')
                        ->join ('airlines', 'airlines.id', '=', 'tickets.airline')
                        ->select ('customers.*', 'tickets.*', 'airlines.airline_name', 'airlines.airline_code')
                        ->where ('tickets.company', '=', 2)
                        ->orWhere ('tickets.company', '=', 5)
                        ->whereBetween ('purchase_date', [$start_date, $end_date])
                        ->get ();
                }else{
                    $data['company'] = intval($request->input ('company'));

                    $data['cost_profits'] = DB::table ('tickets')
                        ->join ('customers', 'customers.id', '=', 'tickets.customer_id')
                        ->join ('airlines', 'airlines.id', '=', 'tickets.airline')
                        ->select ('customers.*', 'tickets.*', 'airlines.airline_name', 'airlines.airline_code')
                        ->where ('tickets.company', '=', $company)
                        ->whereBetween ('purchase_date', [$start_date, $end_date])
                        ->get();

                }
                //dd( $data['cost_profits'] );

                //dd($data['cost_profits']);

                $pdf = PDF::loadView ('reports/tickets_report_external_print', $data);
                $pdf->setPaper ('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');

            } else {
                return view ('reports/ticket_report_list_external', $data);
            }

        }
        return view ('reports/profit_loss_index', $data);
    }

    // Done
    public function profit_loss_visas(Request $request)
    {
        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');

        $data['companys'] = DB::table ('companys')->get ();

        if($request->input ('company')=="2&5"){
            $data['company'] = "2&5";

        }else{
            $data['company'] = intval($request->input ('company'));
            $company=intval($request->input('company'));
        }

        if (isset($start_date) && isset($end_date)) {


            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));


            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            if($request->input ('company')=="2&5") {

                $data['company'] = "2&5";

                $data['cost_profits'] = DB::table ('visas')
                    ->join ('customers', 'customers.id', '=', 'visas.customer_id')
                    ->join ('companys', 'companys.id', '=', 'visas.company')
                    ->select ('customers.*', 'visas.*')
                    ->where ('visas.company', '=', 2)
                    ->orWhere('visas.company', '=', 5)
                    ->whereBetween ('purchase_date', [$start_date, $end_date])
                    ->get ();
            }else {
                $data['company'] = intval ($request->input ('company'));
                $data['cost_profits'] = DB::table ('visas')
                    ->join ('customers', 'customers.id', '=', 'visas.customer_id')
                    ->join ('companys', 'companys.id', '=', 'visas.company')
                    ->select ('customers.*', 'visas.*')
                    ->where ('visas.company', '=', $company)
                    ->whereBetween ('purchase_date', [$start_date, $end_date])
                    ->get ();

            }
                //dd($data['sale_items']);

            if (isset($print)) {

                $start_date = $this->change_date_to_standard ($request->input ('start_date'));
                $end_date = $this->change_date_to_standard ($request->input ('end_date'));

                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));


                if($request->input ('company')=="2&5") {

                    $data['company'] = "2&5";

                    $data['cost_profits'] = DB::table ('visas')
                        ->join ('customers', 'customers.id', '=', 'visas.customer_id')
                        ->join ('companys', 'companys.id', '=', 'visas.company')
                        ->select ('customers.*', 'visas.*')
                        ->where ('visas.company', '=', 2)
                        ->orWhere('visas.company', '=', 5)
                        ->whereBetween ('purchase_date', [$start_date, $end_date])
                        ->get ();
                }else {
                    $data['company'] = intval ($request->input ('company'));
                    $data['cost_profits'] = DB::table ('visas')
                        ->join ('customers', 'customers.id', '=', 'visas.customer_id')
                        ->join ('companys', 'companys.id', '=', 'visas.company')
                        ->select ('customers.*', 'visas.*')
                        ->where ('visas.company', '=', $company)
                        ->whereBetween ('purchase_date', [$start_date, $end_date])
                        ->get ();

                }

                //dd( $data['cost_profits'] );
                //dd($data['cost_profits']);

                $pdf = PDF::loadView ('reports/visas_report_print', $data);
                $pdf->setPaper ('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');

            } else {
                return view ('reports/visas_report_list', $data);
            }

        }
        return view ('reports/profit_loss_index', $data);
    }

    // No need
    public function profit_loss_guest_houses(Request $request)
    {
        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');

        $data['company'] = intval($request->input ('company'));
        $data['companys'] = DB::table ('companys')->get ();
        $company=intval($request->input('company'));

        if (isset($start_date) && isset($end_date)) {


            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));


            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            $data['cost_profits'] = DB::table ('guest_houses')
                ->join ('customers', 'customers.id', '=', 'guest_houses.customer')
                ->join ('companys', 'companys.id', '=', 'guest_houses.company')
                ->join ('places', 'places.id', '=', 'guest_houses.place')
                ->join ('rooms', 'rooms.id', '=', 'guest_houses.room_no')
                ->select ('customers.*', 'places.place_name', 'rooms.room_name', 'guest_houses.*')
                ->where('guest_houses.company','=',$company)
                ->whereBetween ('purchase_date', [$start_date, $end_date])
                ->get ();

            //dd($data['sale_items']);

            if (isset($print)) {
                $start_date = $this->change_date_to_standard ($request->input ('start_date'));
                $end_date = $this->change_date_to_standard ($request->input ('end_date'));

                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));


                $data['cost_profits'] = DB::table ('guest_houses')
                    ->join ('customers', 'customers.id', '=', 'guest_houses.customer')
                    ->join ('companys', 'companys.id', '=', 'guest_houses.company')
                    ->join ('places', 'places.id', '=', 'guest_houses.place')
                    ->join ('rooms', 'rooms.id', '=', 'guest_houses.room_no')
                    ->select ('customers.*', 'rooms.room_name', 'places.place_name', 'guest_houses.*')
                    ->where('guest_houses.company','=',$company)
                    ->whereBetween ('purchase_date', [$start_date, $end_date])
                    ->get ();

                //dd( $data['cost_profits'] );
                //dd($data['cost_profits']);

                $pdf = PDF::loadView ('reports/guest_houses_report_print', $data);
                $pdf->setPaper ('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');

            } else {
                return view ('reports/guest_houses_report_list', $data);
            }

        }
        return view ('reports/profit_loss_index', $data);
    }

    // No need
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

    // No need
    public function tickets_index()
    {
        $data = [];
        $data['tickets'] = DB::table ('customers')
            ->join ('tickets', 'customers.id', '=', 'tickets.customer_id')
            ->join ('airlines', 'airlines.id', '=', 'tickets.airline')
            ->select ('airlines.airline_name','customers.*','tickets.*')
            ->get ();
        return view ('tickets/index', $data);
    }

    // No need
    public function visas_index()
    {
        $data = [];
        $data['visas'] = DB::table ('customers')
            ->join ('visas', 'customers.id', '=', 'visas.customer_id')
            ->get ();
        return view ('tickets/visa_index', $data);
    }

    // Done
    public function profit_loss_expenses(Request $request)
    {

        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');
        $data['companys'] = DB::table ('companys')->get ();

        if($request->input ('company')=="2&5"){
            $data['company'] = "2&5";

    }else{
        $data['company'] = intval($request->input ('company'));
            $company=intval($request->input('company'));
    }

        if (isset($start_date) && isset($end_date)) {


            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));


            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            if($request->input ('company')=="2&5"){
                $data['company'] = "2&5";
                $data['cost_profits'] = DB::table ('expenses')
                    ->join ('companys','companys.id','=','expenses.company')
                    ->where('expenses.company','=',2)
                    ->orWhere ('expenses.company','=',5)
                    ->whereBetween ('date', [$start_date, $end_date])
                    ->select ('companys.*','expenses.*')
                    ->get ();

            }else{
                $data['company'] = intval($request->input ('company'));
                $data['companys'] = DB::table ('companys')->get ();
                $company=intval($request->input('company'));
                $data['cost_profits'] = DB::table ('expenses')
                    ->join ('companys','companys.id','=','expenses.company')
                    ->where('expenses.company','=',$company)
                    ->whereBetween ('date', [$start_date, $end_date])
                    ->select ('companys.*','expenses.*')
                    ->get ();
            }


            if (isset($print)) {

                $start_date = $this->change_date_to_standard ($request->input ('start_date'));
                $end_date = $this->change_date_to_standard ($request->input ('end_date'));

                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));


                if($request->input ('company')=="2&5"){

                    $data['company'] = "2&5";
                    $data['cost_profits'] = DB::table ('expenses')
                        ->join ('companys','companys.id','=','expenses.company')
                        ->where('expenses.company','=',2)
                        ->orWhere ('expenses.company','=',5)
                        ->whereBetween ('date', [$start_date, $end_date])
                        ->select ('companys.*','expenses.*')
                        ->get ();

                }else{
                    $data['company'] = intval($request->input ('company'));
                    $company=intval($request->input('company'));
                    $data['cost_profits'] = DB::table ('expenses')
                        ->join ('companys','companys.id','=','expenses.company')
                        ->where('expenses.company','=',$company)
                        ->whereBetween ('date', [$start_date, $end_date])
                        ->select ('companys.*','expenses.*')
                        ->get ();
                }
                //dd( $data['cost_profits'] );

                //dd($data['cost_profits']);

                $pdf = PDF::loadView ('reports/expenses_report_print', $data);
                $pdf->setPaper ('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');

            } else {
                return view ('reports/expenses_report_list', $data);
            }

        }
        return view ('reports/expenses_report_list', $data);
    }

    public function profit_loss_receipts(Request $request)
    {
        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');

        $data['company'] = intval($request->input ('company'));
        $data['companys'] = DB::table ('companys')->get ();
        $company=intval($request->input('company'));

        //dd($start_date);

        if (isset($start_date) && isset($end_date)) {


            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));


            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            $data['cost_profits'] = DB::table ('receipts')
                ->join ('customers', 'customers.id', '=', 'receipts.customer')
                ->join ('companys', 'companys.id', '=', 'receipts.company')
                ->select ('customers.*', 'receipts.*')
                ->where('receipts.company','=',$company)
                ->whereBetween ('date', [$start_date, $end_date])
                ->get ();

            //dd($data['sale_items']);

            if (isset($print)) {
                $start_date = $this->change_date_to_standard ($request->input ('start_date'));
                $end_date = $this->change_date_to_standard ($request->input ('end_date'));

                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));


                $data['cost_profits'] = DB::table ('receipts')
                    ->join ('customers', 'customers.id', '=', 'receipts.customer')
                    ->join ('companys', 'companys.id', '=', 'receipts.company')
                    ->select ('customers.*', 'receipts.*')
                    ->where('receipts.company','=',$company)
                    ->whereBetween ('date', [$start_date, $end_date])
                    ->get ();
                //dd( $data['cost_profits'] );

                //dd($data['cost_profits']);

                $pdf = PDF::loadView ('reports/receipts_report_print', $data);
                $pdf->setPaper ('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');

            } else {
                return view ('reports/receipts_report_list', $data);
            }

        }
        return view ('reports/receipts_report_list', $data);
    }

    public function profit_loss_deposits(Request $request)
    {
        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');

        $data['company'] = intval($request->input ('company'));
        $data['companys'] = DB::table ('companys')->get ();
        $company=intval($request->input('company'));


        if (isset($start_date) && isset($end_date)) {


            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));


            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            $data['cost_profits'] = DB::table ('deposits')
                ->join ('companys', 'companys.id', '=', 'deposits.company')
                ->select ('deposits.*')
                ->where('deposits.company','=',$company)
                ->whereBetween ('date', [$start_date, $end_date])
                ->get ();

            //dd($data['sale_items']);

            if (isset($print)) {
                $start_date = $this->change_date_to_standard ($request->input ('start_date'));
                $end_date = $this->change_date_to_standard ($request->input ('end_date'));

                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));


                $data['cost_profits'] = DB::table ('deposits')
                    ->join ('companys', 'companys.id', '=', 'deposits.company')
                    ->select ('deposits.*')
                    ->where('deposits.company','=',$company)
                    ->whereBetween ('date', [$start_date, $end_date])
                    ->get ();
                //dd( $data['cost_profits'] );

                //dd($data['cost_profits']);

                $pdf = PDF::loadView ('reports/deposits_report_print', $data);
                $pdf->setPaper ('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');

            } else {
                return view ('reports/deposits_report_list', $data);
            }

        }
        return view ('reports/deposits_report_list', $data);
    }

    public function profit_loss_tickets_tourism(Request $request)
    {

        $data = [];

        $start_date = $request->input ('start_date');
        $end_date = $request->input ('end_date');
        $print = $request->input ('print');

        $data['companys'] = DB::table ('companys')->get ();

        if($request->input ('company')=="2&5"){
            $data['company'] = "2&5";

        }else{
            $data['company'] = intval($request->input ('company'));
            $company=intval($request->input('company'));
        }

        global $company_id;
        $company_id = $company;
        $company_id_class = $company;

        if (isset($start_date) && isset($end_date))
        {
            $data['d1'] = date ("d/m/Y", strtotime ($start_date));
            $data['d2'] = date ("d/m/Y", strtotime ($end_date));

            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

//            $data['cost_profits'] = DB::table ('tickets')
//                ->join ('airlines', 'airlines.id', '=', 'tickets.airline')
//                ->select ('airlines.airline_name', 'tickets.*')
//                ->where('tickets.company','=',$company)
//                ->whereBetween ('purchase_date', [$start_date, $end_date])
//                ->get ();


//            $tickets_tourism = DB::table ('airlines')
//                ->leftjoin ('tickets', 'airlines.id', '=', 'tickets.airline')
//                ->where('tickets.company','=',$company)
//                ->select (DB::raw ('count(*) as tickets_count'), 'airlines.airline_name', 'airlines.airline_tigrigna',
//                    'tickets.currency', 'airline', DB::raw ('SUM(tickets.taxable_gross) as taxable_gross'))
//                ->groupBy ('airlines.id', 'tickets.currency')
//                ->get ();


            // Query One
//            $tickets = DB::table ('airlines')
//                ->where('tickets.company','=',$company)
//                ->leftJoin ('tickets','airlines.id', '=', 'tickets.airline')
//            ->select ('airlines.airline_name','tickets.*');
//
//            // dd($tickets);
//            //
//
//           $tickets_tourism = DB::table ('airlines')
//                ->joinSub ($tickets, 'tickets', function($join){
//                    $join->on( 'airlines.id', '=', 'tickets.airline');
//                })
//                ->select (DB::raw ('count(*) as tickets_count'), 'airlines.airline_name', 'airlines.airline_tigrigna',
//                    'tickets.currency', 'airline', DB::raw ('SUM(tickets.taxable_gross) as taxable_gross'))
//                ->groupBy ('airlines.id', 'tickets.currency')
//                ->get ();


//            global $company_main;
//            $company_main=0;

//            DB::table('airlines')
//                ->join('tickets', function ($join) {
//                    $join->on('airlines.id', '=', 'tickets.airline')
//                        ->where('tickets.company','=',$company_main);
//                })
//                ->get();


            $data['cost_profits'] = DB::select('select sum(tickets.taxable_gross) as taxable_gross, tickets.airline, 
                tickets.currency,airlines.airline_tigrigna, count(*) as tickets_count,
                airlines.airline_name from airlines
                left join (select * from tickets where tickets.company='.$company.') as tickets on tickets.airline=airlines.id
                group by airlines.id,tickets.currency
');



//            $data['cost_profits'] = DB::select('select sum(tickets.taxable_gross) as taxable_gross, tickets.airline,
//                tickets.currency,airlines.airline_tigrigna, count(*) as tickets_count,
//                airlines.airline_name from airlines
//                left join tickets on tickets.airline=airlines.id
//                where tickets.company='.$company.'
//                group by airlines.id,tickets.currency
//');


//            dd($data['cost_profits']);


//            $data['cost_profits'] = $tickets_tourism;
//            dd($tickets_tourism);

           // dd($tickets_tourism);

            if (isset($print))
            {
                $start_date = $this->change_date_to_standard ($request->input ('start_date'));
                $end_date = $this->change_date_to_standard ($request->input ('end_date'));

                $data['start_date'] = $this->change_date_to_standard ($request->input ('start_date'));
                $data['end_date'] = $this->change_date_to_standard ($request->input ('end_date'));


                $data['cost_profits'] = DB::table ('tickets')
                    ->join ('customers', 'customers.id', '=', 'tickets.customer_id')
                    ->select ('customers.*', 'tickets.*')
                    ->where('tickets.company','=',$company)
                    ->whereBetween ('purchase_date', [$start_date, $end_date])
                    ->get ();


                $data['tickets_tourism'] = DB::table ('tickets_tourism')
                    ->join ('airlines', 'airlines.id', '=', 'tickets.airline')
                    ->select ('airlines.airline_name', 'count(*) as tickets_count', 'tickets.commission')
                    ->where('tickets.company','=',$company)
                    ->groupBy ('tickets.airline')
                    ->get ();

                $pdf = PDF::loadView ('reports/tickets_report_print', $data);
                $pdf->setPaper ('A4', 'landscape');
                $pdf->save (storage_path () . '_filename.pdf');
                return $pdf->stream ('sales.pdf');

            } else {
                return view ('reports/ticket_report_list_tourism', $data);
            }

        }
        return view ('reports/profit_loss_tourism_index', $data);
    }


}
