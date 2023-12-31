@extends('layouts.admin')
@section('content')
    <h1>Listes des produits de {{ $magasin->nom }}</h1>
    <form action="" method="GET">
        <div class="form-group">
            <select class="magasin form-control" style="width: 100%;" name="nom_produit" multiple="multiple"
                id="select2Multiple">
                @foreach ($rechercheProduit as $items)
                    <option value="{{ $items->nom_produit }}">{{ $items->nom_produit.' Nombres Piece '.$items->nombre_carton.' Prix '.$items->prix_unitaire }}</option>
                @endforeach
            </select>
        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-dark">Recherche</button>
        </div>
    </form>
    <hr>
    @include('layouts.partials.message')
    @include('layouts.partials.error')
    <div class="mb-3">
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
    </div>
    <div class="white-box">
        <h3 class="box-title">
            <a href="{{ url('admin/export/'.$magasin->nom) }}" class="btn btn-success">Exporter vers Excel</a>
        </h3>
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
                                    <a href="{{ url('admin/magasin/' . $magasin->nom . '/gerant/' . $gerant->prenom . '/produit/' . $produit->code . '/edit') }}"
                                        class="btn btn-dark">Modifier
                                    </a>
                                    {{-- <a href="{{ url('admin/magasin/' . $magasin->nom . '/gerant/' . $gerant->prenom . '/produit/' . $produit->code . '/delete') }}"
                                        class="btn btn-danger"
                                        onclick="return confirm('Etes-vous sur de vouloir supprimer le produit')">
                                        Supprimer
                                    </a> --}}
                                </td>
                            @endif
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
@endsection
