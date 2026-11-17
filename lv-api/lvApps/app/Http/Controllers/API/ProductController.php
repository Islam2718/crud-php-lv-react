<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check if the user is authenticated using the 'api' guard
        // if (!Auth::guard('api')->check()) {
        //     return response()->json(['message' => 'Unauthorized!'], 401);
        // }
    
        // Retrieve the search key from the request header
        // $searchKey = $request->header('search_key');
        $search_key = $request->input('search_key');
    
        // Start building the query
        // $query = Product::query();

        $query = Product::with(['stocks' => function ($query) {
            $query->selectRaw('product_id, SUM(quantity) as total_quantity')
                ->groupBy('product_id');
        }]);

    
        // Apply the search filter if a search key is provided
        if ($search_key) {
            $query->where(function ($q) use ($search_key) {
                $q->where('name', 'like', '%' . $search_key . '%')
                  ->orWhere('description', 'like', '%' . $search_key . '%');
            });
        }
    
        // Apply ordering
        $query->orderBy('id', 'DESC');
    
        // Get pagination parameters with sensible defaults
        $page = $request->input('page', 1); // Default page is 1 if not specified
        $size = $request->input('size', 15); // Default size is 15 if not specified
    
        // Paginate the results
        $paginatedProducts = $query->paginate($size, ['*'], 'page', $page);
    
        // Return the paginated data
        return response()->json($paginatedProducts, 200);
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): Response
    {
        if (Auth::guard('api')->check()) {
            $validatedData = $request->validate([
                'name' => 'required',
            ]);

            $response = Product::create($request->all());
            return response(['data' => $response, 'message' => 'Created Successfully!', 'status'=> 201], 201);
        }
        return response(['data' => 'Unauthorized!'], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id): Response
    {
        //
        if (Auth::guard('api')->check()) {
            $response = Product::find($id);
            if ($response) {
                return response(['data' => $response, 'status' => 200], 200);
            } else {
                return response(['data' => 'Not found!', 'status' => 404], 404);
            }
        }
        return response(['data' => 'Unauthorized!', 'status' => 401], 401);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Check if the user is authenticated
        if (!Auth::guard('api')->check()) {
            return response()->json(['message' => 'Unauthorized!'], 401);
        }
    
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            // Add more fields as needed
        ]);
    
        // Find the product by ID
        $product = Product::find($id);
    
        // If the product is not found, return a 404 response
        if (!$product) {
            return response()->json(['message' => 'Product not found!', 'status' => 404], 404);
        }
    
        // Update the product with validated data
        $product->update($validatedData);
    
        // Return a success response
        return response()->json(['data' => $product, 'message' => 'Updated successfully!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): Response
    {
        //
        if(Auth::guard('api')->check()){
            $response = Product::find($id);
            if (! $response) {
                return Response(['data' => 'Not found !.'], 404);
            }
            $response->delete();
            return Response(['data' => 'Deleted Successfully !.', 'status'=>200], 200);
        }
        return Response(['data' => 'Unauthorized!'], 401);
    }
}
