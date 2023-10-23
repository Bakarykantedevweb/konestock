<?php

namespace App\Http\Controllers\Admin;

use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CommandeMagasinController extends Controller
{
    public function index($nom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $commandes = Commande::where('magasin_id', $magasin->id)->get();
            return view('admin.commandeMagasin.index', compact('magasin', 'commandes'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function create($nom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $produits = Produit::where('nombre_carton', '!=', '0')
                    ->where('magasin_id', $magasin->id)
                    ->orderBy('nom_produit','Asc')
                    ->where('delete_as', '0')
                    ->get();
            return view('admin.commandeMagasin.create', compact('magasin', 'produits'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function save(Request $request, $nomMagasin)
    {
        try {
            $magasin = Magasin::where('nom', $nomMagasin)->first();
            // Validez les données du formulaire
            $validatedData = $request->validate([
                'date' => 'required',
                'telephone' => 'required|min:8',
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
            $commande->telephone = $validatedData['telephone'];
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

            return redirect('admin/commandeMagasin/' . $magasin->nom)->with('message', 'Produits ajoutés avec succès à la commande.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage()); //
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
                    return view('admin.commandeMagasin.detail', compact('magasin', 'commande', 'commandeProduits'));
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
}
