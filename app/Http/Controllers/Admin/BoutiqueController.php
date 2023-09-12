<?php

namespace App\Http\Controllers\Admin;

use App\Models\Magasin;
use App\Models\Produit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Boutique;
use App\Models\OpertationBoutique;

class BoutiqueController extends Controller
{
    public function index()
    {
        return view('admin.boutique.index');
    }

    public function boutique(Request $req, $nom)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->first();
            $produits = Produit::where('boutique_id', $boutique->id)
                ->when($req->code != null, function ($q) use ($req) {
                    return $q->where('code', $req->code);
                })
                ->paginate(10);
            return view('admin.boutique.boutique', compact('boutique', 'produits'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function historique($nom)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->first();
            $magasins = Magasin::get();
            return view('admin.boutique.historique', compact('magasins','boutique'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function historiqueMag($nom,$magasin)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->first();
            $magasin = Magasin::where('nom', $magasin)->first();
            $date = date('Y-m-d');
            $operations = OpertationBoutique::where('magasin_id',$magasin->id)
                                            ->where('boutique_id',$boutique->id)
                                            ->where('date',$date)
                                            ->get();
            return view('admin.boutique.histo-magasin',compact('boutique','operations','magasin'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }

    public function historiqueMagTout($nom, $magasin)
    {
        try {
            $boutique = Boutique::where('nom', $nom)->first();
            $magasin = Magasin::where('nom', $magasin)->first();
            $operations = OpertationBoutique::where('magasin_id', $magasin->id)
                ->where('boutique_id', $boutique->id)
                ->get();
            return view('admin.boutique.histo-magasin-tout', compact('boutique', 'operations', 'magasin'));
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
            return redirect('admin/dashboard');
        }
    }
}
