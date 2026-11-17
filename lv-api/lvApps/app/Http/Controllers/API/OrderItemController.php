<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class OrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): Response
    {
        // Check if the user is authenticated using the 'api' guard
        // if (!Auth::guard('api')->check()) {
        //     return response()->json(['message' => 'Unauthorized!'], 401);
        // }

        // Default pagination size and page
        $size = $request->input('size', 15);
        $page = $request->input('page', 1);
        // get by order id params 
        $order_id = $request->input('order_id');
    
        // Build the query
        $query = OrderItem::query();
        if ($order_id) {
            $query->where('order_id', '=', $order_id);
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
        // if (Auth::guard('api')->check()) {
            $validatedData = $request->validate([
                'order_id' => 'required',
            ]);

            $response = OrderItem::create($request->all());
            return response(['data' => $response, 'message' => 'Created Successfully!', 'status'=> 201], 201);
        // }
        // return response(['data' => 'Unauthorized!'], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
     */
    public function show($id): Response
    {
        //
        if (Auth::guard('api')->check()) {
            $response = OrderItem::with(['user', 'company','product','customer'])->find($id);
            return response(['data' => $response, 'status' => 200], 200);
        }
        return response(['data' => 'Unauthorized!', 'status' => 401], 401);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
     */
    // public function edit(OrderItem $orderItem)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): Response
    {
        //
        if(Auth::guard('api')->check()){ 
            $validatedData = $request->validate([
                'order_id' => 'required',
            ]);
            $response = OrderItem::find($id);
            if (!$response) {
                return Response(['message' => 'Not found !.', 'status'=>404], 404);
            }
            $response->update($request->all());
            return Response(['data' => $response, 'message' => 'Updated successfully!.'], 204);
        }
        return Response(['data' => 'Unauthorized!'], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): Response
    {
        //
        // if(Auth::guard('api')->check()){
            $response = OrderItem::find($id);
            if (! $response) {
                return Response(['data' => 'Not found !.'], 404);
            }
            $response->delete();
            return Response(['data' => 'Deleted Successfully !.', 'status'=>200], 200);
        // }
        // return Response(['data' => 'Unauthorized!'], 401);
    }
}
