@extends('layouts.admin')
@section('content')
    <h1>Magasin : {{ $magasin->nom }}</h1>
    @include('layouts.partials.error')
    <div class="mb-3">
        <a href="{{ url('admin/commandeMagasin/' . $magasin->nom) }}" class="btn btn-dark">
            Listes des Commandes
        </a>
    </div>
    <div class="row">
        <div class="col-lg-12 col-xlg-9 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ url('admin/commandeMagasin/' . $magasin->nom . '/create') }}"
                        class="form-horizontal form-material">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="p-0">Nom</label>
                                    <div class="border-bottom p-0">
                                        <input type="text" name="nom" class="form-control p-0 border-0">
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="p-0">Date</label>
                                    <div class="border-bottom p-0">
                                        <input type="date" name="date" class="form-control p-0 border-0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="p-0">Prenom</label>
                                    <div class="border-bottom p-0">
                                        <input type="text" name="prenom" class="form-control p-0 border-0">
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="p-0">Telephone</label>
                                    <div class="border-bottom p-0">
                                        <input type="text" name="telephone" class="form-control p-0 border-0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive m-t-15">
                                    <table id="commande" style="width:100%" class="table table-striped custom-table">
                                        <thead>
                                            <tr>
                                                <th>produits</th>
                                                <th class="text-center">Select</th>
                                                <th class="text-center">Quantite</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($produits as $produit)
                                                <tr>
                                                    <td>{{ $produit->nom_produit . ' Total ' . $produit->nombre_carton }}
                                                    </td>
                                                    <td class="text-center">
                                                        <input name="produits[]" value="{{ $produit->id }}"
                                                            type="checkbox">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="number" name="quantite[]" min="1"
                                                            max="{{ $produit->nombre_carton }}"class="form-control">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-dark">Enregistrer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Column -->
    </div>
@endsection
