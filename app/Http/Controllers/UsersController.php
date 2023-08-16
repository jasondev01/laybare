<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    // show all users
    public function index()
    {
        $users = User::all();

        return response()->json([
            'status_code' => 200,
            'message' => 'OK',
            'data' => $users,
        ], 200);
    }

    // store a user
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = new User();
        $user->fill($request->all());
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return response()->json([
            'status_code' => 201,
            'message' => 'User created successfully',
            'data' => $user,
        ], 201);
    }

    // show a single user
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status_code' => 404,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'OK',
            'data' => $user,
        ], 200);
    }

    // update a user
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status_code' => 404,
                'message' => 'User not found',
            ], 404);
        }

        $user->fill($request->all());
        $user->save();

        return response()->json([
            'status_code' => 200,
            'message' => 'User updated successfully',
            'data' => $user,
        ], 200);
    }

    // soft deletes a user
    public function softDelete($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status_code' => 404,
                'message' => 'User not found',
            ], 404);
        }

        $user->delete(); // Soft delete the user

        return response()->json([
            'status_code' => 200,
            'message' => 'User soft deleted successfully',
        ], 200);
    }

    // restore a soft deleted user
    public function restore($id)
    {
        $softDeletedUser = User::onlyTrashed()->find($id);

        if (!$softDeletedUser) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Soft-deleted user not found',
            ], 404);
        }

        $softDeletedUser->restore(); // Restore the user

        return response()->json([
            'status_code' => 200,
            'message' => 'User restored successfully',
        ], 200);
    }

}
