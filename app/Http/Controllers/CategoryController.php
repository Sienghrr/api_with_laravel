<?php

namespace App\Http\Controllers;

use App\Helper\ValidationService;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/categories",
 *     summary="Get paginated categories",
 *     tags={"Category"},
 *     @OA\Parameter(
 *         name="_pageLimit",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer", default=10)
 *     ),
 *     @OA\Parameter(
 *         name="_pageSize",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer", default=1)
 *     ),
 *     @OA\Response(response=200, description="Paginated category list")
 * )
 */
    function getPagination(Request $request){
        $limit = $request->query('_pageLimit',10);
        $page = $request->query('_pageSize',1);

        $categories = Category::paginate($limit, ['*'], 'page', $page);

        return response()->json([
        'status' => 'success',
        'current_page' => $categories->currentPage(),
        'total_pages' => $categories->lastPage(),
        'total_items' => $categories->total(),
        'per_page' => $categories->perPage(),
        'data' => $categories->items(),
        'status_code' => 200
    ]);
    }

    /**
 * @OA\Get(
 *     path="/api/category/{id}",
 *     summary="Get category by ID",
 *     tags={"Category"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Category found"),
 *     @OA\Response(response=404, description="Category not found")
 * )
 */
    function getById($id){
        $category = Category::find($id);
        if ($category) {
        return response()->json([
            'status' => 'success',
            'data' => $category,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Category with ID {$id} not found",
            'status_code' => 404
        ]);
    }
    }


/**
 * @OA\Post(
 *     path="/api/category/create",
 *     summary="Create a new category",
 *     tags={"Category"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Category created"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */
    function saveCategory(Request $request){

        $rules = [
        'name'    => 'required|string|max:255'               
        ];

        $messages = [
            'name.required'    => 'Category name is not allowed to be null.'
            
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }  
        $category = new Category();
        $category -> name = $request->name;               
        $category -> save();
        return response() -> json([
            'status' => 'success',
            'new_category' => $category,
            'status_code' => 200
        ]);
    }

    /**
 * @OA\Post(
 *     path="/api/category/update",
 *     summary="Update category",
 *     tags={"Category"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id", "name"},
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="name", type="string")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Category updated"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */
    function updateCategory(Request $request){
        $category = Category::find($request->id);
        if ($category!= null){
        $rules = [
        'name'    => 'required|string|max:255'                
        ];

        $messages = [
            'name.required'    => 'Category name is not allowed to be null.'            
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }          
        $category -> name = $request->name;       
        $category->save();
        }
        return response()->json([
            'status'=> 'success',
            'updated_data'=>$category,
            'status_code'=>200
        ]);
    }

    /**
 * @OA\Post(
 *     path="/api/category/delete/force",
 *     summary="Permanently delete category",
 *     tags={"Category"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id"},
 *             @OA\Property(property="id", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Category permanently deleted"),
 *     @OA\Response(response=404, description="Category not found in trash")
 * )
 */
    function forceDeleteCategory(Request $request){
    // Only look into trashed records
    $category = Category::onlyTrashed()->find($request->id);

    if ($category) {
        $category->forceDelete(); // permanently deletes from DB

        return response()->json([
            'status' => 'success',
            'message' => "Category with ID {$request->id} permanently deleted.",
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Category with ID {$request->id} not found in trash.",
            'status_code' => 404
        ]);
    }
}

/**
 * @OA\Post(
 *     path="/api/category/delete/soft",
 *     summary="Soft delete category",
 *     tags={"Category"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id"},
 *             @OA\Property(property="id", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Category soft-deleted"),
 *     @OA\Response(response=404, description="Category not found")
 * )
 */
function softDeleteCategory(Request $request){    
    $category = Category::find($request->id);
    if ($category !== null) {
        $category->delete(); // Soft delete (sets deleted_at)
        return response()->json([
            'status' => 'success',
            'deleted_data' => $category,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Category with ID {$request->id} not found or already deleted!",
            'status_code' => 404
        ]);
    }
}

/**
 * @OA\Post(
 *     path="/api/category/restore/{id}",
 *     summary="Restore soft-deleted category",
 *     tags={"Category"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Category restored"),
 *     @OA\Response(response=404, description="Category not found in trash")
 * )
 */
function restoreCategory($id){
    $category = Category::onlyTrashed()->find($id);

    if ($category) {
        $category->restore();
        return response()->json([
            'status' => 'success',
            'restored_data' => $category,
            'status_code' => 200
        ]);
    }

    return response()->json([
        'status' => "Category with ID {$id} not found in trash.",
        'status_code' => 404
    ]);
}
}
