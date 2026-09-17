<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerPhone extends Model
{
    protected $fillable = ['owner_id', 'phone_number', 'phone_type'];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
