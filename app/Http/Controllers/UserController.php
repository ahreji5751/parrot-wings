<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function index(Request $request, User $user)
    {
        return response()->json([
            'users' => $user->getAll($request)
        ], 200);
    }

    public function names(Request $request, User $user)
    {
        return response()->json([
            'users' => $user->getAllEmails($request)
        ], 200);
    }

    public function update(Request $request)
    {
        User::find($request->user_id)->update($request->all());

        return response()->json(['success' => true], 200);
    }
}
