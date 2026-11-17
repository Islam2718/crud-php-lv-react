<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function index(Request $request): Response
     {
         // Check if the user is authenticated using the 'api' guard
        //  if (!Auth::guard('api')->check()) {
        //      return response()->json(['message' => 'Unauthorized!'], 401);
        //  }
 
         // Default pagination size and page
         $size = $request->input('size', 15);
         $page = $request->input('page', 1);
     
         // Search filter
         $search = $request->input('search');
         $from_date = $request->input('from_date');
         $to_date = $request->input('to_date');
     
         // Build the query
         $query = Order::query()->with(['orderItems']);
     
         if ($search) {
             $query->where('invoice_no', 'LIKE', "%{$search}%");
         }

        if ($from_date && $to_date) {
            $query->whereBetween('created_at', [$from_date, $to_date]);
        }
         
         // Paginate the results
         $response = $query->paginate($size, ['*'], 'page', $page);
     
         // Format the paginated response
         return response([
             'current_page' => $response->currentPage(),
             'data' => $response->items(),
             'first_page_url' => $response->url(1),
             'from' => $response->firstItem(),
             'last_page' => $response->lastPage(),
             'last_page_url' => $response->url($response->lastPage()),
             'links' => $response->linkCollection()->toArray(),
             'next_page_url' => $response->nextPageUrl(),
             'path' => $response->path(),
             'per_page' => $response->perPage(),
             'prev_page_url' => $response->previousPageUrl(),
             'to' => $response->lastItem(),
             'total' => $response->total(),
         ], 200);
     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $invoice_no = $request->input('invoice_no', 'PPOS-' . time());
        $orderRequestArray = [
            'invoice_no' => $invoice_no,
            'customer_type' => $request->input('customer_type'),
            'total' => $request->input('total'),
            'discount' => $request->input('discount'),
            'sub_total' => $request->input('sub_total'),
            'payment' => $request->input('payment'),
            'due' => $request->input('due'),
            'vat' => $request->input('vat'),
            'tax' => $request->input('tax'),
            'customer_id' => $request->input('customer_id'),
            'store_id' => $request->input('store_id') ?? null,
            'user_id' => 1,
            'order_items' => [],
        ];
        // save request 
        $responseOrder = Order::create($orderRequestArray);
        // var_dump($responseOrder); die();
        // $orderItems = $request->input('order_items');
        // $orderItemsArray = json_decode($orderItems, true); // Pass true to get an associative array

        // if($responseOrder) {            
        //     // save order items 
        //     // $product_items = [1,2,3];

        //     // if (is_array($orderItemsArray)) {
        //     //     // Loop through the array
        //     //     foreach ($orderItemsArray as $item) {
        //     //         $orderItemsRequestArray = [
        //     //             'buy_price' => $item['buy_price'],
        //     //             'sell_price' => $item['sell_price'],
        //     //             'quantity' => $item['quantity'],
        //     //             'discount' => 0.00,
        //     //             'total' => $item['total'],
        //     //             'order_id' => $responseOrder->id,
        //     //             'product_id' => $item['product_id'],
        //     //             'user_id' => 1
        //     //         ];
        //     //         try{
        //     //             $responseOrderItems = OrderItem::create($orderItemsRequestArray);
        //     //         } catch(\Exception $e) {
        //     //             return response(['data' => $e], 404);
        //     //         }
        //     //     }
        //     // }
        // }

        return response(['data' => $responseOrder, 'status' => 201], 201);
    }
    

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id): Response
    {
        //
        // if (Auth::guard('api')->check()) {
            $response = Order::with('orderItems')->find($id);
            // $response = Order::with(['OrderItems'])->find($id);
            return response(['data' => $response, 'status' => 200], 200);
        // }
        // return response(['data' => 'Unauthorized!', 'status' => 401], 401);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    // public function edit(Order $order)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): Response
    {
        //
        // if(Auth::guard('api')->check()){ 
            $validatedData = $request->validate([
                'customer_type' => 'required',
            ]);
            $response = Order::find($id);
            if (!$response) {
                return Response(['message' => 'Not found !.', 'status'=>404], 404);
            }
            $response->update($request->all());
            return Response(['data' => $response, 'message' => 'Updated successfully!.'], 204);
        // }
        // return Response(['data' => 'Unauthorized!'], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): Response
    {
        //
        // if(Auth::guard('api')->check()){
            $response = Order::find($id);
            if (!$response) {
                return Response(['data' => 'Not found !.'], 404);
            }
            $response->delete();
            return Response(['data' => 'Deleted Successfully !.', 'status'=>200], 200);
        // }
        // return Response(['data' => 'Unauthorized!'], 401);
    }
}
