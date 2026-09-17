<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = ['owner_id', 'name', 'species', 'breed', 'birth_date', 'weight'];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
