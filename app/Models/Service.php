<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name', 'commission_id',
    ];

    public function commission()
    {
        return $this->has(Commission::class);
    }

    public function assetClasses()
    {
        return $this->belongsTo(AssetClass::class);
    }
}
