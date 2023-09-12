<?php

namespace App\Models;

use App\Models\Produit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commande extends Model
{
    use HasFactory;

    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'commande_produit', 'commande_id', 'produit_id')
            ->withPivot('quantite');
    }

}
