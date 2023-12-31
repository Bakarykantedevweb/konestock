<?php

namespace App\Http\Controllers\Admin;

use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Boutique;
use App\Models\Commande;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use App\Models\OpertationBoutique;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BoutiqueController extends Controller
{
    public function index()
    {
        return view('admin.boutique.index');
    }

    public function boutique(Request $req, $nom)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->first();
            $rechercheProduit = Produit::where('boutique_id', $boutique->id)->where('delete_as', '0')->orderBy('nom_produit', 'ASC')->get();
            $produits = Produit::where('boutique_id', $boutique->id)

                ->when($req->code != null, function ($q) use ($req) {
                    return $q->where('nom_produit', $req->code);
                })
                ->where('delete_as', '0')
                ->orderBy('nom_produit','ASC')
                ->get();
            return view('admin.boutique.boutique', compact('boutique', 'produits', 'rechercheProduit'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function create($nom)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->first();
            $rechercheProduit = Produit::where('boutique_id', $boutique->id)
                ->where('delete_as', '0')
                ->orderBy('nom_produit', 'ASC')->get();
            $fournisseurs = Fournisseur::get();
            return view('admin.boutique.produit-create', compact('boutique', 'fournisseurs', 'rechercheProduit'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function save(Request $request, $nom)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $boutique = Boutique::where('nom', $nom)->firstOrFail();
            // Validez les données du formulaire
            $validatedData = $request->validate([
                'nom_produit' => 'required|string|max:255',
                'nom_carton' => 'required',
                'fournisseur_id' => 'required|integer',
                'prix_unitaire' => 'required'
            ]);

            $product = Produit::where('nom_produit', $validatedData['nom_produit'])
            ->where('boutique_id', $boutique->id)
            ->where('prix_unitaire', $validatedData['prix_unitaire'])
            ->where('delete_as', '0')
            ->first();
            if ($product) {
                // Le produit existe, mettez à jour ses attributs
                $product->nombre_carton = $product->nombre_carton + $validatedData['nom_carton'];
                // $product->prix_unitaire = $validatedData['prix_unitaire'];
                $product->piece_totale = $product->nombre_carton;
                $product->update();
                return redirect()->back()->with('message', 'Produit modifié avec succès');
            }
            else
            {
                // Créez un nouvel objet Produit et affectez les valeurs
                $produit = new Produit();
                $produit->nom_produit = $validatedData['nom_produit'];
                $produit->nombre_carton = $validatedData['nom_carton'];
                $produit->prix_unitaire = $validatedData['prix_unitaire'];
                $produit->fournisseur_id = $validatedData['fournisseur_id'];
                $produit->piece_totale = $validatedData['nom_carton'];
                $produit->boutique_id = $boutique->id;
                $produit->save();
                $latestProduitId = Produit::latest('id')->first()->id;
                $code = 'PR' . str_pad($latestProduitId, 4, '0', STR_PAD_LEFT);
                $produit->code = $code;
                $produit->save();
            }

            return redirect()->back()->with('message', 'Produit ajouté avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($nom, $code)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->firstOrFail();
            $produit = Produit::where('code', $code)->where('boutique_id',$boutique->id)->firstOrFail();
            $fournisseurs = Fournisseur::get();
            return view('admin.boutique.produit-edit', compact('boutique', 'produit', 'fournisseurs'));
        } catch (ModelNotFoundException $e) {
            session()->flash('error', 'Boutique ou produit non trouvé.');
            return redirect('admin/dashboard');
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }


    public function update(Request $request, $nom, $code)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $boutique = Boutique::where('nom', $nom)->firstOrFail();

            $produit = Produit::where('code', $code)->where('boutique_id', $boutique->id)->firstOrFail();

            // Validez les données du formulaire
            $validatedData = $request->validate([
                'nom_produit' => 'required|string|max:255',
                'nom_carton' => 'required',
                'fournisseur_id' => 'required|integer',
                'prix_unitaire' => 'required'
            ]);

            // Mettez à jour les propriétés du produit
            $produit->nom_produit = $validatedData['nom_produit'];
            $produit->nombre_carton = $validatedData['nom_carton'];
            $produit->prix_unitaire = $validatedData['prix_unitaire'];
            $produit->fournisseur_id = $validatedData['fournisseur_id'];
            $produit->piece_totale = $validatedData['nom_carton'];
            $produit->boutique_id = $boutique->id;

            // Enregistrez les modifications
            $produit->save();

            return redirect('admin/boutique/' . $boutique->nom)->with('message', 'Produit Modifié avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function delete($nom, $code)
    {
        try {
            // Récupérer la boutique en fonction du nom
            $boutique = Boutique::where('nom', $nom)->firstOrFail();

            // Récupérer le produit en fonction du code
            $produit = Produit::where('code', $code)
                ->where('boutique_id', $boutique->id)
                ->firstOrFail();

            // Supprimer le produit
            $produit->delete();

            // Rediriger avec un message de succès
            return redirect('admin/boutique/' . $boutique->nom)->with('message', 'Produit supprimé avec succès');
        } catch (\Throwable $th) {
            // Gérer les erreurs en flashant un message d'erreur
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function corbeilleBoutique($nom)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->first();
            $produits = Produit::where('delete_as', '1')
                ->orderBy('nom_produit', 'ASC')
                ->where('boutique_id', $boutique->id)
                ->get();
            return view('admin.boutique.corbeille', compact('produits', 'boutique'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function AnnulercorbeilleBoutique($produit_id, $nom)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->first();
            $produit = Produit::where('id', $produit_id)
                ->where('boutique_id', $boutique->id)
                ->first();
            $produit->delete_as = 0;
            $produit->update();
            session()->flash('message', 'Operation effectue avec success');
            return redirect()->back();
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

}
