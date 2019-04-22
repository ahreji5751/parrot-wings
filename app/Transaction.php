<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Nicolaslopezj\Searchable\SearchableTrait;
use Kyslik\ColumnSortable\Sortable;
use Exception;

class Transaction extends Model
{
    use Sortable;

    protected $fillable = [
        'type', 'amount', 'balance_before', 'balance_after', 'correspondent_id'
    ];

    public $sortable = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function getAll($request)
    {
        return Transaction::sortable([$request->orderBy => $request->orderType])->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function make($request)
    {
        $correspondent = User::where('email', $request->email)->get();
        $correspondentModel = User::hydrate($correspondent->toArray())->first();

        DB::beginTransaction();

        try {
            auth()->user()->createTransaction('credit', $request->amount, $correspondentModel->id);
            auth()->user()->decrement('balance', $request->amount);

            $correspondentModel->createTransaction('debit', $request->amount, auth()->user()->id);
            $correspondentModel->increment('balance', $request->amount);

            DB::commit();
            return response()->json(['success' => true], 200);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Transaction not finished and will be rolled back'
            ], 400);
        }

    }
}
