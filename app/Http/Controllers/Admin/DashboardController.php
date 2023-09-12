<?php

namespace App\Http\Controllers\Admin;

use App\Models\Magasin;
use App\Models\Boutique;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $magasins = Magasin::select('magasins.*')
        ->selectRaw('COUNT(produits.id) as nombre_de_produits')
        ->leftJoin('produits', 'magasins.id', '=', 'produits.magasin_id')
        ->groupBy('magasins.id')
        ->get();
        $boutiques = Boutique::select('boutiques.*')
            ->selectRaw('COUNT(produits.id) as nombre_de_produits')
            ->leftJoin('produits', 'boutiques.id', '=', 'produits.boutique_id')
            ->groupBy('boutiques.id')
            ->get();

        return view('admin.dashboard',compact('magasins','boutiques'));
    }
}
