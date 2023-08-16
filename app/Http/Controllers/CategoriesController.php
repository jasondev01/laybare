<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Controllers\Response;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    // shows all the category
    public function index()
    {
        $categories = Category::paginate(20);

        if($categories->count() > 0) {
            return response()->json([
                'status_code' => 200,
                'message' => 'OK',
                'data' => $categories,
            ], 200);
        } else {
            return response()->json([
                'status_code' => 404,
                'message' => 'No categories are found',
            ], 404);
        }
    }

    // store a category
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|unique:categories|string|max:100',
            'category_description' => 'nullable'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status_code' => 422,
                'message' => 'The category name has already been taken.'
            ], 422);
        } else {
            $category = Category::create([
                'category_name' => $request->category_name,
                'category_description' => $request->category_description
            ]);
            
            if ($category) {
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Category is created successfully.',
                    'data' => $category
                ], 200);
            } else {
                return response()->json([
                    'status_code' => 500,
                    'message' => 'Something went wrong.'
                ], 500);
            }
        }
    }

    // show a category
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Category not found.',
            ], 404);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'OK',
            'data' => $category,
        ], 200);
    }

    // updates a category
    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|unique:categories|string|max:100',
            'category_description' => 'nullable'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status_code' => 422,
                'message' => 'The category name has already been taken.'
            ], 422);
        } else {
            $category = Category::find($id);
            if ($category) {
                $category->update([
                    'category_name' => $request->category_name,
                    'category_description' => $request->category_description
                ]);

                return response()->json([
                    'status_code' => 200,
                    'message' => 'Category is updated successfully.',
                    'data' => $category
                ], 200);
            } else {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Category not found.'
                ], 404);
            }
        }
    }

    // soft deletes a category
    public function softDelete($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Category not found',
            ], 404);
        }

        $category->delete(); 

        return response()->json([
            'status_code' => 200,
            'message' => 'Category soft deleted successfully',
        ], 200);
    }

    // view soft deleted category/ies
    public function softDeleteShow()
    {
        $categories = Category::onlyTrashed()->get();

        return response()->json([
            'status_code' => 200,
            'message' => 'OK',
            'data' => $categories,
        ], 200);
    }

    // restores sofl deleted product
    public function restore($id)
    {
        $softDeletedCategory = Category::onlyTrashed()->find($id);

        if (!$softDeletedCategory) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Soft-deleted category not found',
            ], 404);
        }

        $softDeletedCategory->restore(); // Restore the category

        return response()->json([
            'status_code' => 200,
            'message' => 'Category restored successfully',
        ], 200);
    }
}
