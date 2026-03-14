<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxClass extends Model
{
    protected $fillable = ['name', 'description', 'status'];

    public function rates()
    {
        return $this->hasMany(TaxRate::class);
    }
}
