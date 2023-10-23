<?php

namespace App\Http\Controllers\Admin;

use App\Models\Magasin;
use App\Models\Produit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExportController extends Controller
{
    public function export($magasinNom)
    {
        $magasin = Magasin::where('nom', $magasinNom)->first();
        // Récupérez les données de votre table "produit" depuis la base de données (par exemple, en utilisant Eloquent).
        $productData = Produit::where('magasin_id', $magasin->id)
            ->orderBy('nom_produit', 'ASC')
            ->where('delete_as', '0')
            ->get();

        // Créez un nouveau classeur (spreadsheet)
        $spreadsheet = new Spreadsheet();

        // Obtenez la feuille active
        $sheet = $spreadsheet->getActiveSheet();

        // Ajoutez les titres de colonnes
        $col = 'A';
        $sheet->setCellValue($col++ . '1', 'Code');
        $sheet->setCellValue($col++ . '1', 'Nom du Produit');
        $sheet->setCellValue($col++ . '1', 'Nombre de Pieces');
        $sheet->setCellValue($col++ . '1', 'Prix Unitaire');
        $sheet->setCellValue($col++ . '1', 'Montant Total');
        // ... Ajoutez d'autres titres de colonnes ici

        // Ajoutez les données du produit à la feuille
        $row = 2; // Commencez à la ligne suivante
        foreach ($productData as $product) {
            $col = 'A';
            $sheet->setCellValue($col++ . $row, $product->code);
            $sheet->setCellValue($col++ . $row, $product->nom_produit);
            $sheet->setCellValue($col++ . $row, $product->nombre_carton);
            $sheet->setCellValue($col++ . $row, $product->prix_unitaire);
            $sheet->setCellValue($col++ . $row, $product->nombre_carton * $product->prix_unitaire);
            // ... Ajoutez les données des autres colonnes ici
            $row++;
        }

        // Définissez les en-têtes de réponse pour le téléchargement
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="produits.xlsx"');
        header('Cache-Control: max-age=0');

        // Sauvegardez le classeur dans la sortie
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');

        return redirect()->route('dashboard');
    }


}
