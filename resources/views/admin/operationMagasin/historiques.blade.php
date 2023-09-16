@extends('layouts.admin')
@section('content')
    <h1>Magasin : {{ $magasin->nom }}</h1>
    @include('layouts.partials.error')
    <div class="mb-3">
        <a href="{{ url('admin/operation/' . $magasin->nom) }}" class="btn btn-dark">
            Retour
        </a>
    </div>
    @include('layouts.partials.message')
    @include('layouts.partials.error')
    <div class="white-box">
        <h3 class="box-title">
            Listes des Operations
        </h3>
        <!-- Button trigger modal -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table text-nowrap">
                        <thead>
                            <tr>
                                <th class="border-top-0">Date</th>
                                <th class="border-top-0">Magasin depart</th>
                                <th class="border-top-0">Magasin Arrive</th>
                                <th class="border-top-0">Nom Produit</th>
                                <th class="border-top-0">Nombre Piece</th>
                                <th class="border-top-0">prix Unitaire</th>
                                <th class="border-top-0">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalPrice = 0;
                            @endphp
                            @forelse ($operations as $operation)
                                <tr>
                                    <td>{{ $operation->date }}</td>
                                    <td>{{ $magasin->nom }}</td>
                                    <td>{{ $operation->nomMagasinArrive }}</td>
                                    <td>{{ $operation->produit->nom_produit }}</td>
                                    <td>{{ $operation->nombre_piece }}</td>
                                    <td>{{ $operation->produit->prix_unitaire }}</td>
                                    <td>{{ $operation->nombre_piece * $operation->produit->prix_unitaire }}</td>
                                </tr>
                                @php $totalPrice += $operation->nombre_piece * $operation->produit->prix_unitaire @endphp
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Pas d'Operations pour le Magasin
                                        {{ $magasin->nom }}
                                    </td>
                                </tr>
                            @endforelse
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
