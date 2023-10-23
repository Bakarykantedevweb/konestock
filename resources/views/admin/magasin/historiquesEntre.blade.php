@extends('layouts.admin')
@section('content')
    <div class="row justify-content-center">
        {{-- <h1>Entrée : {{ $magasin->nom }} et {{ $boutique->nom }}</h1> --}}
        <div class="mb-3">
            <a href="{{ url('admin/sortieBoutique/' . $magasin->nom . '/boutique/' . $boutique->nom) }}"
                class="btn btn-dark">
                Retour
            </a>
        </div>
        <!-- Button trigger modal -->
    </div>
    <div class="white-box">
        <h3 class="box-title text-center">
            Historiques entre {{ $boutique->nom }} vers {{ $magasin->nom }}
        </h3>
        <div class="table-responsive">
            <table class="table text-nowrap">
                <thead>
                    <tr>
                        <th class="border-top-0">Date</th>
                        <th class="border-top-0">Nom Produit</th>
                        <th class="border-top-0">Nombre Pièce</th>
                        <th class="border-top-0">Prix Unitaire</th>
                        <th class="border-top-0">Total</th>
                        {{-- @if (Auth::user()->role_as == '1')
                            <th class="border-top-0">Actions</th>
                        @endif --}}
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPrice = 0;
                    @endphp
                    @forelse ($operations as $operation)
                        @php
                            $produit = $operation->produit;
                            $prix_unitaire = optional($produit)->prix_unitaire ?? 'Non défini';
                            $total = $prix_unitaire !== 'Non défini' ? $operation->nombre_piece * $prix_unitaire : 'Non défini';
                            $totalPrice += is_numeric($total) ? $total : 0;
                        @endphp
                        <tr>
                            <td>{{ $operation->date }}</td>
                            <td>{{ $produit ? $produit->nom_produit : 'Non défini' }}</td>
                            <td>{{ $operation->nombre_piece }}</td>
                            <td>{{ $prix_unitaire }}</td>
                            <td>{{ $total }}</td>
                            {{-- @if (Auth::user()->role_as == '1')
                                <td>
                                    <a href="{{ url('admin/operationBoutique/' . $magasin->nom . '/boutique/' . $boutique->nom . '/delete/' . $operation->id) }}"
                                        class="btn btn-danger">Supprimer</a>
                                </td>
                            @endif --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Pas d'opérations pour la boutique {{ $boutique->nom }}
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
@endsection
