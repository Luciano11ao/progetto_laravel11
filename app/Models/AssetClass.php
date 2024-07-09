<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetClass extends Model
{
    protected $fillable = [
        'name', 'commission_id', 'service_id',
    ];

    public function commission()
    {
        return $this->belongsTo(Commission::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
