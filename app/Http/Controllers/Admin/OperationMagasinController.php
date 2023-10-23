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
    public function listMagasin($nom, $prenom)
    {
        try {
            //code...
            $magasin = Magasin::where('nom', $nom)->first();
            $gerant = Gerant::where('prenom', $prenom)->first();
            $magasins = Magasin::where('nom', '!=', $nom)->get();
            return view('admin.operationMagasin.list', compact('nom', 'magasin', 'magasins', 'gerant'));
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', $th);
            return redirect('admin/dashboard');
        }
    }
    public function index(Request $request, $nom, $prenom, $magasinA)
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
            return view('admin.operationMagasin.index', compact('magasin', 'magasinArrive', 'operations', 'gerant'), [
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
            $produits = Produit::where('magasin_id', $magasin->id)
                ->where('delete_as', '0')
                ->where('nombre_carton', '!=', '0')
                ->orderBy('nom_produit', 'ASC')->get();
            // $magasins = Magasin::where('nom', '!=', $magasin->nom)->get();
            return view('admin.operationMagasin.create', compact('magasin', 'magasinA', 'gerant', 'produits'));
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

            for ($i = 0; $i < count($request->produit_id); $i++) {
                $product = Produit::find($request->produit_id[$i]);

                if (!$product) {
                    return redirect()->back()->with('error', 'Produit introuvable');
                }

                // Vérifiez si la quantité demandée est supérieure au stock total
                if ($request->nom_piece[$i] > $product->nombre_carton) {
                    return redirect()->back()->with('error', 'La quantité demandée est supérieure au stock total');
                }

                $operation = new OperationMagasin([
                    'magasin_depart' => $magasin->id,
                    'magasin_arrive' => $magasinArrive->id,
                    'produit_id' => $request->produit_id[$i],
                    'nombre_piece' => $request->nom_piece[$i],
                    'date' => $request->date,
                ]);

                $operation->save();

                if ($operation) {
                    $existingProduct = Produit::where('code', $product->code)
                        ->where('nom_produit', $product->nom_produit)
                        ->where('prix_unitaire', $product->prix_unitaire)
                        ->where('magasin_id', $magasinArrive->id)->first();

                    if ($existingProduct) {
                        $existingProduct->nombre_carton += $request->nom_piece[$i];
                        $existingProduct->save();
                    } else {
                        $newProduct = new Produit([
                            'code' => $product->code,
                            'nom_produit' => $product->nom_produit,
                            'nombre_carton' => $request->nom_piece[$i],
                            'prix_unitaire' => $product->prix_unitaire,
                            'fournisseur_id' => $product->fournisseur_id,
                            'piece_totale' => $request->nom_piece[$i],
                            'magasin_id' => $magasinArrive->id,
                        ]);

                        $newProduct->save();
                    }

                    $product->nombre_carton -= $request->nom_piece[$i];
                    $product->piece_totale -= $request->nom_piece[$i];
                    $product->save();
                }
            }

            $route = 'admin/operation/' . $magasin->nom . '/gerant/' . $gerant->prenom . '/index/' . $magasinArrive->nom;
            return redirect($route)->with('message', 'Produit affecté avec succès');
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
                return redirect('admin/operation/' . $nom . '/gerant/' . $prenom . '/historiques/' . $magasinArrive);
            }


            $operationProduit = Produit::where('id', $operation->produit_id)
                ->where('magasin_id', $operation->magasin_depart)
                ->first();
            if ($operationProduit) {

                $operationProduit->nombre_carton += $operation->nombre_piece;
                $operationProduit->piece_totale += $operation->nombre_piece;
                $operationProduit->save();

                Produit::where('magasin_id', $operation->magasin_arrive)
                        ->where('nom_produit', $operation->produit->nom_produit)
                        ->delete();
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
