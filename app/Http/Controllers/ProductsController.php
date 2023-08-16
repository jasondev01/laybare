<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\Response;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    // show all products
    public function index()
    {
        $products = Product::with('category')->paginate(10);

        $formattedProducts = [];
        foreach ($products as $product) {
            $formattedProducts[] = [
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'product_sku' => $product->product_sku,
                'product_category_id' => $product->category->id,
                'product_category' => $product->category->category_name,
                'product_description' => $product->product_description,
            ];
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'OK',
            'data' => $formattedProducts,
            'meta' => [
                'pagination' => [
                    'total' => $products->total(),
                    'count' => $products->count(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'total_pages' => $products->lastPage(),
                    'links' => [
                        'next' => $products->nextPageUrl(),
                    ],
                ],
            ],
        ], 200);
    }

    // store a product -> add a product
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'product_sku' => 'required|unique:products|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'product_description' => 'nullable',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status_code' => 422,
                'message' => 'The product SKU has already been taken.',
                'error' => $validator->messages()
            ], 422);
        } else {
            $product = Product::create([
                'product_name' => $request->product_name,
                'product_sku' => $request->product_sku,
                'category_id' => $request->category_id,
                'product_description' => $request->product_description,
            ]);

            $data = [
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'product_sku' => $product->product_sku,
                'product_category_id' => $product->category_id,
                'product_category' => $product->category->category_name,
                'product_description' => $product->product_description,
            ];

            if ($product) {
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Product is added successfully.',
                    'data' => $data
                ], 200);
            } else {
                return response()->json([
                    'status_code' => 500,
                    'message' => 'Something went wrong.'
                ], 500);
            }
        }
    }

    // show a product 
    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Product not found',
            ], 404);
        }

        $formattedProduct = [
            'product_id' => $product->id,
            'product_name' => $product->product_name,
            'product_sku' => $product->product_sku,
            'product_category_id' => $product->category->id,
            'product_category' => $product->category->category_name,
            'product_description' => $product->product_description,
        ];

        return response()->json([
            'status_code' => 200,
            'message' => 'OK',
            'data' => $formattedProduct,
        ], 200);
    }

    // update a product
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Product not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'product_sku' => [
                'required',
                Rule::unique('products')->ignore($product->id),
                'string',
                'max:255',
            ],
            'category_id' => 'required|exists:categories,id',
            'product_description' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Validation error',
                'errors' => $validator->messages(),
            ], 422);
        } else {
            $product->update([
                'product_name' => $request->product_name,
                'product_sku' => $request->product_sku,
                'category_id' => $request->category_id,
                'product_description' => $request->product_description,
            ]);

            $data = [
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'product_sku' => $product->product_sku,
                'product_category_id' => $product->category_id,
                'product_category' => $product->category->category_name,
                'product_description' => $product->product_description,
            ];

            return response()->json([
                'status_code' => 200,
                'message' => 'Product updated successfully',
                'data' => $data,
            ], 200);
        }
    }

    // soft deletes a product
    public function softDelete($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Product not found',
            ], 404);
        }

        $product->delete(); 

        return response()->json([
            'status_code' => 200,
            'message' => 'Product soft deleted successfully',
        ], 200);
    }

    // view soft deleted products
    public function softDeleteShow()
    {
        $products = Product::onlyTrashed()->get();

        return response()->json([
            'status_code' => 200,
            'message' => 'OK',
            'data' => $products,
        ], 200);
    }

    // restores sofl deleted product
    public function restore($id)
    {
        $softDeletedProduct = Product::onlyTrashed()->find($id);

        if (!$softDeletedProduct) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Soft-deleted product not found',
            ], 404);
        }

        $softDeletedProduct->restore(); // Restore the product

        return response()->json([
            'status_code' => 200,
            'message' => 'Product restored successfully',
        ], 200);
    }
}