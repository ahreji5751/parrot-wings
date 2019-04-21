<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\Transaction as TransactionResource;

class TransactionController extends Controller
{
    public function index()
    {
        return TransactionResource::collection(auth()->user()->transactions()->get());
    }

    public function all()
    {
        return Transaction::all();
    }

    public function update()
    {

    }

    public function create(Request $request, Transaction $transaction, User $user)
    {
        if ($user->exist($request->email) && $user->enoughBalance($request->amount)) {
            return $transaction->make($request);
        }

        return response()->json([
            'error' => "Not enough balance or correspondent user doesn't exist"
        ], 400);
    }
}
