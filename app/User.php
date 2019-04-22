<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Nicolaslopezj\Searchable\SearchableTrait;
use Kyslik\ColumnSortable\Sortable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SearchableTrait, Sortable;

    private $token;

    protected $searchable = [
        'columns' => [
            'users.email' => 10,
            'users.name' => 10,
        ]
    ];

    public $sortable = [
        'id',
        'name',
        'email',
        'balance',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'name', 'email', 'password', 'role', 'balance', 'banned'
    ];

    protected $hidden = [
        'password',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function authToken()
    {
        return $this->token;
    }

    public function getAll($request)
    {
        return $this->search($request->search)
                    ->sortable([$request->orderBy => $request->orderType])
                    ->get();
    }

    public function getAllEmails($request)
    {
        return $this->where('id', '!=', auth()->user()->id)
                    ->search($request->search)
                    ->get()
                    ->pluck('email');
    }

    public function getTransactions($request)
    {
        return $this->transactions()
                    ->sortable([$request->orderBy => $request->orderType])
                    ->get();

    }

    public function createTransaction($type, $amount, $correspondent_id)
    {
        $balance_before = $this->balance;
        $balance_after = $type === 'debit'
            ? $this->balance + $amount
            : $this->balance - $amount;

        $this->transactions()->create(compact([
            'type', 'amount', 'balance_before', 'balance_after', 'correspondent_id'
        ]));
    }

    public function exist($email)
    {
        return $this->where('email', $email)->exists();
    }

    public function enoughBalance($amount)
    {
        return $amount <= auth()->user()->balance;
    }

    public function generateToken()
    {
        $this->token = $this->createToken('PersonalToken')->accessToken;
    }
}
