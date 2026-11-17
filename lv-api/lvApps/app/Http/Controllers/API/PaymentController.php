<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(Request $request): Response
    {
        // Check if the user is authenticated using the 'api' guard
        // if (!Auth::guard('api')->check()) {
        //     return response()->json(['message' => 'Unauthorized!'], 401);
        // }

        $response = Payment::with(['user']);
        
        // params 
        $order_id = $request->input('order_id');        
        if ($order_id !== null) { $response->where('order_id', $order_id); }
        $payment_type = $request->input('payment_type');
        if ($payment_type !== null) { $response->where('payment_type', $payment_type); }

        // Add orderBy clause to sort response by id in descending order
        $response->orderBy('id', 'desc');

        // Get pagination parameters
        $page = $request->input('page', 1); // Default page is 1 if not specified
        $size = $request->input('size', 15); // Default size is 10 if not specified

        // Search filter
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): Response
    {
        // if (Auth::guard('api')->check()) {
            $validatedData = $request->validate([
                'payment_type' => 'required',
                'items' => 'nullable|array',
            ]);

            $response = Payment::create($request->all());
            return response(['data' => $response, 'message' => 'Created Successfully!', 'status'=> 201], 201);
        // }
        // return response(['data' => 'Unauthorized!'], 401);
    }

    /**
     * Display by ID.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id): Response
    {
        //
        // if (Auth::guard('api')->check()) {
            $response = Payment::find($id);
            if ($response) {
                return response(['data' => $response, 'status' => 200], 200);
            } else {
                return response(['data' => 'Not found!', 'status' => 404], 404);
            }
        // }
        // return response(['data' => 'Unauthorized!', 'status' => 401], 401);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */

    /**
     * Update.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Check if the user is authenticated using the 'api' guard
        // if (!Auth::guard('api')->check()) {
        //     return response()->json(['data' => 'Unauthorized!'], 401);
        // }
    
        // Validate the incoming request
        $validatedData = $request->validate([
            'payment_type' => 'required',
            'items' => 'sometimes|nullable|array',
        ]);
    
        // Find the feature by ID
        $feature = Payment::find($id);
    
        // If the feature is not found, return a 404 response
        if (!$feature) {
            return response()->json(['message' => 'Not found!', 'status' => 404], 404);
        }
    
        // Update the feature with validated data
        $feature->update($validatedData);
    
        // Return a success response
        return response()->json(['data' => $feature, 'message' => 'Updated successfully!'], 200);
    }
    

    /**
     * Remove.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): Response
    {
        //
        if(Auth::guard('api')->check()){
            $response = Payment::find($id);
            if (! $response) {
                return Response(['data' => 'Not found !.'], 404);
            }
            $response->delete();
            return Response(['data' => 'Deleted Successfully !.', 'status'=>200], 200);
        }
        return Response(['data' => 'Unauthorized!'], 401);
    }    
}
