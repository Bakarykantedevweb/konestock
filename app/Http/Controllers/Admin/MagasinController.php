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
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            $rechercheProduit = Produit::where('magasin_id', $magasin->id)
                                ->where('delete_as', '0')
                                ->orderBy('nom_produit','ASC')->get();
            $produits = Produit::where('magasin_id',$magasin->id)
                        ->where('delete_as','0')
                    ->when($req->nom_produit != null, function ($q) use ($req) {
                    return $q->where('nom_produit', $req->nom_produit);
                })
            ->orderBy('nom_produit','ASC')
            ->get();
            return view('admin.magasin.magasin',compact('magasin', 'produits','gerant', 'rechercheProduit'));
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
            $rechercheProduit = Produit::where('magasin_id', $magasin->id)
                                ->where('delete_as', '0')
                                ->orderBy('nom_produit', 'ASC')->get();
            return view('admin.magasin.produitajout', compact('magasin', 'prenom', 'fournisseurs', 'rechercheProduit'));
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
                'nom_carton' => 'required',
                'fournisseur_id' => 'required|integer',
                'prix_unitaire' => 'required'
            ]);
            // Vérifiez si le produit existe en utilisant le nom du produit et l'ID du magasin
            $product = Produit::where('nom_produit', $validatedData['nom_produit'])
            ->where('magasin_id', $magasin->id)
            ->where('delete_as', '0')
            ->first();
            // Créez un nouvel objet Produit et affectez les valeurs
            if($product)
            {
                // Le produit existe, mettez à jour ses attributs
                $product->nombre_carton = $product->nombre_carton + $validatedData['nom_carton'];
                $product->prix_unitaire = $validatedData['prix_unitaire'];
                $product->piece_totale = $product->nombre_carton;
                $product->update();
                return redirect()->back()->with('message', 'Produit modifié avec succès');
            }
            else{
                $produit = new Produit();
                $produit->nom_produit = $validatedData['nom_produit'];
                $produit->nombre_carton = $validatedData['nom_carton'];
                $produit->prix_unitaire = $validatedData['prix_unitaire'];
                $produit->fournisseur_id = $validatedData['fournisseur_id'];
                $produit->piece_totale = $request->nom_carton * $validatedData['prix_unitaire'];
                $produit->magasin_id = $magasin->id;
                $produit->save();
                $latestProduitId = Produit::latest('id')->first()->id;
                $code = 'PR' . str_pad($latestProduitId, 4, '0', STR_PAD_LEFT);
                $produit->code = $code;
                $produit->save();
            }

            return redirect()->back()->with('message', 'Produit ajouté avec succès');
            // return redirect('admin/magasin/' . $magasin->nom)->with('message', 'Produit ajouté avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function produitEdit($nom, $prenom, $code)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->firstOrFail();
            $produit = Produit::where('code', $code)->where('magasin_id',$magasin->id)->firstOrFail();
            $fournisseurs = Fournisseur::get();

            return view('admin.magasin.produit-edit', compact('magasin', 'prenom', 'produit', 'fournisseurs'));
        } catch (ModelNotFoundException $e) {
            session()->flash('error', 'Magasin ou produit non trouvé.');
            return redirect('admin/dashboard');
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

            $produitCode = Produit::where('code', $code)->where('delete_as', '0')->where('magasin_id', $magasin->id)->first();
            // Validez les données du formulaire
            $validatedData = $request->validate([
                'nom_produit' => 'required|string|max:255',
                'nom_carton' => 'required|',
                'fournisseur_id' => 'required|integer',
                'prix_unitaire' => 'required'
            ]);

            // Créez un nouvel objet Produit et affectez les valeurs
            $produit = Produit::where('id',$produitCode->id)->first();;
            $produit->nom_produit = $validatedData['nom_produit'];
            $produit->nombre_carton = $request->nom_carton;
            $produit->prix_unitaire = $validatedData['prix_unitaire'];
            $produit->fournisseur_id = $validatedData['fournisseur_id'];
            $produit->piece_totale = $request->nom_carton;
            $produit->magasin_id = $magasin->id;
            $produit->save();

            return redirect('admin/magasin/' . $magasin->nom . '/gerant/' . $prenom )->with('message', 'Produit Modifié avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function produitDelete($nomMagasin, $prenomGerant, $codeProduit)
    {
        try {
            // Rechercher le magasin par nom
            $magasin = Magasin::where('nom', $nomMagasin)->firstOrFail();

            // Rechercher le produit par code et vérifier s'il appartient au magasin
            $produit = Produit::where('code', $codeProduit)
                ->where('magasin_id', $magasin->id)
                ->firstOrFail();

            // Supprimer le produit
            $produit->delete_as = 1;
            $produit->update();

            // Rediriger avec un message de succès
            return redirect("admin/magasin/{$nomMagasin}/gerant/{$prenomGerant}")->with('message', 'Produit supprimé avec succès');
        } catch (\Throwable $th) {
            // Gérer les erreurs en flashant un message d'erreur
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }


    public function corbeille()
    {
        try{
            $magasins = Magasin::get();
            return view('admin.magasin.magasin-corbeille', compact('magasins'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function corbeilleMagasin($nom)
    {
        try {
            $magasin = Magasin::where('nom',$nom)->first();
            $produits = Produit::where('delete_as', '1')
                ->orderBy('nom_produit', 'ASC')
                ->where('magasin_id',$magasin->id)
                ->get();
            return view('admin.magasin.corbeille', compact('produits','magasin'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function Annulercorbeille($produit_id,$nom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $produit = Produit::where('id', $produit_id)
            ->where('magasin_id', $magasin->id)
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
