<?php

namespace App\Http\Controllers;

use App\Helper\ValidationService;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/users",
 *     summary="Get paginated list of users",
 *     tags={"User"},
 *     @OA\Parameter(name="_pageLimit", in="query", required=false, @OA\Schema(type="integer"), example=10),
 *     @OA\Parameter(name="_pageSize", in="query", required=false, @OA\Schema(type="integer"), example=1),
 *     @OA\Response(response=200, description="Paginated users fetched successfully")
 * )
 */
    function getPagination(Request $request){
        $limit = $request->query('_pageLimit',10);
        $page = $request->query('_pageSize',1);

        $users = User::paginate($limit, ['*'], 'page', $page);

        return response()->json([
        'status' => 'success',
        'current_page' => $users->currentPage(),
        'total_pages' => $users->lastPage(),
        'total_items' => $users->total(),
        'per_page' => $users->perPage(),
        'data' => $users->items(),
        'status_code' => 200
    ]);
    }

    /**
 * @OA\Get(
 *     path="/api/user/{id}",
 *     summary="Get user by ID",
 *     tags={"User"},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="User found"),
 *     @OA\Response(response=404, description="User not found")
 * )
 */
    function getById($id){
        $user = User::find($id);
        if ($user) {
        return response()->json([
            'status' => 'success',
            'data' => $user,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "User with ID {$id} not found",
            'status_code' => 404
        ]);
    }
    }


/**
 * @OA\Post(
 *     path="/api/user/create",
 *     summary="Create a new user",
 *     tags={"User"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "password", "staff_id"},
 *             @OA\Property(property="name", type="string", example="admin"),
 *             @OA\Property(property="password", type="string", example="secret123"),
 *             @OA\Property(property="staff_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(response=200, description="User created successfully"),
 *     @OA\Response(response=422, description="Validation failed")
 * )
 */
    function saveUser(Request $request){

        $rules = [
        'name'    => 'required|string|max:255' ,
        'password'    => 'required|string|max:255' ,        
        'staff_id' => 'required|integer|exists:staff,id'               
        ];

        $messages = [
        'name.required' => 'User name is required.',
        'name.string' => 'User name must be a valid string.',
        'name.max' => 'User name cannot exceed 255 characters.',
        'password.required' => 'Password is required.',
        'password.string' => 'Password must be a valid string.',
        'password.max' => 'Password cannot exceed 255 characters.',        
        'staff_id.required' => 'Staff ID is required.',
        'staff_id.integer'  => 'Staff ID must be an integer.',
        'staff_id.exists'   => 'The selected staff ID does not exist.'
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }  
        $user = new User();
        $user -> name = $request->name; 
        $user -> password = $request->password; 
        $user -> staff_id = $request->staff_id; 
        $user -> save();
        return response() -> json([
            'status' => 'success',
            'new_user' => $user,
            'status_code' => 200
        ]);
    }

    /**
 * @OA\Post(
 *     path="/api/user/update",
 *     summary="Update an existing user",
 *     tags={"User"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id", "name", "password", "staff_id"},
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="new_admin"),
 *             @OA\Property(property="password", type="string", example="newpass123"),
 *             @OA\Property(property="staff_id", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(response=200, description="User updated successfully"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */
    function updateUser(Request $request){
        $user = User::find($request->id);
        if ($user!= null){
        $rules = [
        'name'    => 'required|string|max:255' ,
        'password'    => 'required|string|max:255' ,        
        'staff_id' => 'required|integer|exists:staff,id'               
        ];

        $messages = [
        'name.required' => 'User name is required.',
        'name.string' => 'User name must be a valid string.',
        'name.max' => 'User name cannot exceed 255 characters.',
        'password.required' => 'Password is required.',
        'password.string' => 'Password must be a valid string.',
        'password.max' => 'Password cannot exceed 255 characters.',        
        'staff_id.required' => 'Staff ID is required.',
        'staff_id.integer'  => 'Staff ID must be an integer.',
        'staff_id.exists'   => 'The selected staff ID does not exist.'
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }        
       
        $user -> name = $request->name; 
        $user -> password = $request->password; 
        $user -> staff_id = $request->staff_id; 
        $user -> save();
        }
        return response()->json([
            'status'=> 'success',
            'updated_data'=>$user,
            'status_code'=>200
        ]);
    }

    /**
 * @OA\Post(
 *     path="/api/user/delete/force",
 *     summary="Permanently delete a soft-deleted user",
 *     tags={"User"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id"},
 *             @OA\Property(property="id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(response=200, description="User permanently deleted"),
 *     @OA\Response(response=404, description="User not found in trash")
 * )
 */

    function forceDeleteUser(Request $request){
    // Only look into trashed records
    $user = User::onlyTrashed()->find($request->id);

    if ($user) {
        $user->forceDelete(); // permanently deletes from DB

        return response()->json([
            'status' => 'success',
            'message' => "User with ID {$request->id} permanently deleted.",
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "User with ID {$request->id} not found in trash.",
            'status_code' => 404
        ]);
    }
}

/**
 * @OA\Post(
 *     path="/api/user/delete/soft",
 *     summary="Soft delete a user",
 *     tags={"User"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id"},
 *             @OA\Property(property="id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(response=200, description="User soft deleted successfully"),
 *     @OA\Response(response=404, description="User not found")
 * )
 */
function softDeleteUser(Request $request){    
    $user = User::find($request->id);
    if ($user !== null) {
        $user->delete(); // Soft delete (sets deleted_at)
        return response()->json([
            'status' => 'success',
            'deleted_data' => $user,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "User with ID {$request->id} not found or already deleted!",
            'status_code' => 404
        ]);
    }
}

/**
 * @OA\Post(
 *     path="/api/user/restore/{id}",
 *     summary="Restore a soft-deleted user",
 *     tags={"User"},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
 *     @OA\Response(response=200, description="User restored successfully"),
 *     @OA\Response(response=404, description="User not found in trash")
 * )
 */
function restoreUser($id){
    $user = User::onlyTrashed()->find($id);

    if ($user) {
        $user->restore();
        return response()->json([
            'status' => 'success',
            'restored_data' => $user,
            'status_code' => 200
        ]);
    }

    return response()->json([
        'status' => "User with ID {$id} not found in trash.",
        'status_code' => 404
    ]);
}
}
