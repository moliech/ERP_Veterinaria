<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nit',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'address',
    ];

    /**
     * Relación con las Órdenes de Compra
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
