<?php

namespace App\Models;

use App\Models\Gerant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Boutique extends Model
{
    use HasFactory;

    public function gerant()
    {
        return $this->belongsTo(Gerant::class);
    }
}
