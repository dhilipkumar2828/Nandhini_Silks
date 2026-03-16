<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $fillable = [
        'tax_class_id',
        'name',
        'country',
        'state',
        'zip',
        'rate',
        'is_compound',
        'applies_to_shipping',
        'priority',
        'status',
    ];

    public function taxClass()
    {
        return $this->belongsTo(TaxClass::class);
    }
}
