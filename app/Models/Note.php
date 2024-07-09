<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['note', 'user_id', 'image'];
    
    public function actionLogs()
{
    return $this->hasMany(ActionLog::class);
}
}
