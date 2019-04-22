<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'token' => $this->when($this->authToken(), $this->authToken()),
            'role' => $this->role,
            'balance' => $this->balance
        ];
    }
}
