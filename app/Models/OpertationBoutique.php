<?php

namespace App\Models;

use App\Models\Magasin;
use App\Models\Produit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OpertationBoutique extends Model
{
    use HasFactory;

    protected $table = 'operation_boutiques';

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }
}
