<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Magasin;
use App\Models\Produit;
use Illuminate\Http\Request;
use App\Models\OperationMagasin;
use App\Http\Controllers\Controller;
use App\Models\Gerant;

class OperationMagasinController extends Controller
{
    public function listMagasin($nom,$prenom)
    {
        try {
            //code...
            $magasin = Magasin::where('nom', $nom)->first();
            $gerant = Gerant::where('prenom', $prenom)->first();
            $magasins = Magasin::where('nom','!=', $nom)->get();
            return view('admin.operationMagasin.list',compact('nom','magasin', 'magasins','gerant'));
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error',$th);
            return redirect('admin/dashboard');
        }
    }
    public function index(Request $request, $nom,$prenom, $magasinA)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            // Récupérez le magasin en fonction du nom
            $magasin = Magasin::where('nom', $nom)->firstOrFail();
            // Récupérez la date saisie dans le formulaire

            $magasinArrive = Magasin::where('nom', $magasinA)->firstOrFail();

            $gerant = Gerant::where('prenom', $prenom)->first();
            $dateSaisie = $request->input('date_saisie');
            if ($dateSaisie == null) {
                $dateSaisie = date('Y-m-d');
            }
            // Assurez-vous que $dateSaisie est au bon format (ex: '2023-09-15')
            Carbon::setLocale('fr');
            // Utilisez Carbon pour formater la date en français
            $dateFormatee = Carbon::parse($dateSaisie)->isoFormat('dddd, D MMMM YYYY', 'Do MMMM YYYY');
            $operations = OperationMagasin::where(function ($query) use ($magasin, $magasinArrive) {
                $query->where('magasin_depart', $magasin->id)
                    ->where('magasin_arrive', $magasinArrive->id)
                    ->orWhere('magasin_depart', $magasinArrive->id)
                    ->where('magasin_arrive', $magasin->id);
            })
                ->where('date', $dateSaisie)
                ->orderBy('id', 'DESC')->get();
            foreach ($operations as $key => $value) {
                $operations[$key]->nomMagasinArrive =  Magasin::where('id', $value->magasin_arrive)->first()->nom;
            }
            return view('admin.operationMagasin.index', compact('magasin', 'magasinArrive', 'operations','gerant'), [
                'dateFormatee' => ucfirst($dateFormatee),
            ]);
        } catch (\Throwable $th) {
            session()->flash('error', $th);
            return redirect('admin/dashboard');
        }
    }

    public function create($nom, $prenom, $magasinA)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $gerant = Gerant::where('prenom', $prenom)->first();
            $produits = Produit::where('piece_totale', '!=', '0')->where('magasin_id', $magasin->id)->orderBy('nom_produit','ASC')->get();
            // $magasins = Magasin::where('nom', '!=', $magasin->nom)->get();
            return view('admin.operationMagasin.create', compact('magasin', 'magasinA', 'gerant' , 'produits'));
        } catch (\Throwable $th) {
            session()->flash('error', $th);
            return redirect('admin/dashboard');
        }
    }

    public function saveOperationMagasin(Request $request, $nom, $prenom, $magasinA)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $magasin = Magasin::where('nom', $nom)->firstOrFail();

            $magasinArrive = Magasin::where('nom', $magasinA)->firstOrFail();

            $gerant = Gerant::where('prenom', $prenom)->first();
            // Validez les données du formulaire
            $validatedData = $request->validate([
                'date' => 'required',
                'nom_piece' => 'required|integer',
                'produit_id' => 'required|integer',
            ]);

            // Récupérez le produit en fonction de l'ID
            $product = Produit::find($validatedData['produit_id']);

            if (!$product) {
                return redirect()->back()->with('error', 'Produit introuvable');
            }

            // Vérifiez si la quantité demandée est supérieure au stock total
            if ($validatedData['nom_piece'] > $product->piece_totale) {
                return redirect()->back()->with('error', 'La quantité demandée est supérieure au stock total');
            }

            // Effectuez le calcul pour obtenir le nombre de cartons et de pièces ici
            $nombrePieces = $validatedData['nom_piece'] % $product->nombre_piece;
            $nombreCartons = ($validatedData['nom_piece'] - $nombrePieces) / $product->nombre_piece;

            // Créez une nouvelle opération magasin
            $operation = new OperationMagasin();
            $operation->magasin_depart = $magasin->id;
            $operation->magasin_arrive = $magasinArrive->id;
            $operation->produit_id = $validatedData['produit_id'];
            $operation->nombre_piece = $validatedData['nom_piece'];
            $operation->date = $validatedData['date'];
            $operation->save();

            if ($operation) {
                // Vérifiez si le produit existe déjà dans le magasin d'arrivée
                $existingProduct = Produit::where('code', $product->code)->where('magasin_id', $magasinArrive->id)->first();

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
                    $newProduct->magasin_id = $magasinArrive->id;
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

            return redirect('admin/operation/' . $magasin->nom.'/gerant/'.$gerant->prenom.'/index/'.$magasinArrive->nom)->with('message', 'Produit affecté avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function historiquesMagasin($nom, $prenom, $magasinA)
    {
        try {
            // Récupérez le magasin de départ et d'arrivée en fonction des noms
            $magasin = Magasin::where('nom', $nom)->firstOrFail();
            $magasinArrive = Magasin::where('nom', $magasinA)->firstOrFail();

            // Récupérez toutes les opérations associées aux magasins de départ et d'arrivée
            $operations = OperationMagasin::where(function ($query) use ($magasin, $magasinArrive) {
                $query->where(function ($subquery) use ($magasin, $magasinArrive) {
                    $subquery->where('magasin_depart', $magasin->id)
                        ->where('magasin_arrive', $magasinArrive->id);
                })->orWhere(function ($subquery) use ($magasin, $magasinArrive) {
                    $subquery->where('magasin_depart', $magasinArrive->id)
                        ->where('magasin_arrive', $magasin->id);
                });
            })
                ->orderBy('id', 'DESC')
                ->get();

            return view('admin.operationMagasin.historiques', compact('magasin', 'magasinA', 'prenom', 'operations'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function delete($nom, $prenom, $magasinArrive, $operation_id)
    {
        try {
            $operation = OperationMagasin::find($operation_id);

            if (!$operation) {
                session()->flash('error', 'Opération non trouvée');
                return redirect('admin/operation/' . $nom . '/gerant/' . $prenom .'/historiques/'. $magasinArrive);
            }


            Produit::where('magasin_id', $operation->magasin_arrive)
                    ->delete();

            $operationProduit = Produit::where('id', $operation->produit_id)
                                        ->where('magasin_id',$operation->magasin_depart)
                                        ->first();
            if($operationProduit){

                $operationProduit->nombre_carton += $operation->nombre_piece;
                $operationProduit->piece_totale += $operation->nombre_piece;
                $operationProduit->save();
            }
            $operation->delete();
            return redirect()->back()
                ->with('message', 'Opération supprimée avec succès');
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

}
