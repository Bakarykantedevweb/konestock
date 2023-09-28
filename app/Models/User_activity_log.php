<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User_activity_log extends Model
{
    use HasFactory;
    protected $guarded=[];

    protected $table='user_activity_logs';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
