<?php

namespace App\Models;

use App\Models\Magasin;
use App\Models\Commande;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produit extends Model
{
    use HasFactory;

    protected $table = 'produits';

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function commandes()
    {
        return $this->belongsToMany(Commande::class, 'commande_produit', 'produit_id', 'commande_id')
            ->withPivot('quantite');
    }

}
