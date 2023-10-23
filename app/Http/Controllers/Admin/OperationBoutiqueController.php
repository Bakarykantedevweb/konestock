<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Boutique;
use Illuminate\Http\Request;
use App\Models\OpertationBoutique;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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

    public function histoBoutique(Request $request, $nom, $nomBoutique)
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

    public function create($nom, $nomBoutique)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $produits = Produit::where('nombre_carton', '!=', '0')->where('delete_as', '0')->where('magasin_id', $magasin->id)->orderBy('nom_produit', 'ASC')->get();
            return view('admin.operationBoutique.ajouter', compact('magasin', 'produits', 'nomBoutique'));
        } catch (\Throwable $th) {
            session()->flash('error', $th);
            return redirect('admin/dashboard');
        }
    }
    // ...

    public function saveOperationBoutique(Request $request, $nom, $nomBoutique)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->firstOrFail();
            $boutique = Boutique::where('nom', $nomBoutique)->firstOrFail();

            for ($i = 0; $i < count($request->produit_id); $i++){
                $product = Produit::find($request->produit_id[$i]);

                if (!$product) {
                    return redirect()->back()->with('error', 'Produit introuvable');
                }

                // Vérifiez si la quantité demandée est supérieure au stock total
                if ($request->nom_piece[$i] > $product->nombre_carton) {
                    return redirect()->back()->with('error', 'La quantité demandée est supérieure au stock total');
                }

                // Créez une nouvelle opération boutique
                $operation =  OpertationBoutique::create([
                    'magasin_id' => $magasin->id,
                    'boutique_id' => $boutique->id,
                    'produit_id' => $request->produit_id[$i],
                    'nombre_piece' => $request->nom_piece[$i],
                    'date' => $request->date,
                ]);

                if ($operation) {
                    $existingProduct = Produit::where('code', $product->code)
                    ->where('nom_produit', $product->nom_produit)
                        ->where('prix_unitaire', $product->prix_unitaire)
                        ->where('boutique_id', $boutique->id)->first();

                    if ($existingProduct) {
                        $existingProduct->nombre_carton += $request->nom_piece[$i];
                        $existingProduct->piece_totale += $request->nom_piece[$i];
                        $existingProduct->save();
                    } else {
                        $newProduct = new Produit([
                            'code' => $product->code,
                            'nom_produit' => $product->nom_produit,
                            'nombre_carton' => $request->nom_piece[$i],
                            'prix_unitaire' => $product->prix_unitaire,
                            'fournisseur_id' => $product->fournisseur_id,
                            'piece_totale' => $request->nom_piece[$i],
                            'boutique_id' => $boutique->id
                        ]);

                        $newProduct->save();
                    }

                    $product->nombre_carton -= $request->nom_piece[$i];
                    $product->piece_totale -= $request->nom_piece[$i];
                    $product->save();
                }

            }
            return redirect("admin/operationBoutique/$magasin->nom/boutique/$nomBoutique")->with('message', 'Produit affecté avec succès');
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

    public function delete($nom, $nomBoutique, $operation_id)
    {
        try {
            $operation = OpertationBoutique::find($operation_id);

            if (!$operation) {
                return redirect()
                    ->route('admin.operationBoutique', ['nom' => $nom, 'boutique' => $nomBoutique])
                    ->with('error', 'Opération non trouvée');
            }

            // Obtenez le produit associé à l'opération
            $produit_operation = Produit::where('id', $operation->produit_id)
                ->where('magasin_id', $operation->magasin_id)
                ->first();
            // dd($produit_operation);
            if ($produit_operation) {
                // Mettez à jour le produit
                $produit_operation->nombre_carton += $operation->nombre_piece;
                $produit_operation->piece_totale += $operation->nombre_piece;
                $produit_operation->save();
            }

            // Supprimez l'opération
            $operation->delete();

            Produit::where('boutique_id', $operation->boutique_id)
                ->where('nom_produit', $operation->produit->nom_produit)
                ->delete();

            return redirect()->back()
                ->with('message', 'Opération supprimée avec succès');
        } catch (\Throwable $th) {
            return redirect()->route('admin.dashboard')->with('error', $th->getMessage());
        }
    }


    // public function update(Request $request ,$nom, $nomBoutique, $operation_id)
    // {
    //     try {
    //         // Récupérez le magasin en fonction du nom
    //         $magasin = Magasin::where('nom', $nom)->firstOrFail();
    //         $boutique = Boutique::where('nom', $nomBoutique)->firstOrFail();

    //         // Validez les données du formulaire
    //         $validatedData = $request->validate([
    //             'date' => 'required',
    //             'nom_piece' => 'required|integer',
    //             'produit_id' => 'required|integer',
    //         ]);

    //         //dd($request->nom_piece[$i] - $request->nom_piece[$i]);

    //         // Récupérez le produit en fonction de l'ID
    //         $product = Produit::find($request->produit[$i]);

    //         if (!$product) {
    //             return redirect('admin/operation/' . $nomBoutique)->with('error', 'Produit introuvable');
    //         }

    //         // Vérifiez si la quantité demandée est supérieure au stock total
    //         if ($request->nom_piece[$i] > $product->piece_totale) {
    //             return redirect('admin/operation/' . $nomBoutique)->with('error', 'La quantité demandée est supérieure au stock total');
    //         }

    //         // Effectuez le calcul pour obtenir le nombre de cartons et de pièces ici
    //         $nombrePieces = $request->nom_piece[$i] % $product->nom_piece;
    //         $nombreCartons = ($request->nom_piece[$i] - $nombrePieces) / $product->nom_piece;

    //         // Créez une nouvelle opération boutique
    //         $operation = OpertationBoutique::where('id',$operation_id)->first();
    //         $operation->magasin_id = $magasin->id;
    //         $operation->boutique_id = $boutique->id;
    //         $operation->produit_id = $request->produit[$i];
    //         $operation->nom_piece = $request->nom_piece[$i];
    //         $operation->date = $validatedData['date'];
    //         $operation->save();

    //         return redirect('admin/operationBoutique/' . $magasin->nom . '/boutique/' . $nomBoutique)->with('message', 'Produit affecté avec succès');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', $e->getMessage());
    //     }
    // }
}
