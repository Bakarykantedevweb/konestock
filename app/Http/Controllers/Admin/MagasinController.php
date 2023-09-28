<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Boutique;
use App\Models\Commande;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use App\Models\OperationMagasin;
use App\Models\OpertationBoutique;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Gerant;

class MagasinController extends Controller
{
    public function index()
    {
        return view('admin.magasin.index');
    }

    public function magasin(Request $req,$nom,$prenom)
    {
        try {
            $gerant = Gerant::where('prenom',$prenom)->first();
            $magasin = Magasin::where('nom', $nom)->first();
            $produits = Produit::where('magasin_id',$magasin->id)
                ->when($req->code != null, function ($q) use ($req) {
                    return $q->where('code', $req->code);
                })
                ->when($req->nom_produit != null, function ($q) use ($req) {
                    return $q->where('nom_produit', $req->nom_produit);
                })
            ->orderBy('nom_produit','ASC')
            ->get();
            return view('admin.magasin.magasin',compact('magasin', 'produits','gerant'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function produitAjout($nom,$prenom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $fournisseurs = Fournisseur::get();
            return view('admin.magasin.produitajout', compact('magasin', 'prenom', 'fournisseurs'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function produitSave(Request $request, $nom,$prenom)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $magasin = Magasin::where('nom', $nom)->firstOrFail();
            //dd($request->nom_produit);
            // Validez les données du formulaire
            $validatedData = $request->validate([
                'nom_produit' => 'required|string|max:255',
                'nom_piece' => 'required',
                'nom_carton' => 'required',
                'fournisseur_id' => 'required|integer',
                'prix_unitaire' => 'required'
            ]);
            if(Produit::where('nom_produit', $validatedData['nom_produit'])->where('magasin_id', $magasin->id)->exists())
            {
                $updateProduit = Produit::where('nom_produit', $validatedData['nom_produit'])->first();
                $updateProduit->nombre_piece = $validatedData['nom_piece'];
                $updateProduit->nombre_carton = $validatedData['nom_carton'] + $updateProduit->nombre_carton;
                $updateProduit->prix_unitaire = $validatedData['prix_unitaire'];
                $updateProduit->piece_totale = $updateProduit->nombre_carton * $validatedData['nom_piece'];
                //dd($updateProduit->piece_totale);
                $updateProduit->magasin_id = $magasin->id;
                $updateProduit->update();
                return redirect()->back()->with('message', 'Produit modifier avec success');
            }
            // Créez un nouvel objet Produit et affectez les valeurs
            $produit = new Produit();
            $produit->nom_produit = $validatedData['nom_produit'];
            $produit->nombre_piece = $validatedData['nom_piece'];
            $produit->nombre_carton = $validatedData['nom_carton'];
            $produit->prix_unitaire = $validatedData['prix_unitaire'];
            $produit->fournisseur_id = $validatedData['fournisseur_id'];
            $produit->piece_totale = $request->nom_carton * $request->nom_piece;
            $produit->magasin_id = $magasin->id;
            $produit->save();
            $latestProduitId = Produit::latest('id')->first()->id;
            $code = 'PR' . str_pad($latestProduitId, 4, '0', STR_PAD_LEFT);
            $produit->code = $code;
            $produit->save();

            return redirect()->back()->with('message', 'Produit ajouté avec succès');
            // return redirect('admin/magasin/' . $magasin->nom)->with('message', 'Produit ajouté avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function produitEdit($nom, $prenom,$code)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $produitCode = Produit::where('code', $code)->first();
            $produit = Produit::find($produitCode->id);
            $fournisseurs = Fournisseur::get();
            return view('admin.magasin.produit-edit', compact('magasin', 'prenom', 'produit', 'fournisseurs'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function produitUpdate(Request $request,$nom, $prenom,$code)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $magasin = Magasin::where('nom', $nom)->firstOrFail();

            $produitCode = Produit::where('code', $code)->first();
            // Validez les données du formulaire
            $validatedData = $request->validate([
                'nom_produit' => 'required|string|max:255',
                'nom_piece' => 'required|',
                'nom_carton' => 'required|',
                'fournisseur_id' => 'required|integer',
                'prix_unitaire' => 'required'
            ]);

            // Créez un nouvel objet Produit et affectez les valeurs
            $produit = Produit::where('id',$produitCode->id)->first();;
            $produit->nom_produit = $validatedData['nom_produit'];
            $produit->nombre_piece = $request->nom_piece;
            $produit->nombre_carton = $request->nom_carton;
            $produit->prix_unitaire = $validatedData['prix_unitaire'];
            $produit->fournisseur_id = $validatedData['fournisseur_id'];
            $produit->piece_totale = $request->nom_carton * $request->nom_piece;
            $produit->magasin_id = $magasin->id;
            $produit->save();

            return redirect('admin/magasin/' . $magasin->nom . '/gerant/' . $prenom )->with('message', 'Produit Modifié avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function produitDelete($nom, $prenom, $code)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $produitCode = Produit::where('code', $code)->first();
            $produit = Produit::where('id',$produitCode->id)->delete();
            return redirect('admin/magasin/' . $magasin->nom . '/gerant/' . $prenom)->with('message', 'Produit Supprime avec succès');
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }
}
