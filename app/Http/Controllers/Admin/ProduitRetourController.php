<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Boutique;
use Illuminate\Http\Request;
use App\Models\OpertationBoutique;
use App\Http\Controllers\Controller;

class ProduitRetourController extends Controller
{
    public function index($nom)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->first();
            $magasins = Magasin::get();
            return view('admin.ProduitRetour.index', compact('boutique', 'nom', 'magasins'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function list(Request $request, $nom, $nomMagasin)
    {
        try {
            // Récupérez la boutique et le magasin en utilisant les noms fournis
            $boutique = Boutique::where('nom', $nom)->first();
            $magasin = Magasin::where('nom', $nomMagasin)->first();

            // Récupérez la date de saisie à partir de la requête ou utilisez la date actuelle
            $dateSaisie = $request->input('date_saisie', date('Y-m-d'));

            // Assurez-vous que $dateSaisie est au bon format (ex: '2023-09-15')
            Carbon::setLocale('fr');

            // Utilisez Carbon pour formater la date en français
            $dateFormatee = Carbon::parse($dateSaisie)->isoFormat('dddd, D MMMM YYYY', 'Do MMMM YYYY');

            // Récupérez les opérations de la boutique et du magasin pour la date donnée
            $operations = OpertationBoutique::where('magasin_id', $magasin->id)
                ->where('boutique_id', $boutique->id)
                ->where('date', $dateSaisie)
                ->get();

            // Retournez la vue avec les données nécessaires
            return view('admin.ProduitRetour.list', compact('boutique', 'magasin', 'operations', 'dateFormatee'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect()->back();
        }
    }

    public function create($nom,$nomMagasin)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->first();
            $produits = Produit::where('piece_totale', '!=', '0')->where('boutique_id', $boutique->id)->get();
            return view('admin.ProduitRetour.create', compact('boutique', 'produits', 'nomMagasin'));
        } catch (\Throwable $th) {
            session()->flash('error', $th);
            return redirect('admin/dashboard');
        }
    }

    public function save(Request $request, $nom, $nomMagasin)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $boutique = Boutique::where('nom', $nom)->firstOrFail();
            $magasin = Magasin::where('nom', $nomMagasin)->firstOrFail();

            // Validez les données du formulaire
            $validatedData = $request->validate([
                'date' => 'required',
                'nom_piece' => 'required|integer',
                'produit_id' => 'required|integer',
            ]);

            // Récupérez le produit en fonction de l'ID
            $product = Produit::find($validatedData['produit_id']);

            if (!$product) {
                return redirect('admin/boutique/' . $boutique->nom.'/retour')->with('error', 'Produit introuvable');
            }

            // Vérifiez si la quantité demandée est supérieure au stock total
            if ($validatedData['nom_piece'] > $product->piece_totale) {
                return redirect('admin/boutique/' . $boutique->nom . '/retour')->with('error', 'La quantité demandée est supérieure au stock total');
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
                $existingProduct = Produit::where('code', $product->code)->where('magasin_id', $magasin->id)->first();

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
                    $newProduct->magasin_id = $magasin->id;
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

            return redirect('admin/boutique/' . $boutique->nom . '/retour/' . $nomMagasin)->with('message', 'Produit affecté avec succès');
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($nom, $nomMagasin, $operation_id)
    {
        try {
            if (!OpertationBoutique::where('id', $operation_id)->exists()) {
                session()->flash('error', 'Operation non Trouvée');
                return redirect('admin/boutique/' . $nom . '/retour/' . $nomMagasin);
            }
            $boutique = Boutique::where('nom', $nom)->first();
            $produits = Produit::where('piece_totale', '!=', '0')->where('boutique_id', $boutique->id)->get();
            $operation = OpertationBoutique::find($operation_id);
            return view('admin.ProduitRetour.edit', compact('boutique', 'nomMagasin', 'operation', 'produits', 'operation_id'));
        } catch (\Throwable $th) {
            session()->flash('error', $th);
            return redirect('admin/dashboard');
        }
    }

    public function update(Request $request, $nom, $nomMagasin, $operation_id)
    {
        try {
            // Récupérez le magasin en fonction du nom
            $boutique = Boutique::where('nom', $nom)->firstOrFail();
            $magasin = Magasin::where('nom', $nomMagasin)->firstOrFail();

            // Validez les données du formulaire
            $validatedData = $request->validate([
                'date' => 'required',
                'nom_piece' => 'required|integer',
                'produit_id' => 'required|integer',
            ]);

            // Récupérez le produit en fonction de l'ID
            $product = Produit::find($validatedData['produit_id']);

            if (!$product) {
                return redirect('admin/boutique/' . $boutique->nom . '/retour')->with('error', 'Produit introuvable');
            }

            // Vérifiez si la quantité demandée est supérieure au stock total
            if ($validatedData['nom_piece'] > $product->piece_totale) {
                return redirect('admin/boutique/' . $boutique->nom . '/retour')->with('error', 'La quantité demandée est supérieure au stock total');
            }

            // Créez une nouvelle opération boutique
            $operation = OpertationBoutique::find($operation_id);
            $operation->magasin_id = $magasin->id;
            $operation->boutique_id = $boutique->id;
            $operation->produit_id = $validatedData['produit_id'];
            $operation->nombre_piece = $validatedData['nom_piece'];
            $operation->date = $validatedData['date'];
            $operation->update();

            return redirect('admin/boutique/' . $boutique->nom . '/retour/' . $nomMagasin)->with('message', 'Produit affecté avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
