<?php

namespace App\Http\Controllers\Admin;

use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Boutique;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteProduitMagasinController extends Controller
{
    public function index()
    {
        $magasins = Magasin::get();
        $boutiques = Boutique::get();
        return view('admin.deleteMagasin.index',compact('magasins','boutiques'));
    }

    public function produitMagasin($nom)
    {
        $magasin = Magasin::where('nom',$nom)->first();
        if(!$magasin){
            return redirect('admin/supprimer')->with('error','Operation non trouve');
        }
        $produits = Produit::where('magasin_id', $magasin->id)
            ->where('delete_as', '0')
            ->orderBy('nom_produit', 'ASC')->get();
        return view('admin.deleteMagasin.produit',compact('produits','magasin'));
    }

    public function produitMagasinSave(Request $request, $nom)
    {
        $magasin = Magasin::where('nom', $nom)->first();
        if ($request->has('produits')) {
            $produitsIds = $request->input('produits');

            if (!empty($produitsIds)) {
                Produit::whereIn('id', $produitsIds)
                        ->where('magasin_id',$magasin->id)
                    ->update(['delete_as' => 1,]);

                return redirect('admin/supprimer')->with('message', 'Opération effectuée avec succès');
            }
        }

        return redirect('admin/supprimer')->with('error', 'Aucun produit sélectionné.');
    }


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

    public function produitBoutiqueSave(Request $request, $nomBoutique)
    {
        $boutique = Boutique::where('nom', $nomBoutique)->first();
        if ($request->has('produits')) {
            $produitsIds = $request->input('produits');

            if (!empty($produitsIds)) {
                Produit::whereIn('id', $produitsIds)
                        ->where('boutique_id',$boutique->id)
                    ->update(['delete_as' => 1,]);

                return redirect('admin/supprimer')->with('message', 'Opération effectuée avec succès');
            }
        }

        return redirect('admin/supprimer')->with('error', 'Aucun produit sélectionné.');
    }
}
