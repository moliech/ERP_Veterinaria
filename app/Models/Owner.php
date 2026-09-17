<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $fillable = ['document_type', 'document_number', 'first_name', 'last_name', 'address', 'email'];

    public function phones()
    {
        return $this->hasMany(OwnerPhone::class);
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }
}
