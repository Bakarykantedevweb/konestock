<?php

namespace App\Http\Controllers\Admin;

use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Boutique;
use App\Models\Historique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;

class HistoriqueController extends Controller
{
    public function index()
    {
        $magasins = Magasin::get();
        $boutiques = Boutique::get();
        return view('admin.historique.index', compact('magasins', 'boutiques'));
    }

    public function magasin($nom)
    {
        try {
            $magasin = Magasin::where('nom', $nom)->first();
            $rechercheProduit = Historique::where('magasin_id', $magasin->id)
                ->where('delete_as','0')
                ->orderBy('nom_produit', 'ASC')->get();
            $Histoproduits = Historique::where('magasin_id', $magasin->id)
                ->where('delete_as', '0')
                ->get();
            return view('admin.historique.magasin',compact('magasin', 'Histoproduits', 'rechercheProduit'));
        } catch (\Throwable $th) {
            return redirect('admin/historiques')->with('error','Une erreur est survenue'.$th);
        }
    }

    public function Savemagasin(Request $request, $nom)
    {
        $magasin = Magasin::where('nom', $nom)->first();
        // Valider et stocker le fichier Excel
        $request->validate([
            'excel_file' => 'required|mimes:xlsx',
        ]);

        $file = $request->file('excel_file');
        // dd($file);
        // Charger le fichier Excel
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            try {
                $code = $worksheet->getCell('A' . $row)->getValue();
                $nom_produit = $worksheet->getCell('B' . $row)->getValue();
                $nombre_carton = $worksheet->getCell('C' . $row)->getValue();
                $prix_unitaire = $worksheet->getCell('D' . $row)->getValue();

                Historique::create([
                    'code' => $code,
                    'nom_produit' => $nom_produit,
                    'nombre_carton' => $nombre_carton,
                    'prix_unitaire' => $prix_unitaire,
                    'piece_totale' => $nombre_carton,
                    'magasin_id' => $magasin->id,
                    'boutique_id' => NULL,
                    'fournisseur_id' => 1,
                ]);
            } catch (\Exception $e) {
                session()->flash('error', $e->getMessage());
            }
        }
        return redirect()->back()->with('message', 'Importation reussi avec success');
    }

    public function boutique($nom)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->first();
            $rechercheProduit = Historique::where('boutique_id', $boutique->id)
                ->where('delete_as', '0')
                ->orderBy('nom_produit', 'ASC')->get();
            $Histoproduits = Historique::where('boutique_id', $boutique->id)
                ->where('delete_as', '0')
                ->get();
            return view('admin.historique.boutique', compact('boutique', 'Histoproduits', 'rechercheProduit'));
        } catch (\Throwable $th) {
            return redirect('admin/historiques')->with('error', 'Une erreur est survenue' . $th);
        }
    }

    public function Saveboutique(Request $request, $nom)
    {
        $boutique = Boutique::where('nom', $nom)->first();
        // Valider et stocker le fichier Excel
        $request->validate([
            'excel_file' => 'required|mimes:xlsx',
        ]);

        $file = $request->file('excel_file');
        // dd($file);
        // Charger le fichier Excel
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            try {
                $code = $worksheet->getCell('A' . $row)->getValue();
                $nom_produit = $worksheet->getCell('B' . $row)->getValue();
                $nombre_carton = $worksheet->getCell('C' . $row)->getValue();
                $prix_unitaire = $worksheet->getCell('D' . $row)->getValue();

                Historique::create([
                    'code' => $code,
                    'nom_produit' => $nom_produit,
                    'nombre_carton' => $nombre_carton,
                    'prix_unitaire' => $prix_unitaire,
                    'piece_totale' => $nombre_carton,
                    'magasin_id' => NULL,
                    'boutique_id' => $boutique->id,
                    'fournisseur_id' => 1,
                ]);
            } catch (\Exception $e) {
                session()->flash('error', $e->getMessage());
            }
        }
        return redirect()->back()->with('message', 'Importation reussi avec success');
    }

    public function supprimerBoutique(Request $request, $nom)
    {
        $boutique = Boutique::where('nom', $nom)->first();
        if ($request->has('produits')) {
            $produitsIds = $request->input('produits');

            if (!empty($produitsIds)) {
                Historique::whereIn('id', $produitsIds)
                    ->where('boutique_id', $boutique->id)
                    ->update(['delete_as' => 1,]);

                return redirect()->back()->with('message', 'Opération effectuée avec succès');
            }
        }

        return redirect()->back()->with('error', 'Aucun produit sélectionné.');
    }

    public function supprimerMagasin(Request $request, $nom)
    {
        $magasin = Magasin::where('nom', $nom)->first();
        if ($request->has('produits')) {
            $produitsIds = $request->input('produits');

            if (!empty($produitsIds)) {
                Historique::whereIn('id', $produitsIds)
                    ->where('magasin_id', $magasin->id)
                    ->update(['delete_as' => 1,]);

                return redirect()->back()->with('message', 'Opération effectuée avec succès');
            }
        }

        return redirect()->back()->with('error', 'Aucun produit sélectionné.');
    }

}
