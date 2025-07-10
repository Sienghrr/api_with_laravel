<?php

namespace App\Http\Controllers;

use App\Helper\ValidationService;
use App\Models\Branch;
use Illuminate\Http\Request;



/**
 * @OA\Info(
 *     title="Fake Store API",
 *     version="1.0.0",
 *     description="This is the API documentation for my Laravel project.",
 *     @OA\Contact(
 *         email="your-email@example.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 */
class BranchController extends Controller
{
/**
 * @OA\Get(
 *     path="/api/branches",
 *     summary="Get paginated list of branches",
 *     description="Returns a paginated list of branches with optional query parameters for page size and limit.",
 *     tags={"Branch"},
 *
 *     @OA\Parameter(
 *         name="_pageLimit",
 *         in="query",
 *         description="Number of items per page",
 *         required=false,
 *         @OA\Schema(type="integer", default=10)
 *     ),
 *     @OA\Parameter(
 *         name="_pageSize",
 *         in="query",
 *         description="Current page number",
 *         required=false,
 *         @OA\Schema(type="integer", default=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Successful pagination response",
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
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="branch_name", type="string", example="Phnom Penh Branch"),
 *                     @OA\Property(property="location", type="string", example="Phnom Penh"),
 *                     @OA\Property(property="contact_number", type="string", example="012345678")
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

        $branches = Branch::paginate($limit, ['*'], 'page', $page);

        return response()->json([
        'status' => 'success',
        'current_page' => $branches->currentPage(),
        'total_pages' => $branches->lastPage(),
        'total_items' => $branches->total(),
        'per_page' => $branches->perPage(),
        'data' => $branches->items(),
        'status_code' => 200
    ]);
    }

/**
 * @OA\Get(
 *     path="/api/branch/{id}",
 *     summary="Get branch by ID",
 *     tags={"Branch"},
 *     description="Retrieve a single branch by its ID.",
 *     
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the branch to retrieve",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Branch found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="branch_name", type="string", example="Phnom Penh Branch"),
 *                 @OA\Property(property="location", type="string", example="Phnom Penh"),
 *                 @OA\Property(property="contact_number", type="string", example="012345678")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Branch not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="Branch with ID 99 not found"),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     )
 * )
 */
    function getById($id){
        $branch = Branch::find($id);
        if ($branch) {
        return response()->json([
            'status' => 'success',
            'data' => $branch,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Branch with ID {$id} not found",
            'status_code' => 404
        ]);
    }
    }
    /**
 * @OA\Post(
 *     path="/api/branch/create",
 *     summary="Create a new branch",
 *     tags={"Branch"},
 *     description="Creates a new branch with the given details.",
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"branch_name", "location", "contact_number"},
 *             @OA\Property(property="branch_name", type="string", example="Phnom Penh Branch"),
 *             @OA\Property(property="location", type="string", example="Phnom Penh"),
 *             @OA\Property(property="contact_number", type="string", example="012345678")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Branch created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="new_branch", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="branch_name", type="string", example="Phnom Penh Branch"),
 *                 @OA\Property(property="location", type="string", example="Phnom Penh"),
 *                 @OA\Property(property="contact_number", type="string", example="012345678")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="branch_name", type="string", example="Branch name is not allowed to be null.")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=422)
 *         )
 *     )
 * )
 */
    function saveBranch(Request $request){

        $rules = [
        'branch_name'    => 'required|string|max:255',
        'location'       => 'required|string|max:255',
        'contact_number' => 'required|string|max:20',
        ];

        $messages = [
            'branch_name.required'    => 'Branch name is not allowed to be null.',
            'location.required'       => 'Location is not allowed to be null.',
            'contact_number.required' => 'Contact number is not allowed to be null.',
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }  

        $branch = new Branch();
        $branch -> branch_name = $request->branch_name;
        $branch -> location = $request -> location;
        $branch -> contact_number = $request -> contact_number;
        $branch -> save();
        return response() -> json([
            'status' => 'success',
            'new_branch' => $branch,
            'status_code' => 200
        ]);
    }

    /**
 * @OA\Post(
 *     path="/api/branch/update",
 *     summary="Update branch by ID",
 *     tags={"Branch"},
 *     description="Updates an existing branch's details.",
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id", "branch_name", "location", "contact_number"},
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="branch_name", type="string", example="Updated Branch Name"),
 *             @OA\Property(property="location", type="string", example="Updated Location"),
 *             @OA\Property(property="contact_number", type="string", example="012345678")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Branch updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="updated_data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="branch_name", type="string", example="Updated Branch Name"),
 *                 @OA\Property(property="location", type="string", example="Updated Location"),
 *                 @OA\Property(property="contact_number", type="string", example="012345678")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Branch not found"
 *     )
 * )
 */
    function updateBranch(Request $request){
        $branch = Branch::find($request->id);
        if ($branch != null){
        $rules = [
        'branch_name'    => 'required|string|max:255',
        'location'       => 'required|string|max:255',
        'contact_number' => 'required|string|max:20',
        ];

        $messages = [
            'branch_name.required'    => 'Branch name is not allowed to be null.',
            'location.required'       => 'Location is not allowed to be null.',
            'contact_number.required' => 'Contact number is not allowed to be null.',
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }  
        $branch->branch_name = $request->branch_name;
        $branch->location = $request->location;
        $branch->contact_number = $request->contact_number;
        $branch->save();
        }
        return response()->json([
            'status'=> 'success',
            'updated_data'=>$branch,
            'status_code'=>200
        ]);
    }
/**
 * @OA\Post(
 *     path="/api/branch/delete/force",
 *     summary="Force delete a soft-deleted branch",
 *     tags={"Branch"},
 *     description="Permanently delete a branch from the trash (soft-deleted).",
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id"},
 *             @OA\Property(property="id", type="integer", example=1)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Branch permanently deleted",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Branch with ID 1 permanently deleted."),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Branch not found in trash",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="Branch with ID 99 not found in trash."),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     )
 * )
 */
    function forceDeleteBranch(Request $request){
    // Only look into trashed records
    $branch = Branch::onlyTrashed()->find($request->id);

    if ($branch) {
        $branch->forceDelete(); // permanently deletes from DB

        return response()->json([
            'status' => 'success',
            'message' => "Branch with ID {$request->id} permanently deleted.",
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Branch with ID {$request->id} not found in trash.",
            'status_code' => 404
        ]);
    }
}
/**
 * @OA\Post(
 *     path="/api/branch/delete/soft",
 *     summary="Soft delete a branch",
 *     tags={"Branch"},
 *     description="Soft deletes a branch by setting deleted_at without permanently removing it.",
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id"},
 *             @OA\Property(property="id", type="integer", example=1)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Branch soft deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="deleted_data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="branch_name", type="string", example="Kampot Branch"),
 *                 @OA\Property(property="location", type="string", example="Kampot"),
 *                 @OA\Property(property="contact_number", type="string", example="012345678"),
 *                 @OA\Property(property="deleted_at", type="string", example="2025-07-03T10:23:45.000000Z")
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Branch not found or already deleted",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="Branch with ID 10 not found or already deleted!"),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     )
 * )
 */
    function softDeleteBranch(Request $request){    
    $branch = Branch::find($request->id);
    if ($branch !== null) {
        $branch->delete(); // Soft delete (sets deleted_at)
        return response()->json([
            'status' => 'success',
            'deleted_data' => $branch,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Branch with ID {$request->id} not found or already deleted!",
            'status_code' => 404
        ]);
    }
}

/**
 * @OA\Post(
 *     path="/api/branch/restore/{id}",
 *     summary="Restore a soft-deleted branch",
 *     tags={"Branch"},
 *     description="Restores a branch that was soft-deleted.",
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the branch to restore",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Branch restored successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="restored_data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="branch_name", type="string", example="Restored Branch"),
 *                 @OA\Property(property="location", type="string", example="Siem Reap"),
 *                 @OA\Property(property="contact_number", type="string", example="012345678"),
 *                 @OA\Property(property="deleted_at", type="string", nullable=true, example=null)
 *             ),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Branch not found in trash",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="Branch with ID 10 not found in trash."),
 *             @OA\Property(property="status_code", type="integer", example=404)
 *         )
 *     )
 * )
 */


function restoreBranch($id){
    $branch = Branch::onlyTrashed()->find($id);

    if ($branch) {
        $branch->restore();
        return response()->json([
            'status' => 'success',
            'restored_data' => $branch,
            'status_code' => 200
        ]);
    }

    return response()->json([
        'status' => "Branch with ID {$id} not found in trash.",
        'status_code' => 404
    ]);
}


}
