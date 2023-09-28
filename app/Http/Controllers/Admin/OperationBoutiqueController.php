<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Boutique;
use Illuminate\Http\Request;
use App\Models\OpertationBoutique;
use App\Http\Controllers\Controller;

class OperationBoutiqueController extends Controller
{
    public function index($nom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $boutiques = Boutique::get();
            return view('admin.operationBoutique.index', compact('magasin', 'nom', 'boutiques'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function histoBoutique(Request $request, $nom,$nomBoutique)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $magasin = Magasin::where('nom', $nom)->firstOrFail();
            $boutique = Boutique::where('nom', $nomBoutique)->firstOrFail();
            $dateSaisie = $request->input('date_saisie');
            if ($dateSaisie == null) {
                $dateSaisie = date('Y-m-d');
            }
            // Assurez-vous que $dateSaisie est au bon format (ex: '2023-09-15')
            Carbon::setLocale('fr');
            // Utilisez Carbon pour formater la date en français
            $dateFormatee = Carbon::parse($dateSaisie)->isoFormat('dddd, D MMMM YYYY', 'Do MMMM YYYY');
            $operations = OpertationBoutique::where('magasin_id', $magasin->id)
                ->where('boutique_id', $boutique->id)
                ->where('date', $dateSaisie)
                ->get();
            return view('admin.operationBoutique.histomagasinboutique', compact('boutique', 'operations', 'magasin'), [
                'dateFormatee' => ucfirst($dateFormatee),
            ]);
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function create($nom,$nomBoutique)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $produits = Produit::where('piece_totale', '!=', '0')->where('magasin_id', $magasin->id)->orderBy('nom_produit', 'ASC')->get();
            return view('admin.operationBoutique.ajouter', compact('magasin', 'produits', 'nomBoutique'));
        } catch (\Throwable $th) {
            session()->flash('error', $th);
            return redirect('admin/dashboard');
        }
    }

    public function saveOperationBoutique(Request $request, $nom,$nomBoutique)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $magasin = Magasin::where('nom', $nom)->firstOrFail();
            $boutique = Boutique::where('nom', $nomBoutique)->firstOrFail();

            // Validez les données du formulaire
            $validatedData = $request->validate([
                'date' => 'required',
                'nom_piece' => 'required|integer',
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

            // Créez une nouvelle opération boutique
            $operation = new OpertationBoutique();
            $operation->magasin_id = $magasin->id;
            $operation->boutique_id = $boutique->id;
            $operation->produit_id = $validatedData['produit_id'];
            $operation->nombre_piece = $validatedData['nom_piece'];
            $operation->date = $validatedData['date'];
            $operation->save();

            if ($operation) {
                // Mettez à jour le produit existant dans la boutique s'il existe
                $existingProduct = Produit::where('code', $product->code)->where('boutique_id', $boutique->id)->first();

                if ($existingProduct) {
                    $existingProduct->piece_totale += $validatedData['nom_piece'];
                    $existingProduct->prix_unitaire = $product->prix_unitaire;
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
                    $newProduct->prix_unitaire = $product->prix_unitaire;
                    $newProduct->fournisseur_id = $product->fournisseur_id;
                    $newProduct->piece_totale = $validatedData['nom_piece'];
                    $newProduct->boutique_id = $boutique->id;
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

            return redirect('admin/operationBoutique/' . $magasin->nom.'/boutique/'.$nomBoutique)->with('message', 'Produit affecté avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function Historique($nomMagasin, $boutique)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $magasin = Magasin::where('nom', $nomMagasin)->firstOrFail();
            $boutique = Boutique::where('nom', $boutique)->firstOrFail();
            $produit = Produit::where('boutique_id', $boutique->id)->first();
            $operations = OpertationBoutique::where('magasin_id', $magasin->id)
                ->where('boutique_id', $boutique->id)
                ->orderBy('id', 'desc')->get();
            // dd($operations);
            return view('admin.operationBoutique.historiques', compact('boutique', 'operations', 'magasin', 'produit'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function edit($nom,$nomBoutique,$operation_id)
    {
        try {
            if (!OpertationBoutique::where('id', $operation_id)->exists()) {
                session()->flash('error', 'Operation non Trouvée');
                return redirect('admin/operationBoutique/' . $nom.'/boutique/'.$nomBoutique);
            }
            $magasin = Magasin::where('nom', $nom)->first();
            $produits = Produit::where('piece_totale', '!=', '0')->where('magasin_id', $magasin->id)->get();
            $operation = OpertationBoutique::find($operation_id);
            return view('admin.operationBoutique.edit', compact('magasin', 'nomBoutique', 'operation', 'produits', 'operation_id'));
        } catch (\Throwable $th) {
            session()->flash('error', $th);
            return redirect('admin/dashboard');
        }
    }

    public function update(Request $request ,$nom, $nomBoutique, $operation_id)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $magasin = Magasin::where('nom', $nom)->firstOrFail();
            $boutique = Boutique::where('nom', $nomBoutique)->firstOrFail();

            // Validez les données du formulaire
            $validatedData = $request->validate([
                'date' => 'required',
                'nom_piece' => 'required|integer',
                'produit_id' => 'required|integer',
            ]);

            //dd($validatedData['nom_piece'] - $validatedData['nom_piece']);

            // Récupérez le produit en fonction de l'ID
            $product = Produit::find($validatedData['produit_id']);

            if (!$product) {
                return redirect('admin/operation/' . $nomBoutique)->with('error', 'Produit introuvable');
            }

            // Vérifiez si la quantité demandée est supérieure au stock total
            if ($validatedData['nom_piece'] > $product->piece_totale) {
                return redirect('admin/operation/' . $nomBoutique)->with('error', 'La quantité demandée est supérieure au stock total');
            }

            // Effectuez le calcul pour obtenir le nombre de cartons et de pièces ici
            $nombrePieces = $validatedData['nom_piece'] % $product->nombre_piece;
            $nombreCartons = ($validatedData['nom_piece'] - $nombrePieces) / $product->nombre_piece;

            // Créez une nouvelle opération boutique
            $operation = OpertationBoutique::where('id',$operation_id)->first();
            $operation->magasin_id = $magasin->id;
            $operation->boutique_id = $boutique->id;
            $operation->produit_id = $validatedData['produit_id'];
            $operation->nombre_piece = $validatedData['nom_piece'];
            $operation->date = $validatedData['date'];
            $operation->save();

            return redirect('admin/operationBoutique/' . $magasin->nom . '/boutique/' . $nomBoutique)->with('message', 'Produit affecté avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
