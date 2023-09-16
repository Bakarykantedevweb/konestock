<?php

namespace App\Http\Controllers\Admin;

use App\Models\Magasin;
use App\Models\Boutique;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Produit;

class DashboardController extends Controller
{
    public function index()
    {
        $magasins = Magasin::get();
        $boutiques = Boutique::get();

        foreach ($boutiques as $key => $value) {
            $boutiques[$key]->count_produit = Produit::where('boutique_id', $value->id)->count();
        }

        foreach ($magasins as $key => $value) {
            $magasins[$key]->count_produit = Produit::where('magasin_id', $value->id)->count();
        }

        return view('admin.dashboard', compact('magasins', 'boutiques'));
    }
}
