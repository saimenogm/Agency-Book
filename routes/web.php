
<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group( function(){

   Route::get('/','HomeController@index')->name('home');

    // Suppliers
    Route::get('/suppliers', 'SupplierController@index')->name('supplier_index');
    Route::get('/suppliers/new/', 'SupplierController@createSupplier')->name('new_supplier');
    Route::get('/suppliers/{supplier_id}', 'SupplierController@show')->name('show_supplier');
    Route::get('/suppliers/del/{id}', 'SupplierController@delete_supplier')->name('del_supplier');
    Route::post('/suppliers/new', 'SupplierController@newSupplier')->name('create_supplier');
    Route::post('/suppliers/{supplier_id}', 'SupplierController@modify')->name('update_supplier');



//customers
Route::post('/customers/new', 'CustomerController@newCustomer')->name('create_customer');
Route::get('/customers/new', 'CustomerController@createCustomer')->name('new_customer');
Route::get('/customers', 'CustomerController@index')->name('customer_index');
Route::get('/customers/report/', 'CustomerController@reporter')->name('customers_report');
Route::get('/customers/{customer_id}', 'CustomerController@show')->name('show_customer');
Route::post('/customers/del/{customer_id}', 'CustomerController@delete_customer')->name('del_customer');
Route::get('/customers/del/{customer_id}', 'CustomerController@delete_customer')->name('del_customer_get');
Route::post('/customers/{customer_id}', 'CustomerController@modify')->name('update_customer');

Route::get('/sales/new_sale/{customer_id}', 'SaleController@createSaleCustomer')->name('new_sale_customer');


Route::get('/sales/new_sale_from_order/{sales_id}', 'SaleController@createSaleFromOrder')->name('new_sale_from_order');

//Companies
Route::get('/companys', 'CompanyController@index')->name('company_index');
Route::post('/companys/new', 'CompanyController@newCompany')->name('create_company');
Route::get('/companys/new', 'CompanyController@createCompany')->name('new_company');
Route::get('/companys/{company_id}', 'CompanyController@show')->name('show_company');
Route::post('/companys/{company_id}', 'CompanyController@modify')->name('update_company');


//Companies
Route::get('/expense_categorys', 'CategoryController@index')->name('category_index');
Route::post('/expense_categorys/new', 'CategoryController@newCategory')->name('create_expense_category');
Route::get('/expense_categorys/new', 'CategoryController@createCategory')->name('new_expense_categorys');
Route::get('/expense_categorys/{category_id}', 'CategoryController@show')->name('show_expense_category');
Route::post('/expense_categorys/{category_id}', 'CategoryController@modify')->name('update_expense_category');


//Sales
Route::get('/sales', 'SaleController@index')->name('sales_index');
Route::get('/credit_sales', 'SaleController@credit_index')->name('credit_sales_index');
Route::post('/sales/new_sale', 'SaleController@createSale')->name('create_sale');
Route::get('/sales/new_sale', 'SaleController@createSale')->name('create_sale');    
Route::get('/sales/new_sale_get', 'SaleController@newSale')->name('finish_sale');



Route::get('/sales/new_sale_angular', 'SaleController@createSaleAngular')->name('create_sale_angular');
    Route::post('/ajaxRequestAngular', 'SaleController@newAgencySaleAngular')->name('new_sale_angular');
    Route::get('/ajaxRequestAngular', 'SaleController@newAgencySaleAngular')->name('new_sale_angular');

Route::post('/ajaxRequestOrderAngular', 'SaleController@newAgencySaleOrderAngular')->name('new_sale_order_angular');
Route::get('/ajaxRequestOrderAngular', 'SaleController@newAgencySaleOrderAngular')->name('new_sale_order_angular');

Route::get('/get_hotel_price', 'SaleController@getRoomPrice')->name('get_hotel_price');



    Route::get('/sales_order', 'SaleController@index_order')->name('sales_order_index');
Route::get('/sales/new_sale_order', 'SaleController@createSaleOrder')->name('create_sale_order');
Route::get('/sales/new_sale_order_customer/{customer_id}', 'SaleController@createSaleOrderCustomer')->name('new_sale_order_customer');

Route::get('/sales/{sales_id}', 'SaleController@show')->name('show_sale');

Route::get('/sales_order/{sales_id}', 'SaleController@showOrder')->name('show_sale_order');

Route::get('/sales/customer_history/{customer_id}', 'SaleController@showCustomerHistory')->name('customer_history');

Route::get('/sales_order_active', 'SaleController@index_order_active')->name('sales_order_index_active');


Route::post('/ajaxRequest2', 'SaleController@newAgencySale')->name('new_sale_ajaxs');
Route::get('/ajaxRequest2', 'SaleController@newAgencySale')->name('new_sale_ajaxs');


Route::post('/ajaxRequest3', 'SaleController@newAgencySaleOrder')->name('new_sale_ajaxs_order');
Route::get('/ajaxRequest3', 'SaleController@newAgencySaleOrder')->name('new_sale_ajaxs_order');



Route::post('/ajaxRequest4', 'SaleController@showItem')->name('fill_item');
Route::get('/ajaxRequest4', 'SaleController@showItem')->name('fill_item');


Route::post('/ajaxRequest5', 'SaleController@updateItem')->name('update_item');
Route::get('/ajaxRequest5', 'SaleController@updateItem')->name('update_item');


Route::post('/ajaxRequest6', 'SaleController@showItemOrder')->name('fill_item_order');
Route::get('/ajaxRequest6', 'SaleController@showItemOrder')->name('fill_item_order');


Route::post('/ajaxRequest7', 'SaleController@updateItemOrder')->name('update_item_order');
Route::get('/ajaxRequest7', 'SaleController@updateItemOrder')->name('update_item_order');

Route::post('/ajaxRequest8', 'SaleController@updateItemOrderVisa')->name('update_item_order_visa');
Route::get('/ajaxRequest8', 'SaleController@updateItemOrderVisa')->name('update_item_order_visa');

Route::post('/ajaxRequest9', 'SaleController@updateItemVisa')->name('update_item_visa');
Route::get('/ajaxRequest9', 'SaleController@updateItemVisa')->name('update_item_visa');

Route::post('/ajaxRequest10', 'SaleController@updateItemOrderGuest')->name('update_item_order_guest');
Route::get('/ajaxRequest10', 'SaleController@updateItemOrderGuest')->name('update_item_order_guest');

Route::post('/ajaxRequest11', 'SaleController@updateItemGuest')->name('update_item_guest');
Route::get('/ajaxRequest11', 'SaleController@updateItemGuest')->name('update_item_guest');


//Customers

Route::get('/customer/add_feq', 'CustomerController@add_feq')->name('add_feq');
Route::get('/customer/del_freq', 'CustomerController@del_freq')->name('del_freq');



//Expenses

Route::post('/expenses/new', 'ExpenseController@newExpense')->name('create_expense');
Route::get('/expenses/new', 'ExpenseController@createExpense')->name('new_expense');
Route::get('/expenses/report/', 'ExpenseController@reporter')->name('expenses_report_get');
Route::post('/expenses/report/', 'ExpenseController@reporter')->name('expenses_report');
Route::post('/expenses/{expense_id}', 'ExpenseController@modify')->name('update_expense');
Route::get('/expenses', 'ExpenseController@index')->name('expense_index');
Route::get('/expenses/edit/{expense_id}', 'ExpenseController@edit')->name('edit_expense');
Route::get('/expenses/{expense_id}', 'ExpenseController@show')->name('show_expense');
Route::get('/expenses/del/{expense_id}', 'ExpenseController@delete_expense')->name('delete_expense');


Route::get('/companys', 'CompanyController@index')->name('company_index');
Route::post('/companys/new', 'CompanyController@newCompany')->name('create_company');
Route::get('/companys/new', 'CompanyController@createCompany')->name('new_company');
Route::get('/companys/{company_id}', 'CompanyController@show')->name('show_company');
Route::post('/companys/{company_id}', 'CompanyController@modify')->name('update_company');


// Payment
Route::post('/paymentspos/new/', 'PaymentController@newPaymentPOS')->name('create_paymentPOS');
Route::get('/paymentspos/new/', 'PaymentController@createPaymentPOS')->name('new_paymentPOS');

Route::post('/paymentsales/new/', 'PaymentController@newPaymentSales')->name('create_paymentSales');
Route::get('/paymentsales/new/', 'PaymentController@createPaymentSales')->name('new_paymentSales');
Route::get('/paymentsales/new_pay', 'PaymentController@createPaymentSales2')->name('new_paymentSales2');
Route::get('/paymentsales', 'PaymentController@index_sales_payment')->name('index_sales_payment');

//Expenses
Route::post('/expenses/new', 'ExpenseController@newExpense')->name('create_expense');
Route::get('/expenses/new', 'ExpenseController@createExpense')->name('new_expense');
Route::get('/expenses/report/', 'ExpenseController@reporter')->name('expenses_report_get');
Route::post('/expenses/report/', 'ExpenseController@reporter')->name('expenses_report');
Route::post('/expenses/{expense_id}', 'ExpenseController@modify')->name('update_expense');
Route::get('/expenses', 'ExpenseController@index')->name('expense_index');
Route::get('/expenses/{expense_id}', 'ExpenseController@show')->name('show_expense');


Route::get('/report/profit_loss_report/', 'SaleController@profit_loss')->name('profit_loss_report');
Route::post('/report/profit_loss_report/', 'SaleController@profit_loss')->name('profit_loss_report');

Route::resource('bank_transfers','BankTransferController');


Route::get('/report/profit_loss_tickets_report/', 'SaleController@profit_loss_tickets')->name('profit_loss_tickets_report');
Route::post('/report/profit_loss_tickets_report/', 'SaleController@profit_loss_tickets')->name('profit_loss_tickets_report');

Route::get('/report/profit_loss_tickets_external/', 'SaleController@profit_loss_tickets_external')->name('profit_loss_tickets_external');
Route::post('/report/profit_loss_tickets_external/', 'SaleController@profit_loss_tickets_external')->name('profit_loss_tickets_external');

Route::get('/report/profit_loss_overall_report/', 'SaleController@profit_loss_overall')->name('profit_loss_overall');
Route::post('/report/profit_loss_overall_report/', 'SaleController@profit_loss_overall')->name('profit_loss_overall');


Route::get('/report/profit_loss_overall_external_report/', 'SaleController@profit_loss_overall_external')->name('profit_loss_overall_external');
Route::post('/report/profit_loss_overall_external_report/', 'SaleController@profit_loss_overall_external')->name('profit_loss_overall_external');

Route::get('/report/profit_loss_visas_report/', 'SaleController@profit_loss_visas')->name('profit_loss_visas_report');
Route::post('/report/profit_loss_visas_report/', 'SaleController@profit_loss_visas')->name('profit_loss_visas_report');

Route::get('/report/profit_loss_expenses/', 'SaleController@profit_loss_expenses')->name('profit_loss_expenses');
Route::post('/report/profit_loss_expenses/', 'SaleController@profit_loss_expenses')->name('profit_loss_expenses');



Route::get('/report/profit_loss_guest_houses/', 'SaleController@profit_loss_guest_houses')->name('profit_loss_guest_houses');
Route::post('/report/profit_loss_guest_houses/', 'SaleController@profit_loss_guest_houses')->name('profit_loss_guest_houses');


Route::get('/report/profit_loss_receipts/', 'SaleController@profit_loss_receipts')->name('profit_loss_receipts');
Route::post('/report/profit_loss_receipts/', 'SaleController@profit_loss_receipts')->name('profit_loss_receipts');


Route::get('/report/profit_loss_deposits/', 'SaleController@profit_loss_deposits')->name('profit_loss_deposits');
Route::post('/report/profit_loss_deposits/', 'SaleController@profit_loss_deposits')->name('profit_loss_deposits');


Route::get('/report/profit_loss_tourism/', 'SaleController@profit_loss_tickets_tourism')->name('profit_loss_tickets_tourism');
Route::post('/report/profit_loss_tourism/', 'SaleController@profit_loss_tickets_tourism')->name('profit_loss_tickets_tourism');



// Receipt
Route::post('/receiptsales/new/', 'ReceiptController@newReceiptSales')->name('create_receiptSales');
Route::get('/receiptsales/new/', 'ReceiptController@createReceiptSales')->name('new_receiptSales');
Route::get('/receiptsales/new_pay', 'ReceiptController@createReceiptSales2')->name('new_receiptSales2');
Route::get('/receiptsales', 'ReceiptController@index_sales_receipt')->name('index_sales_receipt');
Route::get('/receipt/{receipt_id}', 'ReceiptController@show')->name('show_reciept');


Route::post('/paymentpurchases/new/{purchases_id}', 'PaymentController@newPaymentPurchases')->name('create_paymentPurchases');
Route::get('/paymentpurchases/new/{purchases_id}', 'PaymentController@createPaymentPurchases')->name('new_paymentPurchases');
Route::get('/paymentpurchases', 'PaymentController@index_purchase_payment')->name('index_purchases_payment');
Route::get('/paymentpurchases/{payment_id}', 'PaymentController@cancelSupplierPurchaces')->name('cancel_payment');
Route::get('/paymentspos/new_', 'PaymentController@new_paymentPurchase2')->name('new_paymentPurchase2');

//Bank Account
Route::resource('bank_accounts','BankAccountController');
Route::resource('deposits','DepositController');
Route::resource('payments','PaymentController');
Route::get('/payments/create/retrive_expenses', 'PaymentController@payment_ajax')->name('payment_ajax');
Route::resource('airlines','AirlinesController');

Route::resource('rooms','RoomsController');
Route::resource('items','ItemsController');
Route::resource('places','PlacesController');
Route::resource('users','UsersController');

Route::resource('receipts','ReceiptController');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/tickets', 'SaleController@tickets_index')->name('tickets_index');
Route::get('/visas', 'SaleController@visas_index')->name('visas_index');


Route::post('/sales/void_sale', 'SaleController@voidSale')->name('void_sale');

Route::post('/sales/void_sale_order', 'SaleController@voidSaleOrder')->name('void_sale_order');


Route::resource('rooms','RoomController');
Route::resource('categorys','RoomCategorysController');
Route::resource('item_categorys','ItemCategoryController');
Route::resource('items','ItemsController');
Route::resource('places','PlacesController');
Route::get('/receipt/approve/{receipt_id}', 'ReceiptController@approve_receipt')->name('approve_receipt');


//notification

    Route::get('/notifications/all_tickets_notification', 'HomeController@all_ticket_orders')->name('all_ticket_orders');
    Route::get('/notifications/tickets_notification', 'HomeController@notification_ticket')->name('booking_warnings');
    Route::get('/notifications/expense_notification', 'HomeController@notification_index')->name('expense_index_notification');

//Expenses

    Route::get('/expenses', 'ExpenseController@index')->name('expense_index');
//    Route::get('/expenses/expense_notification', 'HomeController@notification_index')->name('expense_index_notification');

    Route::resource('visaSuppliers','VisaSupplierController');


});

Auth::routes(["verify"=>false]);

