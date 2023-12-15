@extends('layouts.admin')
@section('content')
    <h1>{{ $boutique->nom }}</h1>
    <form action="" method="GET">
        <div class="row">
            <div class="form-group mb-3 col-md-12">
                <select class="boutique form-control" style="width: 100%;" name="code" multiple="multiple">
                    @foreach ($rechercheProduit as $items)
                        <option value="{{ $items->nom_produit }}">{{ $items->nom_produit.' Nombres Piece '.$items->nombre_carton.' Prix '.$items->prix_unitaire }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-dark">Recherche</button>
            </div>
        </div>
    </form>
    <hr>
    @include('layouts.partials.message')
    @include('layouts.partials.error')
    <div class="mb-3">
        @if (Auth::user()->role_as == '1')
            <a href="{{ url('admin/boutique/' . $boutique->nom . '/create') }}" class="btn btn-dark">
                Ajouter un produit
            </a>
        @endif
        <a href="{{ url('admin/boutique/' . $boutique->nom . '/retour') }}" class="btn btn-dark">
            Entre Magasin
        </a>
        <a href="{{ url('admin/boutique/' . $boutique->nom . '/sortie') }}" class="btn btn-dark">
            Sortie Boutique
        </a>
        <a href="{{ url('admin/boutique/' . $boutique->nom . '/operation') }}" class="btn btn-dark">
            Operations Boutique
        </a>
        <a href="{{ url('admin/commande/' . $boutique->nom) }}" class="btn btn-dark">
            Commande Client
        </a>
    </div>
    <div class="white-box">
        <a href="{{ url('admin/export/'.$boutique->nom.'/boutique') }}" class="btn btn-success">Exporter vers Excel</a>
        <!-- Button trigger modal -->
        <div class="table-responsive">
            <table class="table text-nowrap">
                <thead>
                    <tr>
                        <th class="border-top-0">Code</th>
                        <th class="border-top-0">Nom Produit</th>
                        <th class="border-top-0">Nombre Piece</th>
                        <th class="border-top-0">Prix Unitaire</th>
                        <th class="border-top-0">Total</th>
                        <th class="border-top-0">Status</th>
                        <th class="border-top-0">Date Creation</th>
                        <th class="border-top-0">Date Modification</th>
                        @if (Auth::user()->role_as == '1')
                            <th class="border-top-0" colspan="2">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPrice = 0;
                    @endphp
                    @forelse ($produits as $produit)
                        <tr>
                            <td>{{ $produit->code }}</td>
                            <td>{{ $produit->nom_produit }}</td>
                            <td>{{ $produit->nombre_carton }}</td>
                            <td>{{ $produit->prix_unitaire }}</td>
                            <td>{{ number_format($produit->nombre_carton * $produit->prix_unitaire) }}</td>
                            <td>
                                @if ($produit->nombre_carton != 0)
                                    <span class="text-success"><i class="fas fa-check"></i></span>
                                @else
                                    <span class="text-danger"><i class="fas fa-window-close"></i></span>
                                @endif
                            </td>
                            <td>{{ $produit->created_at }}</td>
                            <td>{{ $produit->updated_at }}</td>
                            @if (Auth::user()->role_as == '1')
                                <td>
                                    <a href="{{ url('admin/boutique/' . $boutique->nom . '/edit/' . $produit->code) }}"
                                        class="btn btn-dark">Modifier</a>
                                </td>
                            @endif
                        </tr>
                        @php $totalPrice += $produit->nombre_carton * $produit->prix_unitaire @endphp
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Pas de Produit</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-md-8"></div>
            <div class="col-md-4 mt-3">
                <div class="shadow-sm bg-white p-3">
                    <h4>Total:
                        <span class="float-end">{{ number_format($totalPrice) }} F</span>
                    </h4>
                    <hr>
                </div>
            </div>
        </div>
    </div>
@endsection
