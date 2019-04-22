<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\Transaction as TransactionResource;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        return TransactionResource::collection(auth()->user()->getTransactions($request));
    }

    public function all(Request $request, Transaction $transaction)
    {
        return $transaction->getAll($request);
    }

    public function update(Request $request)
    {
        Transaction::find($request->transaction_id)->update($request->all());

        return response()->json(['success' => true], 200);
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
