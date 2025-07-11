<?php

namespace App\Http\Controllers;

use App\Helper\ValidationService;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/products",
 *     summary="Get paginated list of products",
 *     tags={"Product"},
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
 *     @OA\Response(
 *         response=200,
 *         description="Successful response with paginated products"
 *     )
 * )
 */
   function getPagination(Request $request){
        $limit = $request->query('_pageLimit',10);
        $page = $request->query('_pageSize',1);

        $products = Product::paginate($limit, ['*'], 'page', $page);

        return response()->json([
        'status' => 'success',
        'current_page' => $products->currentPage(),
        'total_pages' => $products->lastPage(),
        'total_items' => $products->total(),
        'per_page' => $products->perPage(),
        'data' => $products->items(),
        'status_code' => 200
    ]);
    }

    /**
 * @OA\Get(
 *     path="/api/product/{id}",
 *     summary="Get product by ID",
 *     tags={"Product"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Product found"),
 *     @OA\Response(response=404, description="Product not found")
 * )
 */
    function getById($id){
        $product = Product::find($id);
        if ($product) {
        return response()->json([
            'status' => 'success',
            'data' => $product,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Product with ID {$id} not found",
            'status_code' => 404
        ]);
    }
    }


/**
 * @OA\Post(
 *     path="/api/product/create",
 *     summary="Create a new product",
 *     tags={"Product"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"product_name", "price", "cost", "category_id"},
 *             @OA\Property(property="product_name", type="string"),
 *             @OA\Property(property="price", type="number", format="float"),
 *             @OA\Property(property="cost", type="number", format="float"),
 *             @OA\Property(property="category_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Product created successfully"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */
    function saveProduct(Request $request){

        $rules = [
        'product_name'    => 'required|string|max:255' ,
        'price'    => 'required|numeric|min:0',
        'cost' => 'required|numeric|min:0',
        'category_id' => 'required|integer|exists:category,id'               
        ];

        $messages = [
        'product_name.required' => 'Product name is required.',
        'product_name.string' => 'Product name must be a valid string.',
        'product_name.max' => 'Product name cannot exceed 255 characters.',
        'price.required' => 'Price is required.',
        'price.numeric'  => 'Price must be a number.',
        'price.min'      => 'Price must be greater than or equal to 0.',
        'cost.required' => 'Cost is required.',
        'cost.numeric'  => 'Cost must be a number.',
        'cost.min'      => 'Cost must be at least 0.',
        'category_id.required' => 'Category ID is required.',
        'category_id.integer'  => 'Category ID must be an integer.',
        'category_id.exists'   => 'The selected category ID does not exist.'
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }  
        $product = new Product();
        $product -> product_name = $request->product_name; 
        $product -> price = $request->price;  
        $product -> cost = $request->cost;  
        $product -> category_id = $request->category_id;             
        $product -> save();
        return response() -> json([
            'status' => 'success',
            'new_category' => $product,
            'status_code' => 200
        ]);
    }

    /**
 * @OA\Post(
 *     path="/api/product/update",
 *     summary="Update product",
 *     tags={"Product"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id", "product_name", "price", "cost", "category_id"},
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="product_name", type="string"),
 *             @OA\Property(property="price", type="number", format="float"),
 *             @OA\Property(property="cost", type="number", format="float"),
 *             @OA\Property(property="category_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Product updated successfully"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */
    function updateProduct(Request $request){
        $product = Product::find($request->id);
        if ($product!= null){
        $rules = [
        'product_name'    => 'required|string|max:255' ,
        'price'    => 'required|numeric|min:0',
        'cost' => 'required|numeric|min:0',
        'category_id' => 'required|integer|exists:category,id'               
        ];

        $messages = [
        'product_name.required' => 'Product name is required.',
        'product_name.string' => 'Product name must be a valid string.',
        'product_name.max' => 'Product name cannot exceed 255 characters.',
        'price.required' => 'Price is required.',
        'price.numeric'  => 'Price must be a number.',
        'price.min'      => 'Price must be greater than or equal to 0.',
        'cost.required' => 'Cost is required.',
        'cost.numeric'  => 'Cost must be a number.',
        'cost.min'      => 'Cost must be at least 0.',
        'category_id.required' => 'Category ID is required.',
        'category_id.integer'  => 'Category ID must be an integer.',
        'category_id.exists'   => 'The selected category ID does not exist.'
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }        
       
        $product -> product_name = $request->product_name; 
        $product -> price = $request->price;  
        $product -> cost = $request->cost;  
        $product -> category_id = $request->category_id;
        $product -> save();
        }
        return response()->json([
            'status'=> 'success',
            'updated_data'=>$product,
            'status_code'=>200
        ]);
    }

    /**
 * @OA\Post(
 *     path="/api/product/delete/force",
 *     summary="Permanently delete a soft-deleted product",
 *     tags={"Product"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(@OA\Property(property="id", type="integer"))
 *     ),
 *     @OA\Response(response=200, description="Product permanently deleted"),
 *     @OA\Response(response=404, description="Product not found in trash")
 * )
 */
    function forceDeleteProduct(Request $request){
    // Only look into trashed records
    $product = Product::onlyTrashed()->find($request->id);

    if ($product) {
        $product->forceDelete(); // permanently deletes from DB

        return response()->json([
            'status' => 'success',
            'message' => "Product with ID {$request->id} permanently deleted.",
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Product with ID {$request->id} not found in trash.",
            'status_code' => 404
        ]);
    }
}

/**
 * @OA\Post(
 *     path="/api/product/delete/soft",
 *     summary="Soft delete a product",
 *     tags={"Product"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(@OA\Property(property="id", type="integer"))
 *     ),
 *     @OA\Response(response=200, description="Product soft deleted"),
 *     @OA\Response(response=404, description="Product not found")
 * )
 */

function softDeleteProduct(Request $request){    
    $product = Product::find($request->id);
    if ($product !== null) {
        $product->delete(); // Soft delete (sets deleted_at)
        return response()->json([
            'status' => 'success',
            'deleted_data' => $product,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Product with ID {$request->id} not found or already deleted!",
            'status_code' => 404
        ]);
    }
}

/**
 * @OA\Post(
 *     path="/api/product/restore/{id}",
 *     summary="Restore a soft-deleted product",
 *     tags={"Product"},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Product restored successfully"),
 *     @OA\Response(response=404, description="Product not found in trash")
 * )
 */
function restoreProduct($id){
    $product = Product::onlyTrashed()->find($id);

    if ($product) {
        $product->restore();
        return response()->json([
            'status' => 'success',
            'restored_data' => $product,
            'status_code' => 200
        ]);
    }

    return response()->json([
        'status' => "Product with ID {$id} not found in trash.",
        'status_code' => 404
    ]);
}
}
