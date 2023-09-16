<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Magasin;
use App\Models\Produit;
use Illuminate\Http\Request;
use App\Models\OperationMagasin;
use App\Http\Controllers\Controller;

class OperationMagasinController extends Controller
{
    public function index(Request $request, $nom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            // Récupérez le magasin en fonction du nom
            $magasindepart = Magasin::where('nom', $nom)->firstOrFail();
            // Récupérez la date saisie dans le formulaire
            $dateSaisie = $request->input('date_saisie');
            if ($dateSaisie == null) {
                $dateSaisie = date('Y-m-d');
            }
            // Assurez-vous que $dateSaisie est au bon format (ex: '2023-09-15')
            Carbon::setLocale('fr');
            // Utilisez Carbon pour formater la date en français
            $dateFormatee = Carbon::parse($dateSaisie)->isoFormat('dddd, D MMMM YYYY', 'Do MMMM YYYY');
            $operations = OperationMagasin::where(function ($query) use ($magasindepart) {
                $query->where('magasin_depart', $magasindepart->id)
                    ->orWhere('magasin_arrive', $magasindepart->id);
            })
                ->where('date', $dateSaisie)
                ->orderBy('id', 'DESC')->get();
            foreach ($operations as $key => $value) {
                $operations[$key]->nomMagasinArrive =  Magasin::where('id', $value->magasin_arrive)->first()->nom;
            }
            return view('admin.operationMagasin.index', compact('magasin', 'operations'), [
                'dateFormatee' => ucfirst($dateFormatee),
            ]);
        } catch (\Throwable $th) {
            session()->flash('error', $th);
            return redirect('admin/dashboard');
        }
    }

    public function create($nom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $produits = Produit::where('piece_totale', '!=', '0')->where('magasin_id', $magasin->id)->get();
            $magasins = Magasin::where('nom', '!=', $magasin->nom)->get();
            return view('admin.operationMagasin.create', compact('magasin', 'produits', 'magasins'));
        } catch (\Throwable $th) {
            session()->flash('error', $th);
            return redirect('admin/dashboard');
        }
    }

    public function saveOperationMagasin(Request $request, $nom)
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
                return redirect('admin/operation/' . $magasin->nom)->with('error', 'Produit introuvable');
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

            return redirect('admin/operation/' . $magasin->nom)->with('message', 'Produit affecté avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function historiquesMagasin($nom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            // Récupérez le magasin en fonction du nom
            $magasindepart = Magasin::where('nom', $nom)->firstOrFail();
            $operations = OperationMagasin::where(function ($query) use ($magasindepart) {
                $query->where('magasin_depart', $magasindepart->id)
                    ->orWhere('magasin_arrive', $magasindepart->id);
            })
                ->orderBy('id', 'DESC')->get();
            foreach ($operations as $key => $value) {
                $operations[$key]->nomMagasinArrive =  Magasin::where('id', $value->magasin_arrive)->first()->nom;
            }
            return view('admin.operationMagasin.historiques', compact('magasin', 'operations'));
        } catch (\Throwable $th) {
            session()->flash('error', $th);
            return redirect('admin/dashboard');
        }
    }

    public function edit($nom, $operation_id)
    {
        try {
            if (!OperationMagasin::where('id',$operation_id)->exists()) {
                session()->flash('error', 'Operation non Trouvée');
                return redirect('admin/operation/'.$nom);
            }
            $magasin = Magasin::where('nom', $nom)->first();
            $produits = Produit::where('piece_totale', '!=', '0')->where('magasin_id', $magasin->id)->get();
            $magasins = Magasin::where('nom', '!=', $magasin->nom)->get();
            $operation = OperationMagasin::find($operation_id);
            return view('admin.operationMagasin.edit', compact('magasin', 'operation', 'magasins', 'produits', 'operation_id'));
        } catch (\Throwable $th) {
            session()->flash('error', $th);
            return redirect('admin/dashboard');
        }
    }

    public function update(Request $request ,$nom, $operation_id)
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
                return redirect('admin/operation/' . $magasin->nom)->with('error', 'Produit introuvable');
            }

            // Vérifiez si la quantité demandée est supérieure au stock total
            if ($validatedData['nom_piece'] > $product->piece_totale) {
                return redirect('admin/operation/' . $magasin->nom)->with('error', 'La quantité demandée est supérieure au stock total');
            }

            // Effectuez le calcul pour obtenir le nombre de cartons et de pièces ici
            $nombrePieces = $validatedData['nom_piece'] % $product->nombre_piece;
            $nombreCartons = ($validatedData['nom_piece'] - $nombrePieces) / $product->nombre_piece;

            // Créez une nouvelle opération magasin
            $operation = OperationMagasin::where('id',$operation_id)->first();
            $operation->magasin_depart = $magasin->id;
            $operation->magasin_arrive = $validatedData['magasin_id'];
            $operation->produit_id = $validatedData['produit_id'];
            $operation->nombre_piece = $validatedData['nom_piece'];
            $operation->date = $validatedData['date'];
            $operation->update();



            return redirect('admin/operation/' . $magasin->nom)->with('message', 'Produit affecté avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
