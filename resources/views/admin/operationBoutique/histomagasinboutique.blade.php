@extends('layouts.admin')
@section('content')
    <div class="row justify-content-center">
        <h1>Entrée : {{ $magasin->nom }} et {{ $boutique->nom }}</h1>
        <div class="mb-3">
            <a href="{{ url('admin/operationBoutique/' . $magasin->nom . '/boutique') }}" class="btn btn-dark">
                Retour
            </a>
            @if (Auth::user()->role_as == '1')
                <a href="{{ url('admin/operationBoutique/' . $magasin->nom . '/boutique/' . $boutique->nom . '/create') }}"
                    class="btn btn-dark">
                    Ajout une operation
                </a>
            @endif
            <a href="{{ url('admin/operationBoutique/' . $magasin->nom . '/boutique/' . $boutique->nom . '/historique') }}"
                class="btn btn-dark">
                Historiques
            </a>
        </div>
        <!-- Button trigger modal -->
    </div>
    @include('layouts.partials.message')
    @include('layouts.partials.error')
    <div class="white-box">
        <h3 class="box-title text-center">
            Listes des Operations a la date du,{{ $dateFormatee }}
        </h3>
        <div class="row">
            <div class="col-md-10">
                <div class="table-responsive">
                    <table class="table text-nowrap">
                        <thead>
                            <tr>
                                <th class="border-top-0">Nom Produit</th>
                                <th class="border-top-0">Nombre Piece</th>
                                <th class="border-top-0">prix Unitaire</th>
                                <th class="border-top-0">Total</th>
                                @if (Auth::user()->role_as == '1')
                                    {{-- <th class="border-top-0">Actions</th> --}}
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalPrice = 0;
                            @endphp
                            @forelse ($operations as $operation)
                                <tr>
                                    <td>{{ $operation->produit->nom_produit }}</td>
                                    <td>{{ $operation->nombre_piece }}</td>
                                    <td>{{ $operation->produit->prix_unitaire }}</td>
                                    <td>{{ $operation->nombre_piece * $operation->produit->prix_unitaire }}</td>
                                    @if (Auth::user()->role_as == '1')
                                        {{-- <td>
                                            <a href="{{ url('admin/operationBoutique/' . $magasin->nom . '/boutique/' . $boutique->nom .'/edit/'.$operation->id) }}" class="btn btn-dark">Modifier</a>
                                        </td> --}}
                                    @endif
                                </tr>
                                @php $totalPrice += $operation->nombre_piece * $operation->produit->prix_unitaire @endphp
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Pas d'Operations pour la boutiques
                                        {{ $boutique->nom }}
                                    </td>
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
            <div class="col-md-2">
                <form action="" method="GET">
                    <div class="form-group">
                        <label for="">Date</label>
                        <input type="date" name="date_saisie" class="form-control">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-dark">Recherche</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
