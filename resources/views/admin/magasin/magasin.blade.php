@extends('layouts.admin')
@section('content')
    <h1>Magasin : {{ $magasin->nom }}</h1>
    <form action="" method="GET">
        <div class="row">
            <div class="col-md-3">
                <label>Code</label>
                <input type="text" name="code" Fournisseur class="form-control">
            </div>
            {{-- <div class="col-md-3">
                <label>Nom Produit</label>
                <input type="text" name="nom_produit"  class="form-control">
            </div> --}}
            <div class="col-md-6 mt-2">
                <br>
                <button type="submit" class="btn btn-dark">Recherche</button>
            </div>
        </div>
    </form>
    <hr>
    @include('layouts.partials.message')
    @include('layouts.partials.error')
    <div class="mb-3">
        @if (Auth::user()->role_as == '1')
            <a href="{{ url('admin/magasin/' . $magasin->nom . '/produit') }}" class="btn btn-dark">
                Ajouter un Produit
            </a>
        @endif
        <a href="{{ url('admin/operation/' . $magasin->nom) }}" class="btn btn-dark">
            Operations Magasin
        </a>
        <a href="{{ url('admin/operation/' . $magasin->nom . '/boutique') }}" class="btn btn-dark">
            Operations en Boutique
        </a>
        <a href="{{ url('admin/commandeMagasin/' . $magasin->nom) }}" class="btn btn-dark">
            Commande Client
        </a>
    </div>
    <div class="white-box">
        <h3 class="box-title">Listes Produits</h3>
        <!-- Button trigger modal -->
        <div class="table-responsive">
            <table class="table text-nowrap">
                <thead>
                    <tr>
                        <th class="border-top-0">Code</th>
                        <th class="border-top-0">Nom Produit</th>
                        <th class="border-top-0">Nombre Carton</th>
                        <th class="border-top-0">Nombre Piece</th>
                        <th class="border-top-0">Piece restante</th>
                        <th class="border-top-0">Piece Total</th>
                        <th class="border-top-0">Prix Unitaire</th>
                        <th class="border-top-0">Total</th>
                        <th class="border-top-0">Status</th>
                        @if (Auth::user()->role_as == '1')
                            <th class="border-top-0">Actions</th>
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
                            <td>{{ $produit->nombre_piece }}</td>
                            <td>{{ $produit->piece_totale % $produit->nombre_piece }}</td>
                            <td>{{ $produit->piece_totale }}</td>
                            <td>{{ $produit->prix_unitaire }}</td>
                            <td>{{ $produit->piece_totale * $produit->prix_unitaire }}</td>
                            <td>
                                @if ($produit->piece_totale != 0)
                                    <span class="text-success"><i class="fas fa-check"></i></span>
                                @else
                                    <span class="text-danger"><i class="fas fa-window-close"></i></span>
                                @endif
                            </td>
                            @if (Auth::user()->role_as == '1')
                                <td>
                                    <a href="{{ url('admin/magasin/' . $magasin->nom . '/produit/' . $produit->code . '/edit') }}"
                                        class="btn btn-dark">Modifier</a>
                                </td>
                            @endif
                        </tr>
                        @php
                            $totalPrice += $produit->piece_totale * $produit->prix_unitaire;
                        @endphp
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Pas de Produit</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-md-8">
                {{ $produits->links() }}
            </div>
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
