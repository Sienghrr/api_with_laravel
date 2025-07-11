<?php

namespace App\Http\Controllers;

use App\Helper\ValidationService;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{

    /**
 * @OA\Get(
 *     path="/api/staffs",
 *     summary="Get paginated list of staff",
 *     tags={"Staff"},
 *     @OA\Parameter(name="_pageLimit", in="query", required=false, @OA\Schema(type="integer")),
 *     @OA\Parameter(name="_pageSize", in="query", required=false, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Successful pagination response")
 * )
 */
    function getPagination(Request $request){
        $limit = $request->query('_pageLimit',10);
        $page = $request->query('_pageSize',1);

        $staffs = Staff::paginate($limit, ['*'], 'page', $page);

        return response()->json([
        'status' => 'success',
        'current_page' => $staffs->currentPage(),
        'total_pages' => $staffs->lastPage(),
        'total_items' => $staffs->total(),
        'per_page' => $staffs->perPage(),
        'data' => $staffs->items(),
        'status_code' => 200
    ]);
    }

   /**
 * @OA\Get(
 *     path="/api/staff/{id}",
 *     summary="Get staff by ID",
 *     tags={"Staff"},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Staff found"),
 *     @OA\Response(response=404, description="Staff not found")
 * )
 */
    function getById($id){
        $staff = Staff::find($id);
        if ($staff) {
        return response()->json([
            'status' => 'success',
            'data' => $staff,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Staff with ID {$id} not found",
            'status_code' => 404
        ]);
    }
    }


/**
 * @OA\Post(
 *     path="/api/staff/create",
 *     summary="Create a new staff member",
 *     tags={"Staff"},
 *     description="Creates a new staff record with validated fields.",
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"position_id", "name", "dob", "pob", "address", "phone_number", "nation_id_card"},
 *             @OA\Property(property="position_id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="gender", type="string", example="Male", nullable=true),
 *             @OA\Property(property="dob", type="string", example="1990-01-01"),
 *             @OA\Property(property="pob", type="string", example="Phnom Penh"),
 *             @OA\Property(property="address", type="string", example="Street 123, Phnom Penh"),
 *             @OA\Property(property="phone_number", type="string", example="012345678"),
 *             @OA\Property(property="nation_id_card", type="string", example="N123456789")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Staff created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="new_staff", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="position_id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="gender", type="string", example="Male"),
 *                 @OA\Property(property="dob", type="string", example="1990-01-01"),
 *                 @OA\Property(property="pob", type="string", example="Phnom Penh"),
 *                 @OA\Property(property="address", type="string", example="Street 123"),
 *                 @OA\Property(property="phone_number", type="string", example="012345678"),
 *                 @OA\Property(property="nation_id_card", type="string", example="N123456789"),
 *                 @OA\Property(property="created_at", type="string", example="2025-07-11T12:00:00.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", example="2025-07-11T12:00:00.000000Z")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="position_id", type="array", @OA\Items(type="string", example="The selected position ID does not exist.")),
 *             @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
 *             @OA\Property(property="status_code", type="integer", example=422)
 *         )
 *     )
 * )
 */

    function saveStaff  (Request $request){

        $rules = [
        'position_id'    => 'required|integer|exists:position,id'     ,
        'name'       =>'required|string|max:255',
        'gender'     => 'string|nullable',
        'dob' => 'required|string',
        'pob' => 'required|string',
        'address' => 'required|string',
        'phone_number' => 'required|string',
        'nation_id_card' => 'required|string'
        ];

        $messages = [
        'position_id.required' => 'Position ID is required.',
        'position_id.integer'  => 'Position ID must be an integer.',
        'position_id.exists'   => 'The selected position ID does not exist.',
        'name.required'    => 'Staff name is not allowed to be null.',
        'dob.required'       => 'dob is not allowed to be null.',
        'pob.required'       => 'pob is not allowed to be null.',
        'address.required'       => 'Address is not allowed to be null.',
        'phone_number.required'       => 'Phone_number is not allowed to be null.',
        'nation_id_card.required' => 'Nation_id_card is not allowed to be null.',
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }  
        $staff = new Staff();
        $staff -> position_id = $request->position_id;
        $staff -> name = $request -> name; 
        $staff -> gender = $request->gender;
        $staff -> dob = $request -> dob;
        $staff -> pob = $request->pob;
        $staff -> address = $request -> address; 
        $staff -> phone_number = $request->phone_number;
        $staff -> nation_id_card = $request -> nation_id_card;         
        $staff -> save();
        return response() -> json([
            'status' => 'success',
            'new_staff' => $staff,
            'status_code' => 200
        ]);
    }

  /**
 * @OA\Post(
 *     path="/api/staff/update",
 *     summary="Update an existing staff member",
 *     tags={"Staff"},
 *     description="Updates staff data by ID",
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id", "position_id", "name", "dob", "pob", "address", "phone_number", "nation_id_card"},
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="position_id", type="integer", example=2),
 *             @OA\Property(property="name", type="string", example="Jane Smith"),
 *             @OA\Property(property="gender", type="string", example="Female", nullable=true),
 *             @OA\Property(property="dob", type="string", example="1995-05-15"),
 *             @OA\Property(property="pob", type="string", example="Siem Reap"),
 *             @OA\Property(property="address", type="string", example="Street 456, Siem Reap"),
 *             @OA\Property(property="phone_number", type="string", example="098765432"),
 *             @OA\Property(property="nation_id_card", type="string", example="ID987654321")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Staff updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="updated_data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="position_id", type="integer", example=2),
 *                 @OA\Property(property="name", type="string", example="Jane Smith"),
 *                 @OA\Property(property="gender", type="string", example="Female"),
 *                 @OA\Property(property="dob", type="string", example="1995-05-15"),
 *                 @OA\Property(property="pob", type="string", example="Siem Reap"),
 *                 @OA\Property(property="address", type="string", example="Street 456, Siem Reap"),
 *                 @OA\Property(property="phone_number", type="string", example="098765432"),
 *                 @OA\Property(property="nation_id_card", type="string", example="ID987654321"),
 *                 @OA\Property(property="created_at", type="string", example="2025-07-10T10:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", example="2025-07-11T10:00:00Z")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="position_id", type="array", @OA\Items(type="string", example="The selected position ID does not exist.")),
 *             @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
 *             @OA\Property(property="status_code", type="integer", example=422)
 *         )
 *     )
 * )
 */

    function updateStaff(Request $request){
        $staff = Staff::find($request->id);
        if ($staff!= null){
        $rules = [
        'position_id'    => 'required|integer|exists:position,id'     ,
        'name'       =>'required|string|max:255',
        'gender'     => 'string|nullable',
        'dob' => 'required|string',
        'pob' => 'required|string',
        'address' => 'required|string',
        'phone_number' => 'required|string',
        'nation_id_card' => 'required|string'
        ];

        $messages = [
        'position_id.required' => 'Position ID is required.',
        'position_id.integer'  => 'Position ID must be an integer.',
        'position_id.exists'   => 'The selected position ID does not exist.',
        'name.required'    => 'Staff name is not allowed to be null.',
        'dob.required'       => 'dob is not allowed to be null.',
        'pob.required'       => 'pob is not allowed to be null.',
        'address.required'       => 'Address is not allowed to be null.',
        'phone_number.required'       => 'Phone_number is not allowed to be null.',
        'nation_id_card.required' => 'Nation_id_card is not allowed to be null.',
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }          
        $staff -> position_id = $request->position_id;
        $staff -> name = $request -> name; 
        $staff -> gender = $request->gender;
        $staff -> dob = $request -> dob;
        $staff -> pob = $request->pob;
        $staff -> address = $request -> address; 
        $staff -> phone_number = $request->phone_number;
        $staff -> nation_id_card = $request -> nation_id_card;     
        $staff->save();
        }
        return response()->json([
            'status'=> 'success',
            'updated_data'=> $staff,
            'status_code'=>200
        ]);
    }

    /**
 * @OA\Post(
 *     path="/api/staff/delete/force",
 *     summary="Force delete staff",
 *     tags={"Staff"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id"},
 *             @OA\Property(property="id", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Staff permanently deleted"),
 *     @OA\Response(response=404, description="Staff not found in trash")
 * )
 */
    function forceDeleteStaff(Request $request){
    // Only look into trashed records
    $staff = Staff::onlyTrashed()->find($request->id);

    if ($staff) {
        $staff->forceDelete(); // permanently deletes from DB

        return response()->json([
            'status' => 'success',
            'message' => "Staff with ID {$request->id} permanently deleted.",
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Staff with ID {$request->id} not found in trash.",
            'status_code' => 404
        ]);
    }
}

/**
 * @OA\Post(
 *     path="/api/staff/delete/soft",
 *     summary="Soft delete staff",
 *     tags={"Staff"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id"},
 *             @OA\Property(property="id", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Staff soft deleted"),
 *     @OA\Response(response=404, description="Staff not found")
 * )
 */
function softDeleteStaff(Request $request){    
    $staff = Staff::find($request->id);
    if ($staff !== null) {
        $staff->delete(); // Soft delete (sets deleted_at)
        return response()->json([
            'status' => 'success',
            'deleted_data' => $staff,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Staff with ID {$request->id} not found or already deleted!",
            'status_code' => 404
        ]);
    }
}

/**
 * @OA\Post(
 *     path="/api/staff/restore/{id}",
 *     summary="Restore soft deleted staff",
 *     tags={"Staff"},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Staff restored successfully"),
 *     @OA\Response(response=404, description="Staff not found in trash")
 * )
 */
function restoreStaff($id){
    $staff = Staff::onlyTrashed()->find($id);

    if ($staff) {
        $staff->restore();
        return response()->json([
            'status' => 'success',
            'restored_data' => $staff,
            'status_code' => 200
        ]);
    }

    return response()->json([
        'status' => "Staff with ID {$id} not found in trash.",
        'status_code' => 404
    ]);
}


}
