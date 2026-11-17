<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Barcode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StockController extends Controller
{
    /**
     * Display.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): Response
    {
        // Check if the user is authenticated using the 'api' guard
        // if (!Auth::guard('api')->check()) {
        //     return response()->json(['message' => 'Unauthorized!'], 401);
        // }

        $response = Stock::with(['product', 'user']);
        
        // params 
        $stock_id = $request->input('stock_id');
        $product_id = $request->input('product_id');

        if ($product_id !== null) { $response->where('product_id', $product_id); }
        if ($stock_id !== null) { $response->where('id', $stock_id); }

        // Add orderBy clause to sort response by id in descending order
        $response->orderBy('id', 'desc');

        // Get pagination parameters
        $page = $request->input('page', 1); // Default page is 1 if not specified
        $size = $request->input('size', 15); // Default size is 10 if not specified
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        if ($from_date && $to_date) {
            $response->whereBetween('created_at', [$from_date, $to_date]);
        }        

        // Paginate the results
        $response = $response->paginate($size, ['*'], 'page', $page);
        return response([
            'current_page' => $response->currentPage(),
            'data' => $response->items(),
            'first_page_url' => $response->url(1),
            'from' => $response->firstItem(),
            'last_page' => $response->lastPage(),
            'last_page_url' => $response->url($response->lastPage()),
            'links' => $response->linkCollection()->toArray(), // Use Laravel's pagination links
            'next_page_url' => $response->nextPageUrl(),
            'path' => $response->path(),
            'per_page' => $response->perPage(),
            'prev_page_url' => $response->previousPageUrl(),
            'to' => $response->lastItem(),
            'total' => $response->total(),
        ], 200);
        // }
    
        // return response(['data' => 'Unauthorized!', 'status' => 401], 401);
    }

    /**
     * Store
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::guard('api')->check()) {
            $validatedData = $request->validate([
                'type' => 'required',
            ]);

            $response = Stock::create($request->all());
            $this->addBarcode($response);
            // die();
            return response(['data' => $response, 'message' => 'Created Successfully!', 'status'=> 201], 201);
        }
        return response(['data' => 'Unauthorized!'], 401);
    }

    /**
     * Display
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function show($id): Response
    {
        //
        if (Auth::guard('api')->check()) {
            $response = Stock::with('user', 'product')->find($id);
            return response(['data' => $response, 'status' => 200], 200);
        }
        return response(['data' => 'Unauthorized!', 'status' => 401], 401);
    }

    /**
     * Update.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        if (!Auth::guard('api')->check()) {
            return Response(['data' => 'Unauthorized!'], 401);
        }
        // var_dump($request->all()); die(); 
        $validatedData = $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required'
        ]);

        $response = Stock::find($id);
        if (! $response) {
            return Response(['data' => 'Not found !.'], 404);
        }
        $response->update($request->all());
        // Barcode::where('stock_id', $id)->delete();
        $this->addBarcode($response);
        return response()->json(['data' => $response, 'message' => 'Updated successfully!'], 200);
    }

    /**
     * Remove.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): Response
    {
        //
        if(Auth::guard('api')->check()){
            $response = Stock::find($id);
            if (! $response) {
                return Response(['data' => 'Not found !.'], 404);
            }
            // Barcode::where('stock_id', $id)->delete();
            $response->delete();            
            
            return Response(['data' => 'Deleted Successfully !.', 'status'=>200], 200);
        }
        return Response(['data' => 'Unauthorized!'], 401);
    }
}
