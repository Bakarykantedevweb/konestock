<?php

namespace App\Http\Controllers\Admin;

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
            ->get();
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

    public function produitMag($nom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $produits = Produit::where('piece_totale', '!=' , '0')->where('magasin_id', $magasin->id)->get();
            $magasins = Magasin::where('nom', '!=', $magasin->nom)->get();
            return view('admin.magasin.produit-magasin', compact('magasin', 'produits', 'magasins'));
        } catch (\Throwable $th) {
            session()->flash('error', $th);
            return redirect('admin/dashboard');
        }
    }

    public function produitMagSave(Request $request, $nom)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $magasin = Magasin::where('nom', $nom)->firstOrFail();

            // Validez les données du formulaire
            $validatedData = $request->validate([
                'date' => 'required',
                'nom_piece' => 'required|integer',
                'magasin_id' => 'required|integer',
                'produit_id' => 'required|integer',
            ]);

            // Récupérez le produit en fonction de l'ID
            $product = Produit::find($validatedData['produit_id']);

            if (!$product) {
                return redirect('admin/magasin/' . $magasin->nom)->with('error', 'Produit introuvable');
            }

            // Vérifiez si la quantité demandée est supérieure au stock total
            if ($validatedData['nom_piece'] > $product->piece_totale) {
                return redirect('admin/magasin/' . $magasin->nom)->with('error', 'La quantité demandée est supérieure au stock total');
            }

            // Effectuez le calcul pour obtenir le nombre de cartons et de pièces ici
            $nombrePieces = $validatedData['nom_piece'] % $product->nombre_piece;
            $nombreCartons = ($validatedData['nom_piece'] - $nombrePieces) / $product->nombre_piece;

            // Créez une nouvelle opération magasin
            $operation = new OperationMagasin();
            $operation->magasin_depart = $magasin->id;
            $operation->magasin_arrive = $validatedData['magasin_id'];
            $operation->produit_id = $validatedData['produit_id'];
            $operation->nombre_piece = $validatedData['nom_piece'];
            $operation->date = $validatedData['date'];
            $operation->save();

            if ($operation) {
                // Vérifiez si le produit existe déjà dans le magasin d'arrivée
                $existingProduct = Produit::where('code', $product->code)->where('magasin_id', $validatedData['magasin_id'])->first();

                if ($existingProduct) {
                    $existingProduct->piece_totale += $validatedData['nom_piece'];

                    $nombrePiecesUpdate = $existingProduct->piece_totale % $product->nombre_piece;
                    $nombreCartonsUpdate = ($existingProduct->piece_totale - $nombrePiecesUpdate) / $product->nombre_piece;

                    $existingProduct->nombre_carton = $nombreCartonsUpdate;
                    $existingProduct->update();

                    // Mettez à jour le produit d'origine
                    $product->piece_totale -= $validatedData['nom_piece'];

                    $nombrePiecesUpdate = $product->piece_totale % $product->nombre_piece;
                    $nombreCartonsUpdate = ($product->piece_totale - $nombrePiecesUpdate) / $product->nombre_piece;

                    $product->nombre_carton = $nombreCartonsUpdate;
                    $product->update();
                } else {
                    // Créez un nouveau produit dans le magasin d'arrivée s'il n'existe pas encore
                    $newProduct = new Produit();
                    $newProduct->code = $product->code;
                    $newProduct->nom_produit = $product->nom_produit;
                    $newProduct->nombre_piece = $product->nombre_piece;
                    $newProduct->nombre_carton = $nombreCartons;
                    $newProduct->prix_unitaire = $product->prix_unitaire;
                    $newProduct->fournisseur_id = $product->fournisseur_id;
                    $newProduct->piece_totale = $validatedData['nom_piece'];
                    $newProduct->magasin_id = $validatedData['magasin_id'];
                    $newProduct->save();

                    if ($newProduct) {
                        // Mettez à jour le produit d'origine
                        $product->piece_totale -= $validatedData['nom_piece'];

                        $nombrePiecesUpdate = $product->piece_totale % $product->nombre_piece;
                        $nombreCartonsUpdate = ($product->piece_totale - $nombrePiecesUpdate) / $product->nombre_piece;

                        $product->nombre_carton = $nombreCartonsUpdate;
                        $product->update();
                    }
                }
            }

            return redirect('admin/magasin/' . $magasin->nom)->with('message', 'Produit affecté avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function produitBout($nom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $produits = Produit::where('piece_totale', '!=', '0')->where('magasin_id', $magasin->id)->get();
            $boutiques = Boutique::get();
        return view('admin.magasin.produit-magasin-boutique',compact('magasin','produits', 'boutiques'));
        } catch (\Throwable $th) {
            session()->flash('error', $th);
            return redirect('admin/dashboard');
        }
    }

    public function produitBoutSave(Request $request, $nom)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $magasin = Magasin::where('nom', $nom)->firstOrFail();

            // Validez les données du formulaire
            $validatedData = $request->validate([
                'date' => 'required',
                'nom_piece' => 'required|integer',
                'boutique_id' => 'required|integer',
                'produit_id' => 'required|integer',
                'prix_unitaire' => 'required'
            ]);

            // Récupérez le produit en fonction de l'ID
            $product = Produit::find($validatedData['produit_id']);

            if (!$product) {
                return redirect('admin/magasin/' . $magasin->nom)->with('error', 'Produit introuvable');
            }

            // Vérifiez si la quantité demandée est supérieure au stock total
            if ($validatedData['nom_piece'] > $product->piece_totale) {
                return redirect('admin/magasin/' . $magasin->nom)->with('error', 'La quantité demandée est supérieure au stock total');
            }

            // Effectuez le calcul pour obtenir le nombre de cartons et de pièces ici
            $nombrePieces = $validatedData['nom_piece'] % $product->nombre_piece;
            $nombreCartons = ($validatedData['nom_piece'] - $nombrePieces) / $product->nombre_piece;

            // Créez une nouvelle opération boutique
            $operation = new OpertationBoutique();
            $operation->magasin_id = $magasin->id;
            $operation->boutique_id = $validatedData['boutique_id'];
            $operation->produit_id = $validatedData['produit_id'];
            $operation->nombre_piece = $validatedData['nom_piece'];
            $operation->date = $validatedData['date'];
            $operation->save();

            if ($operation) {
                // Mettez à jour le produit existant dans la boutique s'il existe
                $existingProduct = Produit::where('code', $product->code)->where('boutique_id', $validatedData['boutique_id'])->first();

                if ($existingProduct) {
                    $existingProduct->piece_totale += $validatedData['nom_piece'];

                    $nombrePiecesUpdate = $existingProduct->piece_totale % $product->nombre_piece;
                    $nombreCartonsUpdate = ($existingProduct->piece_totale - $nombrePiecesUpdate) / $product->nombre_piece;

                    $existingProduct->nombre_carton = $nombreCartonsUpdate;
                    $existingProduct->update();

                    // Mettez à jour le produit d'origine
                    $product->piece_totale -= $validatedData['nom_piece'];

                    $nombrePiecesUpdate = $product->piece_totale % $product->nombre_piece;
                    $nombreCartonsUpdate = ($product->piece_totale - $nombrePiecesUpdate) / $product->nombre_piece;

                    $product->nombre_carton = $nombreCartonsUpdate;
                    $product->update();
                } else {
                    // Créez un nouveau produit dans la boutique s'il n'existe pas encore
                    $newProduct = new Produit();
                    $newProduct->code = $product->code;
                    $newProduct->nom_produit = $product->nom_produit;
                    $newProduct->nombre_piece = $product->nombre_piece;
                    $newProduct->nombre_carton = $nombreCartons;
                    $newProduct->prix_unitaire = $validatedData['prix_unitaire'];
                    $newProduct->fournisseur_id = $product->fournisseur_id;
                    $newProduct->piece_totale = $validatedData['nom_piece'];
                    $newProduct->boutique_id = $validatedData['boutique_id'];
                    $newProduct->save();

                    if ($newProduct) {
                        // Mettez à jour le produit d'origine
                        $product->piece_totale -= $validatedData['nom_piece'];

                        $nombrePiecesUpdate = $product->piece_totale % $product->nombre_piece;
                        $nombreCartonsUpdate = ($product->piece_totale - $nombrePiecesUpdate) / $product->nombre_piece;

                        $product->nombre_carton = $nombreCartonsUpdate;
                        $product->update();
                    }
                }
            }

            return redirect('admin/magasin/' . $magasin->nom)->with('message', 'Produit affecté avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function historique(Request $request, $nom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $magasins = Magasin::where('nom', '!=', $nom)->get();
            return view('admin.magasin.historique', compact('magasins', 'magasin','nom'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function historiqueMag($nomMagasin, $MagasinArrive)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $magasindepart = Magasin::where('nom', $nomMagasin)->firstOrFail();

            $magasinArrive = Magasin::where('nom', $MagasinArrive)->firstOrFail();
            // Recherchez les opérations liées au magasin en tant que magasin de départ ou magasin d'arrivée pour la date donnée
            $operations = OperationMagasin::where(function ($query) use ($magasindepart, $magasinArrive) {
                $query->where('magasin_depart', $magasindepart->id)
                    ->where('magasin_arrive',$magasinArrive->id)
                    ->orWhere('magasin_depart', $magasinArrive->id)
                    ->where('magasin_arrive', $magasindepart->id);
            })
            ->where('date',date('Y-m-d'))
                ->get();
            // dd($operations);
            return view('admin.magasin.histomagasin', compact('MagasinArrive', 'operations', 'magasindepart'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function historiqueMagTout($nomMagasin, $MagasinArrive)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $magasindepart = Magasin::where('nom', $nomMagasin)->firstOrFail();

            $magasinArrive = Magasin::where('nom', $MagasinArrive)->firstOrFail();
            // Recherchez les opérations liées au magasin en tant que magasin de départ ou magasin d'arrivée pour la date donnée
            $operations = OperationMagasin::where(function ($query) use ($magasindepart, $magasinArrive) {
                $query->where('magasin_depart', $magasindepart->id)
                    ->where('magasin_arrive', $magasinArrive->id)
                    ->orWhere('magasin_depart', $magasinArrive->id)
                    ->where('magasin_arrive', $magasindepart->id);
            })
                ->get();
            // dd($operations);
            return view('admin.magasin.histo-magasin-tout', compact('MagasinArrive', 'operations', 'magasindepart'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function commande($nom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $produits = Produit::where('piece_totale', '!=', '0')->where('magasin_id', $magasin->id)->get();
            return view('admin.magasin.commande',compact('magasin','produits'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function savecommande(Request $request, $nomMagasin)
    {
        try {
            $magasin = Magasin::where('nom', $nomMagasin)->first();
            // Validez les données du formulaire
            $validatedData = $request->validate([
                'date' => 'required',
                'nom' => 'required',
                'prenom' => 'required',
                'produits' => 'required',
                'quantite' => 'required',
            ]);
            // Récupérez la commande (vous devez ajouter la logique pour obtenir la commande appropriée en fonction du nom du magasin)
            $commande = new Commande(); // Exemple : créer une nouvelle commande
            $commande->magasin_id = $magasin->id;
            $commande->nom = $validatedData['nom'];
            $commande->prenom = $validatedData['prenom'];
            $commande->date = $validatedData['date'];
            $commande->save();
            $latestProduitId = Commande::latest('id')->first()->id;
            $numero = 'CD' . str_pad($latestProduitId, 4, '0', STR_PAD_LEFT);
            $commande->numero = $numero;
            $commande->save();

            if ($commande) {
                for ($i = 0; $i < count($validatedData['produits']); $i++) {
                    DB::table('commande_produit')->insert([
                        'produit_id' => $validatedData['produits'][$i],
                        'commande_id' => $commande->id,
                        'quantite' => $validatedData['quantite'][$i]
                    ]);

                    // Mettez à jour la quantité du produit à l'intérieur de la boucle
                    $update = Produit::where('id', $validatedData['produits'][$i])->first();
                    $update->piece_totale -= $validatedData['quantite'][$i];

                    // Mettez à jour le nombre de cartons en fonction de la nouvelle quantité
                    $nombrePiecesUpdate = $update->piece_totale % $update->nombre_piece;
                    $nombreCartonsUpdate = ($update->piece_totale - $nombrePiecesUpdate) / $update->nombre_piece;
                    $update->nombre_carton = $nombreCartonsUpdate;
                    $update->save();
                }
            }

            return redirect('admin/magasin/' . $magasin->nom.'/commande-list')->with('message', 'Produits ajoutés avec succès à la commande.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage()); //
        }
    }

    public function commandeList($nom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $commandes = Commande::where('magasin_id',$magasin->id)->get();
            return view('admin.magasin.commande-list', compact('magasin', 'commandes'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
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
