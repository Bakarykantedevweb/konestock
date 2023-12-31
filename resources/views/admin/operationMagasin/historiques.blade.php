@extends('layouts.admin')
@section('content')
    <h1>Magasin : {{ $magasin->nom }}</h1>
    @include('layouts.partials.error')
    <div class="mb-3">
        <a href="{{ url("admin/operation/$magasin->nom/gerant/$prenom/index/$magasinA") }}" class="btn btn-dark">
            Retour
        </a>
    </div>
    @include('layouts.partials.message')
    @include('layouts.partials.error')
    <div class="white-box">
        <h3 class="box-title text-center">
            Listes des Opérations
        </h3>
        <!-- Button trigger modal -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table text-nowrap">
                        <thead>
                            <tr>
                                <th class="border-top-0">Date</th>
                                <th class="border-top-0">Magasin départ</th>
                                <th class="border-top-0">Magasin Arrivé</th>
                                <th class="border-top-0">Nom Produit</th>
                                <th class="border-top-0">Nombre de Pièces</th>
                                <th class="border-top-0">Prix Unitaire</th>
                                <th class="border-top-0">Total</th>
                                @if (Auth::user()->role_as == '1')
                                    <th class="border-top-0">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalPrice = 0;
                            @endphp
                            @foreach ($operations as $operation)
                                <tr>
                                    <td>{{ $operation->date }}</td>
                                    <td>{{ $magasin->nom }}</td>
                                    <td>{{ $magasinA }}</td>
                                    <td>
                                        @if ($operation->produit)
                                            {{ $operation->produit->nom_produit }}
                                        @else
                                            Pas de Produit
                                        @endif
                                    </td>

                                    <td>{{ $operation->nombre_piece }}</td>

                                    @if ($operation->produit)
                                        <td>{{ $operation->produit->prix_unitaire }}</td>
                                        <td>{{ $operation->nombre_piece * $operation->produit->prix_unitaire }}</td>
                                    @else
                                        <td>Pas de Prix</td>
                                        <td>0</td>
                                    @endif

                                    @if (Auth::user()->role_as == '1')
                                        <td>
                                            {{-- <a href="{{ url("admin/operation/$magasin->nom/gerant/$prenom/edit/$magasinA/$operation->id") }}"
                                                class="btn btn-dark">Modofier</a> --}}
                                            <a href="{{ url("admin/operation/$magasin->nom/gerant/$prenom/delete/$magasinA/$operation->id") }}"
                                                class="btn btn-danger">Supprimer</a>
                                        </td>
                                    @endif
                                </tr>
                                @php
                                    if ($operation->produit) {
                                        $totalPrice += $operation->nombre_piece * $operation->produit->prix_unitaire;
                                    }
                                @endphp
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
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
