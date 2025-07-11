<?php

namespace App\Http\Controllers;

use App\Helper\ValidationService;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
/**
 * @OA\Get(
 *     path="/api/positions",
 *     summary="Get paginated list of positions",
 *     tags={"Position"},
 *     description="Returns a paginated list of positions based on page number and limit.",
 *
 *     @OA\Parameter(
 *         name="_pageLimit",
 *         in="query",
 *         description="Number of items per page (default is 10)",
 *         required=false,
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Parameter(
 *         name="_pageSize",
 *         in="query",
 *         description="Page number (default is 1)",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="List of positions",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="current_page", type="integer", example=1),
 *             @OA\Property(property="total_pages", type="integer", example=5),
 *             @OA\Property(property="total_items", type="integer", example=50),
 *             @OA\Property(property="per_page", type="integer", example=10),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="position_name", type="string", example="Manager"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-10T12:34:56.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-10T12:34:56.000000Z")
 *                 )
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     )
 * )
 */
    function getPagination(Request $request){
        $limit = $request->query('_pageLimit',10);
        $page = $request->query('_pageSize',1);

        $positions = Position::paginate($limit, ['*'], 'page', $page);

        return response()->json([
        'status' => 'success',
        'current_page' => $positions->currentPage(),
        'total_pages' => $positions->lastPage(),
        'total_items' => $positions->total(),
        'per_page' => $positions->perPage(),
        'data' => $positions->items(),
        'status_code' => 200
    ]);
    }

    /**
 * @OA\Get(
 *     path="/api/position/{id}",
 *     summary="Get position by ID",
 *     tags={"Position"},
 *     description="Fetch a single position record using its ID.",
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the position to retrieve",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Position found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="position_name", type="string", example="Manager"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-10T12:34:56.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-10T12:34:56.000000Z")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Position not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="Position with ID 99 not found"),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     )
 * )
 */
    function getById($id){
        $position = Position::find($id);
        if ($position) {
        return response()->json([
            'status' => 'success',
            'data' => $position,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Position with ID {$id} not found",
            'status_code' => 404
        ]);
    }
    }


/**
 * @OA\Post(
 *     path="/api/position/create",
 *     summary="Create a new position",
 *     tags={"Position"},
 *     description="Create a new position with a name and associated branch ID.",
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "branch_id"},
 *             @OA\Property(property="name", type="string", example="Manager"),
 *             @OA\Property(property="branch_id", type="integer", example=1)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Position created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="new_position", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="errors", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=422)
 *         )
 *     )
 * )
 */
    function savePosition(Request $request){

        $rules = [
        'name'    => 'required|string|max:255',
        'branch_id'   => 'required|integer|exists:branch,id'        
        ];

        $messages = [
        
        'name.required' => 'branch name is required.',
        'name.string' => 'branch name must be a valid string.',
        'name.max' => 'branch name cannot exceed 255 characters.',
        'branch_id.required' => 'Branch ID is required.',
        'branch_id.integer'  => 'Branch ID must be an integer.',
        'branch_id.exists'   => 'The selected branch ID does not exist.'
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }  
        $position = new Position();
        $position -> name = $request->name;
        $position -> branch_id = $request -> branch_id;        
        $position -> save();
        return response() -> json([
            'status' => 'success',
            'new_position' => $position,
            'status_code' => 200
        ]);
    }

   /**
 * @OA\Post(
 *     path="/api/position/update",
 *     summary="Update an existing position",
 *     tags={"Position"},
 *     description="Update the name and branch_id of a position by its ID.",
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id", "name", "branch_id"},
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Assistant Manager"),
 *             @OA\Property(property="branch_id", type="integer", example=2)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Position updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="updated_data", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="errors", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=422)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Position not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="Position with ID {id} not found"),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     )
 * )
 */

    function updatePosition(Request $request){
        $position = Position::find($request->id);
        if ($position!= null){
        $rules = [
        'name'    => 'required|string|max:255',
        'branch_id'   => 'required|integer|exists:branch,id'        
        ];

        $messages = [
        
        'name.required' => 'branch name is required.',
        'name.string' => 'branch name must be a valid string.',
        'name.max' => 'branch name cannot exceed 255 characters.',
        'branch_id.required' => 'Branch ID is required.',
        'branch_id.integer'  => 'Branch ID must be an integer.',
        'branch_id.exists'   => 'The selected branch ID does not exist.'
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }          
        $position -> name = $request->name;
        $position -> branch_id = $request -> branch_id;
        $position->save();
        }
        return response()->json([
            'status'=> 'success',
            'updated_data'=>$position,
            'status_code'=>200
        ]);
    }

    /**
 * @OA\Post(
 *     path="/api/position/delete/force",
 *     summary="Force delete a trashed position",
 *     tags={"Position"},
 *     description="Permanently delete a soft-deleted position by ID",
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id"},
 *             @OA\Property(property="id", type="integer", example=3)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Position permanently deleted",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Position with ID 3 permanently deleted."),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Position not found in trash",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="Position with ID 3 not found in trash."),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     )
 * )
 */
    function forceDeletePosition(Request $request){
    // Only look into trashed records
    $position = Position::onlyTrashed()->find($request->id);

    if ($position) {
        $position->forceDelete(); // permanently deletes from DB

        return response()->json([
            'status' => 'success',
            'message' => "Position with ID {$request->id} permanently deleted.",
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Position with ID {$request->id} not found in trash.",
            'status_code' => 404
        ]);
    }
}

/**
 * @OA\Post(
 *     path="/api/position/delete/soft",
 *     summary="Soft delete a position",
 *     tags={"Position"},
 *     description="Soft deletes (moves to trash) a position by ID",
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id"},
 *             @OA\Property(property="id", type="integer", example=3)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Position soft-deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="deleted_data", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Position not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="Position with ID 3 not found or already deleted!"),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     )
 * )
 */
function softDeletePosition(Request $request){    
    $position = Position::find($request->id);
    if ($position !== null) {
        $position->delete(); // Soft delete (sets deleted_at)
        return response()->json([
            'status' => 'success',
            'deleted_data' => $position,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Position with ID {$request->id} not found or already deleted!",
            'status_code' => 404
        ]);
    }
}

/**
 * @OA\Post(
 *     path="/api/position/restore/{id}",
 *     summary="Restore a soft-deleted position",
 *     tags={"Position"},
 *     description="Restores a soft-deleted position using its ID.",
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the position to restore",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Position restored successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="restored_data", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Position not found in trash",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="Position with ID 3 not found in trash."),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     )
 * )
 */
function restorePosition($id){
    $position = Position::onlyTrashed()->find($id);

    if ($position) {
        $position->restore();
        return response()->json([
            'status' => 'success',
            'restored_data' => $position,
            'status_code' => 200
        ]);
    }

    return response()->json([
        'status' => "Position with ID {$id} not found in trash.",
        'status_code' => 404
    ]);
}


}
