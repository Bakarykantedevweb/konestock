<?php

namespace App\Models;

use App\Models\Gerant;
use App\Models\Produit;
use App\Models\Fournisseur;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Magasin extends Model
{
    use HasFactory;

    public function gerant()
    {
        return $this->belongsTo(Gerant::class);
    }


    public function produits()
    {
        return $this->hasMany(Produit::class);
    }
}
