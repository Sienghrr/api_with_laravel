<?php

namespace App\Http\Controllers;

use App\Helper\ValidationService;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/invoices",
 *     summary="Get paginated invoices",
 *     tags={"Invoice"},
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
 *         description="Paginated list of invoices",
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

        $invoices = Invoice::paginate($limit, ['*'], 'page', $page);

        return response()->json([
        'status' => 'success',
        'current_page' => $invoices->currentPage(),
        'total_pages' => $invoices->lastPage(),
        'total_items' => $invoices->total(),
        'per_page' => $invoices->perPage(),
        'data' => $invoices->items(),
        'status_code' => 200
    ]);
    }

    /**
 * @OA\Get(
 *     path="/api/invoice/{id}",
 *     summary="Get invoice by ID",
 *     tags={"Invoice"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Invoice retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(response=404, description="Invoice not found")
 * )
 */

    function getById($id){
        $invoice = Invoice::find($id);
        if ($invoice) {
        return response()->json([
            'status' => 'success',
            'data' => $invoice,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Invoice with ID {$id} not found",
            'status_code' => 404
        ]);
    }
    }


/**
 * @OA\Post(
 *     path="/api/invoice/create",
 *     summary="Create a new invoice",
 *     tags={"Invoice"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id"},
 *             @OA\Property(property="user_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Invoice created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="new_user", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */

    function saveInvoice(Request $request){

        $rules = [                
        'user_id' => 'required|integer|exists:users,id'               
        ];

        $messages = [
        'user_id.required' => 'User ID is required.',
        'user_id.integer'  => 'User ID must be an integer.',
        'user_id.exists'   => 'The selected User ID does not exist.'
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }  
        $invoice = new Invoice();
        $invoice -> user_id = $request->user_id;        
        $invoice -> save();
        return response() -> json([
            'status' => 'success',
            'new_invoice' => $invoice,
            'status_code' => 200
        ]);
    }

    /**
 * @OA\Post(
 *     path="/api/invoice/update",
 *     summary="Update an existing invoice",
 *     tags={"Invoice"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id", "user_id"},
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="user_id", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Invoice updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="updated_data", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error"),
 *     @OA\Response(response=404, description="Invoice not found")
 * )
 */

    function updateInvoice(Request $request){
        $invoice = Invoice::find($request->id);
        if ($invoice!= null){
        $rules = [                
        'user_id' => 'required|integer|exists:users,id'               
        ];

        $messages = [
        'user_id.required' => 'User ID is required.',
        'user_id.integer'  => 'User ID must be an integer.',
        'user_id.exists'   => 'The selected User ID does not exist.'
        ];

        $result = ValidationService::validate($request->all(), $rules , $messages);        
        if ($result !== true) {
            return response()->json($result, 422);
        }        
       
        $invoice -> user_id = $request->user_id; 
        $invoice -> save();
        }
        return response()->json([
            'status'=> 'success',
            'updated_data'=>$invoice,
            'status_code'=>200
        ]);
    }

    /**
 * @OA\Post(
 *     path="/api/invoice/delete/force",
 *     summary="Permanently delete a trashed invoice",
 *     tags={"Invoice"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id"},
 *             @OA\Property(property="id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Invoice deleted permanently",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(response=404, description="Invoice not found in trash")
 * )
 */

    function forceDeleteInvoice(Request $request){
    // Only look into trashed records
    $invoice = Invoice::onlyTrashed()->find($request->id);

    if ($invoice) {
        $invoice->forceDelete(); // permanently deletes from DB

        return response()->json([
            'status' => 'success',
            'message' => "Invoice with ID {$request->id} permanently deleted.",
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Invoice with ID {$request->id} not found in trash.",
            'status_code' => 404
        ]);
    }
}

/**
 * @OA\Post(
 *     path="/api/invoice/delete/soft",
 *     summary="Soft delete an invoice",
 *     tags={"Invoice"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id"},
 *             @OA\Property(property="id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Invoice soft deleted",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="deleted_data", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(response=404, description="Invoice not found")
 * )
 */

function softDeleteInvoice(Request $request){    
    $invoice = Invoice::find($request->id);
    if ($invoice !== null) {
        $invoice->delete(); // Soft delete (sets deleted_at)
        return response()->json([
            'status' => 'success',
            'deleted_data' => $invoice,
            'status_code' => 200
        ]);
    } else {
        return response()->json([
            'status' => "Invoice with ID {$request->id} not found or already deleted!",
            'status_code' => 404
        ]);
    }
}

/**
 * @OA\Post(
 *     path="/api/invoice/restore/{id}",
 *     summary="Restore a soft-deleted invoice",
 *     tags={"Invoice"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Invoice restored",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="restored_data", type="object"),
 *             @OA\Property(property="status_code", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(response=404, description="Invoice not found in trash")
 * )
 */

function restoreInvoice($id){
    $invoice = Invoice::onlyTrashed()->find($id);

    if ($invoice) {
        $invoice->restore();
        return response()->json([
            'status' => 'success',
            'restored_data' => $invoice,
            'status_code' => 200
        ]);
    }

    return response()->json([
        'status' => "Invoice with ID {$id} not found in trash.",
        'status_code' => 404
    ]);
}
}
