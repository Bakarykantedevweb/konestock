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

class MagasinController extends Controller
{
    public function index()
    {
        return view('admin.magasin.index');
    }

    public function magasin(Request $req,$nom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $produits = Produit::where('magasin_id',$magasin->id)
            ->when($req->code != null, function ($q) use ($req) {
                return $q->where('code', $req->code);
            })
            ->paginate(10);
            return view('admin.magasin.magasin',compact('magasin', 'produits'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function produitAjout($nom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $fournisseurs = Fournisseur::get();
            return view('admin.magasin.produitajout', compact('magasin', 'fournisseurs'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function produitSave(Request $request, $nom)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $magasin = Magasin::where('nom', $nom)->firstOrFail();
            //dd($request->nom_produit);
            // Validez les données du formulaire
            $validatedData = $request->validate([
                'nom_produit' => 'required|string|max:255',
                'nom_piece' => 'required|integer',
                'nom_carton' => 'required|integer',
                'fournisseur_id' => 'required|integer',
                'prix_unitaire' => 'required'
            ]);

            // Créez un nouvel objet Produit et affectez les valeurs
            $produit = new Produit();
            $produit->nom_produit = $validatedData['nom_produit'];
            $produit->nombre_piece = $request->nom_piece;
            $produit->nombre_carton = $request->nom_carton;
            $produit->prix_unitaire = $validatedData['prix_unitaire'];
            $produit->fournisseur_id = $validatedData['fournisseur_id'];
            $produit->piece_totale = $request->nom_carton * $request->nom_piece;
            $produit->magasin_id = $magasin->id;
            $produit->save();
            $latestProduitId = Produit::latest('id')->first()->id;
            $code = 'PR' . str_pad($latestProduitId, 4, '0', STR_PAD_LEFT);
            $produit->code = $code;
            $produit->save();

            return redirect('admin/magasin/' . $magasin->nom)->with('message', 'Produit ajouté avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function facture($nom, $numero)
    {
        try {
            // Recherche le magasin par son nom
            $magasin = Magasin::where('nom', $nom)->first();

            // Vérifie si le magasin existe
            if ($magasin) {
                // Recherche la commande du magasin par son numéro
                $commande = Commande::where('magasin_id', $magasin->id)->where('numero', $numero)->first();

                // Vérifie si la commande existe
                if ($commande) {
                    // Récupère les produits de la commande
                    $commandeProduits = $commande->produits;

                    // Affiche la vue de la facture en passant les données
                    return view('admin.magasin.facture', compact('magasin', 'commande', 'commandeProduits'));
                } else {
                    // Redirige avec un message d'erreur si la commande n'existe pas
                    session()->flash('error', 'La commande spécifiée n\'existe pas.');
                    return redirect('admin/dashboard');
                }
            } else {
                // Redirige avec un message d'erreur si le magasin n'existe pas
                session()->flash('error', 'Le magasin spécifié n\'existe pas.');
                return redirect('admin/dashboard');
            }
        } catch (\Throwable $th) {
            // Redirige avec un message d'erreur si une exception se produit
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function produitEdit($nom,$code)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $produitCode = Produit::where('code', $code)->first();
            $produit = Produit::find($produitCode->id);
            $fournisseurs = Fournisseur::get();
            return view('admin.magasin.produit-edit', compact('magasin', 'produit', 'fournisseurs'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function produitUpdate(Request $request,$nom, $code)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $magasin = Magasin::where('nom', $nom)->firstOrFail();

            $produitCode = Produit::where('code', $code)->first();
            // Validez les données du formulaire
            $validatedData = $request->validate([
                'nom_produit' => 'required|string|max:255',
                'nom_piece' => 'required|integer',
                'nom_carton' => 'required|integer',
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

            return redirect('admin/magasin/' . $magasin->nom)->with('message', 'Produit Modifié avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


}
