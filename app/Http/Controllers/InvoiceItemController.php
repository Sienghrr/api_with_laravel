<?php

namespace App\Http\Controllers;

use App\Helper\ValidationService;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/invoiceItems",
 *     summary="Get paginated invoice items",
 *     tags={"InvoiceItem"},
 *     @OA\Parameter(
 *         name="_pageLimit",
 *         in="query",
 *         description="Number of items per page",
 *         required=false,
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Parameter(
 *         name="_pageSize",
 *         in="query",
 *         description="Page number",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Paginated list of invoice items",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="current_page", type="integer", example=1),
 *             @OA\Property(property="total_pages", type="integer", example=5),
 *             @OA\Property(property="total_items", type="integer", example=50),
 *             @OA\Property(property="per_page", type="integer", example=10),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     )
 * )
 */

    function getPagination(Request $request){
        $limit = $request->query('_pageLimit',10);
        $page = $request->query('_pageSize',1);

        $invoiceItems = InvoiceItem::paginate($limit, ['*'], 'page', $page);

        return response()->json([
        'status' => 'success',
        'current_page' => $invoiceItems->currentPage(),
        'total_pages' => $invoiceItems->lastPage(),
        'total_items' => $invoiceItems->total(),
        'per_page' => $invoiceItems->perPage(),
        'data' => $invoiceItems->items(),
        'status_code' => 200
    ]);
    }

    /**
 * @OA\Get(
 *     path="/api/invoiceItem/{id}",
 *     summary="Get invoice item by ID",
 *     tags={"InvoiceItem"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Invoice item retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(response=404, description="Invoice item not found")
 * )
 */

    function getById($id){
        $invoiceItem = InvoiceItem::find($id);
        if ($invoiceItem) {
        return response()->json([
            'status' => 'success',
            'data' => $invoiceItem,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "InvoiceItem with ID {$id} not found",
            'status_code' => 404
        ]);
    }
    }


/**
 * @OA\Post(
 *     path="/api/invoiceItem/create",
 *     summary="Create a new invoice item",
 *     tags={"InvoiceItem"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"invoice_id", "product_id", "qty", "price"},
 *             @OA\Property(property="invoice_id", type="integer", example=1),
 *             @OA\Property(property="product_id", type="integer", example=5),
 *             @OA\Property(property="qty", type="number", format="float", example=3),
 *             @OA\Property(property="price", type="number", format="float", example=10.5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Invoice item created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="new_InvoiceItem", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */

    function saveInvoiceItem(Request $request){

        $rules = [                
        'invoice_id' => 'required|integer|exists:invoice,id' ,
        'product_id' => 'required|integer|exists:product,id', 
        'qty'    => 'required|numeric|min:0',
        'price' => 'required|numeric|min:0',                    
        ];

        $messages = [
        'invoice_id.required' => 'Invoice ID is required.',
        'invoice_id.integer'  => 'Invoice ID must be an integer.',
        'invoice.exists'   => 'The selected Invoice ID does not exist.',
        'product_id.required' => 'Product ID is required.',
        'product_id.integer'  => 'Product ID must be an integer.',
        'product.exists'   => 'The selected Product ID does not exist.',
        'price.required' => 'Price is required.',
        'price.numeric'  => 'Price must be a number.',
        'price.min'      => 'Price must be greater than or equal to 0.',
        'qty.required' => 'Qty is required.',
        'qty.numeric'  => 'Qty must be a number.',
        'qty.min'      => 'Qty must be greater than or equal to 0.',
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }  
        $invoiceItem = new InvoiceItem();
        $invoiceItem -> invoice_id = $request->invoice_id;   
        $invoiceItem -> product_id = $request->product_id;      
        $invoiceItem -> qty = $request->qty; 
        $invoiceItem -> price = $request->price; 
        $invoiceItem -> save();
        return response() -> json([
            'status' => 'success',
            'new_InvoiceItem' => $invoiceItem,
            'status_code' => 200
        ]);
    }

    /**
 * @OA\Post(
 *     path="/api/invoiceItem/update",
 *     summary="Update an existing invoice item",
 *     tags={"InvoiceItem"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id", "invoice_id", "product_id", "qty", "price"},
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="invoice_id", type="integer", example=1),
 *             @OA\Property(property="product_id", type="integer", example=5),
 *             @OA\Property(property="qty", type="number", format="float", example=4),
 *             @OA\Property(property="price", type="number", format="float", example=12.0)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Invoice item updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="updated_data", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error"),
 *     @OA\Response(response=404, description="InvoiceItem not found")
 * )
 */

    function updateInvoiceItem(Request $request){
        $invoiceItem = InvoiceItem::find($request->id);
        if ($invoiceItem!= null){
       $rules = [                
        'invoice_id' => 'required|integer|exists:invoice,id' ,
        'product_id' => 'required|integer|exists:product,id', 
        'qty'    => 'required|numeric|min:0',
        'price' => 'required|numeric|min:0',                    
        ];

        $messages = [
        'invoice_id.required' => 'Invoice ID is required.',
        'invoice_id.integer'  => 'Invoice ID must be an integer.',
        'invoice.exists'   => 'The selected Invoice ID does not exist.',
        'product_id.required' => 'Product ID is required.',
        'product_id.integer'  => 'Product ID must be an integer.',
        'product.exists'   => 'The selected Product ID does not exist.',
        'price.required' => 'Price is required.',
        'price.numeric'  => 'Price must be a number.',
        'price.min'      => 'Price must be greater than or equal to 0.',
        'qty.required' => 'Qty is required.',
        'qty.numeric'  => 'Qty must be a number.',
        'qty.min'      => 'Qty must be greater than or equal to 0.',
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }        
       
        $invoiceItem -> invoice_id = $request->invoice_id;   
        $invoiceItem -> product_id = $request->product_id;      
        $invoiceItem -> qty = $request->qty; 
        $invoiceItem -> price = $request->price; 
        $invoiceItem -> save();
        }
        return response()->json([
            'status'=> 'success',
            'updated_data'=>$invoiceItem,
            'status_code'=>200
        ]);
    }

    /**
 * @OA\Post(
 *     path="/api/invoiceItem/delete/force",
 *     summary="Permanently delete a trashed invoice item",
 *     tags={"InvoiceItem"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id"},
 *             @OA\Property(property="id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Invoice item permanently deleted",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(response=404, description="Invoice item not found in trash")
 * )
 */

    function forceDeleteInvoiceItem(Request $request){
    // Only look into trashed records
    $invoiceItem = InvoiceItem::onlyTrashed()->find($request->id);

    if ($invoiceItem) {
        $invoiceItem->forceDelete(); // permanently deletes from DB

        return response()->json([
            'status' => 'success',
            'message' => "InvoiceItem with ID {$request->id} permanently deleted.",
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "InvoiceItem with ID {$request->id} not found in trash.",
            'status_code' => 404
        ]);
    }
}

/**
 * @OA\Post(
 *     path="/api/invoiceItem/delete/soft",
 *     summary="Soft delete an invoice item",
 *     tags={"InvoiceItem"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id"},
 *             @OA\Property(property="id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Invoice item soft deleted",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="deleted_data", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(response=404, description="Invoice item not found")
 * )
 */

function softDeleteInvoiceItem(Request $request){    
    $invoiceItem = InvoiceItem::find($request->id);
    if ($invoiceItem !== null) {
        $invoiceItem->delete(); // Soft delete (sets deleted_at)
        return response()->json([
            'status' => 'success',
            'deleted_data' => $invoiceItem,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "InvoiceItem with ID {$request->id} not found or already deleted!",
            'status_code' => 404
        ]);
    }
}

/**
 * @OA\Post(
 *     path="/api/invoiceItem/restore/{id}",
 *     summary="Restore a soft-deleted invoice item",
 *     tags={"InvoiceItem"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Invoice item restored",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="restored_data", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(response=404, description="Invoice item not found in trash")
 * )
 */

function restoreInvoiceItem($id){
    $invoiceItem = InvoiceItem::onlyTrashed()->find($id);

    if ($invoiceItem) {
        $invoiceItem->restore();
        return response()->json([
            'status' => 'success',
            'restored_data' => $invoiceItem,
            'status_code' => 200
        ]);
    }

    return response()->json([
        'status' => "InvoiceItem with ID {$id} not found in trash.",
        'status_code' => 404
    ]);
}
}
