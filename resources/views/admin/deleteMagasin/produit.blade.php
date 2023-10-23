@extends('layouts.admin')
@section('content')
    <h1>Listes des produits de {{ $magasin->nom }}</h1>
    @include('layouts.partials.message')
    @include('layouts.partials.error')
    {{-- <div class="mb-3">
        @if (Auth::user()->role_as == '1')
            <a href="{{ url('admin/magasin/' . $magasin->nom . '/gerant/' . $gerant->prenom . '/produit') }}"
                class="btn btn-dark">
                Ajouter un Produit
            </a>
        @endif
        <a href="{{ url('admin/operation/' . $magasin->nom . '/gerant/' . $gerant->prenom) }}" class="btn btn-dark">
            Operations Magasin
        </a>
        <a href="{{ url('admin/operationBoutique/' . $magasin->nom . '/boutique') }}" class="btn btn-dark">
            Sortie en Boutique
        </a>
        <a href="{{ url('admin/sortieBoutique/' . $magasin->nom . '/boutique') }}" class="btn btn-dark">
            Entre en Boutique
        </a>
        <a href="{{ url('admin/commandeMagasin/' . $magasin->nom) }}" class="btn btn-dark">
            Commande Client
        </a>
    </div> --}}
    <form action="{{ url('admin/supprimer/'.$magasin->nom.'/magasin') }}" method="POST">
        @csrf
        <div class="white-box">
            <h3 class="box-title text-center">
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </h3>
            <div class="table-responsive">
                <table class="table text-nowrap">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll" /></th>
                            <th class="border-top-0">Code</th>
                            <th class="border-top-0">Nom Produit</th>
                            <th class="border-top-0">Nombre Piece</th>
                            <th class="border-top-0">Prix Unitaire</th>
                            <th class="border-top-0">Total</th>
                            <th class="border-top-0">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalPrice = 0;
                        @endphp
                        @forelse ($produits as $produit)
                            <tr id="sid{{ $produit->id }}">
                                <td><input type="checkbox" name="produits[{{ $produit->id }}]" class="checkItem"
                                        value="{{ $produit->id }}"></td>
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
                            </tr>
                            @php
                                $totalPrice += $produit->nombre_carton * $produit->prix_unitaire;
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
                    {{-- {{ $produits->links() }} --}}
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
    </form>
@endsection
