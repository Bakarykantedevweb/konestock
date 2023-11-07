@extends('layouts.admin')
@section('content')
    @include('admin.historique.modal')
    <h1>Listes des produits de {{ $magasin->nom }}</h1>
    @include('layouts.partials.message')
    @include('layouts.partials.error')
    <div class="mb-3">
        <a href="{{ url('admin/historiques') }}" class="btn btn-dark">
            Retour
        </a>
    </div>
    <form method="post" action="{{ url('admin/historiques/magasin/'.$magasin->nom.'/supprimer')}}">
        @csrf
        <div class="white-box">
            <h3 class="box-title text-center">
                <a type="button" data-bs-toggle="modal" data-bs-target="#addproduit" class="btn btn-success">Importer les
                    produits</a>
                <button onclick="return confirm('Etes-vous sur de vouloir Supprimer ???')" type="submit"
                    class="btn btn-danger">Supprimer</button>
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
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalPrice = 0;
                        @endphp
                        @forelse ($Histoproduits as $produit)
                            <tr id="sid{{ $produit->id }}">
                                <td><input type="checkbox" name="produits[{{ $produit->id }}]" class="checkItem"
                                        value="{{ $produit->id }}"></td>
                                <td>{{ $produit->code }}</td>
                                <td>{{ $produit->nom_produit }}</td>
                                <td>{{ $produit->nombre_carton }}</td>
                                <td>{{ $produit->prix_unitaire }}</td>
                                <td>{{ number_format($produit->nombre_carton * $produit->prix_unitaire) }}</td>
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
