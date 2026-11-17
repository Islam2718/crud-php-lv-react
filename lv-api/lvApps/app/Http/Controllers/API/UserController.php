<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        // Check if the user is authenticated using the 'api' guard
        if (!Auth::guard('api')->check()) {
            return response()->json(['message' => 'Unauthorized!'], 401);
        }

        // Default pagination size and page
        $size = $request->input('size', 15);
        $page = $request->input('page', 1);
    
        // Search filter
        $search = $request->input('search');
    
        // Build the query
        $query = User::query();
    
        if ($search) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('firstname', 'LIKE', "%{$search}%")
                         ->orWhere('phone', 'LIKE', "%{$search}%");
            });
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

    public function store(Request $request)
    {
        if (Auth::guard('api')->check()) {
            $validator = Validator::make($request->all(), [            
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:4',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $request = $request->merge(['password' => bcrypt($request->password)]); // Hash the password()

            $user = User::create($request->all());
            return response()->json($user, 201);
        }
        return response(['data' => 'Unauthorized!', 'status' => 401], 401);
    }

    public function show($id)
    {
        if (Auth::guard('api')->check()) {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            return response()->json($user);
        }
        return response(['data' => 'Unauthorized!', 'status' => 401], 401);
    }

    public function update(Request $request, $id)
    {
        if (Auth::guard('api')->check()) {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $user->update($request->all());
            return response()->json($user);
        }
        return response(['data' => 'Unauthorized!', 'status' => 401], 401);
    }

    public function destroy($id): Response
    {
        //
        if(Auth::guard('api')->check()){
            $response = User::find($id);
            if (! $response) {
                return Response(['data' => 'Not found !.'], 404);
            }
            $response->delete();
            return Response(['data' => 'Deleted Successfully !.', 'status'=>200], 200);
        }
        return Response(['data' => 'Unauthorized!'], 401);
    }

    // additional methods 
    public function getUserByPhone($phone): Response
    {
        $response = User::where('phone', $phone)->first();
        if($response == null){
            return response(['data' => 'Not found!', 'status' => 404], 404);
        }
        return response(['data' => $response, 'status' => 200], 200);
    }
}
