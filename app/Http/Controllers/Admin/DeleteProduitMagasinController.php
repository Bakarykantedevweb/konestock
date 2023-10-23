<?php

namespace App\Http\Controllers\Admin;

use App\Models\Magasin;
use App\Models\Produit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteProduitMagasinController extends Controller
{
    public function index()
    {
        $magasins = Magasin::get();
        return view('admin.deleteMagasin.index',compact('magasins'));
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
        if ($request->has('produits')) {
            $produitsIds = $request->input('produits');

            if (!empty($produitsIds)) {
                Produit::whereIn('id', $produitsIds)
                    ->update(['delete_as' => 1,]);

                return redirect('admin/supprimer')->with('message', 'Opération effectuée avec succès');
            }
        }

        return redirect('admin/supprimer')->with('error', 'Aucun produit sélectionné.');
    }
}
