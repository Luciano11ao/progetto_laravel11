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
        return $this->belongsTo(Commission::class);
    }

    public function assetClass()
    {
        return $this->hasOne(AssetClass::class);
    }
}
