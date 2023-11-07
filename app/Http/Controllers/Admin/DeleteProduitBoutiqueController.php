<?php

namespace App\Http\Controllers\Admin;

use App\Models\Produit;
use App\Models\Boutique;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteProduitBoutiqueController extends Controller
{
    public function produitBoutique($nomBoutique)
    {
        $boutique = Boutique::where('nom', $nomBoutique)->first();
        if (!$boutique) {
            return redirect('admin/supprimer')->with('error', 'Operation non trouve');
        }
        $produits = Produit::where('boutique_id', $boutique->id)
            ->where('delete_as', '0')
            ->orderBy('nom_produit', 'ASC')->get();
        return view('admin.deleteBoutique.produit', compact('produits', 'boutique'));
    }
}
