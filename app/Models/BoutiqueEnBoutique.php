<?php

namespace App\Models;

use App\Models\Produit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoutiqueEnBoutique extends Model
{
    use HasFactory;

    protected $table = 'boutique_en_boutique';

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
