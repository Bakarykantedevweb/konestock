<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Boutique;
use Illuminate\Http\Request;
use App\Models\BoutiqueEnBoutique;
use App\Http\Controllers\Controller;

class BoutiqueEnBoutiqueController extends Controller
{
    public function index($nom)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->first();
            $boutiques = Boutique::where('nom','!=', $nom)->get();
            return view('admin.boutiqueEnboutique.index', compact('boutique', 'nom', 'boutiques'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function list(Request $request, $nom, $nomBoutique)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->first();
            $boutiqueArrive = Boutique::where('nom', $nomBoutique)->first();

            // Récupérez la date saisie dans le formulaire ou utilisez la date actuelle si elle est nulle
            $dateSaisie = $request->input('date_saisie', date('Y-m-d'));

            // Assurez-vous que $dateSaisie est au bon format (ex: '2023-09-15')
            Carbon::setLocale('fr');
            $dateFormatee = Carbon::parse($dateSaisie)->isoFormat('dddd, D MMMM YYYY', 'Do MMMM YYYY');

            // Récupérez les opérations de transfert
            $operations = BoutiqueEnBoutique::where(function ($query) use ($boutique, $boutiqueArrive) {
                $query->where('boutique_depart', $boutique->id)
                    ->where('boutique_arrive', $boutiqueArrive->id)
                    ->orWhere('boutique_depart', $boutiqueArrive->id)
                    ->where('boutique_arrive', $boutique->id);
            })
                ->whereDate('date', $dateSaisie)
                ->orderBy('id', 'DESC')
                ->get();
            return view('admin.boutiqueEnboutique.list', compact('boutique', 'nom', 'nomBoutique', 'operations', 'dateFormatee'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function historique($nom, $nomBoutique)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->first();
            $boutiqueArrive = Boutique::where('nom', $nomBoutique)->first();
            // Récupérez les opérations de transfert
            $operations = BoutiqueEnBoutique::where(function ($query) use ($boutique, $boutiqueArrive) {
                $query->where('boutique_depart', $boutique->id)
                    ->where('boutique_arrive', $boutiqueArrive->id)
                    ->orWhere('boutique_depart', $boutiqueArrive->id)
                    ->where('boutique_arrive', $boutique->id);
            })
                ->orderBy('id', 'DESC')
                ->get();
            return view('admin.boutiqueEnboutique.historique', compact('boutique', 'nom', 'nomBoutique', 'operations'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }


    public function create($nom,$nomBoutique)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->first();
            $boutiques = Boutique::where('nom', '!=', $nom)->get();
            $produits = Produit::where('boutique_id',$boutique->id)->orderBy('nom_produit','ASC')->get();
            return view('admin.boutiqueEnboutique.create', compact('boutique', 'nomBoutique', 'nom', 'boutiques','produits'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function save(Request $request,$nom, $nomBoutique)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $boutique = Boutique::where('nom', $nom)->firstOrFail();

            $boutiqueArrive = Boutique::where('nom', $nomBoutique)->firstOrFail();

            // Validez les données du formulaire
            $validatedData = $request->validate([
                'date' => 'required',
                'nom_piece' => 'required|integer',
                'produit_id' => 'required|integer',
            ]);

            // Récupérez le produit en fonction de l'ID
            $product = Produit::find($validatedData['produit_id']);

            if (!$product) {
                return redirect('admin/operation/' . $boutique->nom)->with('error', 'Produit introuvable');
            }

            // Vérifiez si la quantité demandée est supérieure au stock total
            if ($validatedData['nom_piece'] > $product->piece_totale) {
                return redirect('admin/boutique/' . $boutique->nom)->with('error', 'La quantité demandée est supérieure au stock total');
            }

            // Effectuez le calcul pour obtenir le nombre de cartons et de pièces ici
            $nombrePieces = $validatedData['nom_piece'] % $product->nombre_piece;
            $nombreCartons = ($validatedData['nom_piece'] - $nombrePieces) / $product->nombre_piece;

            // Créez une nouvelle opération boutique
            $operation = new BoutiqueEnBoutique();
            $operation->boutique_depart = $boutique->id;
            $operation->boutique_arrive = $boutiqueArrive->id;
            $operation->produit_id = $validatedData['produit_id'];
            $operation->nombre_piece = $validatedData['nom_piece'];
            $operation->date = $validatedData['date'];
            $operation->save();

            if ($operation) {
                // Vérifiez si le produit existe déjà dans le boutique d'arrivée
                $existingProduct = Produit::where('code', $product->code)->where('boutique_id', $boutiqueArrive->id)->first();

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
                    // Créez un nouveau produit dans le boutique d'arrivée s'il n'existe pas encore
                    $newProduct = new Produit();
                    $newProduct->code = $product->code;
                    $newProduct->nom_produit = $product->nom_produit;
                    $newProduct->nombre_piece = $product->nombre_piece;
                    $newProduct->nombre_carton = $nombreCartons;
                    $newProduct->prix_unitaire = $product->prix_unitaire;
                    $newProduct->fournisseur_id = $product->fournisseur_id;
                    $newProduct->piece_totale = $validatedData['nom_piece'];
                    $newProduct->boutique_id = $boutiqueArrive->id;
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

            return redirect('admin/boutique/' . $boutique->nom.'/operation/'.$nomBoutique)->with('message', 'Produit affecté avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
