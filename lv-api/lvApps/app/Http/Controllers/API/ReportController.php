<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
// use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Barcode;
use App\Models\Order;
use App\Models\Store;
// use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    //
    public function dashboard(Request $request): Response
    {
        // Retrieve the companyId from the request header        
        $searchKey = $request->header('search-key');

        // if (Auth::guard('api')->check()) {            
            $no_of_card = Barcode::count();
            $no_of_card_0 = Barcode::where('status', 0)->count();
            $no_of_card_1 = Barcode::where('status', 1)->count();
            $no_of_card_2 = Barcode::where('status', 2)->count();
            $no_of_card_3 = Barcode::where('status', 3)->count();
            $no_of_card_4 = Barcode::where('status', 4)->count();
            $no_of_card_5 = Barcode::where('status', 5)->count();
            $no_of_card_6 = Barcode::where('status', 6)->count();
            $no_of_card_7 = Barcode::where('status', 7)->count();
            $no_of_card_4_today = Barcode::where('status', 4)->whereDate('updated_at', date('Y-m-d'))->count();
                     
            $no_of_store = Store::count();     
            $no_of_product = Product::count();
            $no_of_sales = Order::count();
            $amount_of_sales = Order::sum('total');            
            $no_of_sales_by_wholesale = Order::where('customer_type', 'WHOLESALE')->count();
            $amount_of_sales_by_wholesale = Order::where('customer_type', 'WHOLESALE')->sum('total');
            $no_of_sales_by_regular = Order::where('customer_type', 'REGULAR')->count();
            $amount_of_sales_by_regular = Order::where('customer_type', 'REGULAR')->sum('total');            
            $no_of_due = Order::where('due', '<', 0)->count();
            $amount_of_due = Order::sum('due');            

            $dashboardData = array(
                'no_of_store'=> $no_of_store ? $no_of_store : 0,
                'no_of_product'=> $no_of_product ? $no_of_product : 0,
                'no_of_alert_product'=> 0,

                'no_of_sales'=> $no_of_sales ? $no_of_sales : 0,
                'amount_of_sales'=> $amount_of_sales ? $amount_of_sales : 0,
                'no_of_sales_by_wholesale'=> $no_of_sales_by_wholesale ? $no_of_sales_by_wholesale : 0,
                'amount_of_sales_by_wholesale'=> $amount_of_sales_by_wholesale ? $amount_of_sales_by_wholesale : 0,
                'no_of_sales_by_regular'=> $no_of_sales_by_regular ? $no_of_sales_by_regular : 0,
                'amount_of_sales_by_regular'=> $amount_of_sales_by_regular ? $amount_of_sales_by_regular : 0,                
                'no_of_due'=> $no_of_due ? $no_of_due : 0,
                'amount_of_due'=> $amount_of_due ? $amount_of_due : 0,

                'no_of_card'=> $no_of_card ? $no_of_card : 0,
                'no_of_card_0'=> $no_of_card_0 ? $no_of_card_0 : 0,
                'no_of_card_1'=> $no_of_card_1 ? $no_of_card_1 : 0,
                'no_of_card_2'=> $no_of_card_2 ? $no_of_card_2 : 0,
                'no_of_card_3'=> $no_of_card_3 ? $no_of_card_3 : 0,
                'no_of_card_4'=> $no_of_card_4 ? $no_of_card_4 : 0,
                'no_of_card_5'=> $no_of_card_5 ? $no_of_card_5 : 0,
                'no_of_card_6'=> $no_of_card_6 ? $no_of_card_6 : 0,
                'no_of_card_7'=> $no_of_card_7 ? $no_of_card_7 : 0,
                'no_of_card_4_today'=> $no_of_card_4_today ? $no_of_card_4_today : 0,
            );

            return response(['data' => $dashboardData, 'status' => 200], 200);
        // }
        // return response(['data' => 'Unauthorized!', 'status' => 401], 401);
    }
    public function due(Request $request): Response
    {
        // Retrieve the companyId from the request header
        $searchKey = $request->header('search-key');

        if (Auth::guard('api')->check()) {
            $orders = Order::with(['user']);

            $orders->where('due', '<', 0);
            $orders->orderBy('id', 'desc');
            // Paginate the results
            $page = $request->input('page') ?? 1; // Default page is 1 if not specified
            $size = $request->input('size') ?? 10; // Default size is 10 if not specified
            $paginatedOrders = $orders->paginate($size, ['*'], 'page', $page);

            return response(['data' => $paginatedOrders, 'status' => 200], 200);
        }
        return response(['data' => 'Unauthorized!', 'status' => 401], 401);
    }
}
