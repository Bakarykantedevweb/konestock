@extends('layouts.admin')
@section('content')
    <div class="row justify-content-center">
        <h1>Entrée : {{ $magasin->nom }} et {{ $boutique->nom }}</h1>
        <div class="mb-3">
            <a href="{{ url('admin/boutique/' . $boutique->nom . '/historiques') }}" class="btn btn-dark">
                Retour
            </a>
            <a href="{{ url('admin/boutique/' . $boutique->nom . '/historiques/' . $magasin->nom.'/tout') }}" class="float-end btn btn-dark">
                Tout
            </a>
        </div>
        <h3 class="mt-3 text-center">
            @php
                use Carbon\Carbon;

                // Définir la locale en français pour les dates
                Carbon::setLocale('fr');

                // Obtenir la date actuelle et la formater en "jour mois année" en français
                $date = ucfirst(Carbon::now()->isoFormat('dddd, D MMMM YYYY', 'Do MMMM YYYY'));

                echo $date; // Affiche la date formatée avec la première lettre en majuscule

            @endphp
        </h3>
        <!-- Button trigger modal -->
    </div>
    <div class="table-responsive">
        <table class="table text-nowrap">
            <thead>
                <tr>
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
                        <td>{{ $operation->produit->nom_produit }}</td>
                        <td>{{ $operation->nombre_piece }}</td>
                        <td>{{ $produit->prix_unitaire }}</td>
                        <td>{{ $operation->nombre_piece * $produit->prix_unitaire }}</td>
                    </tr>
                    @php $totalPrice += $operation->nombre_piece * $produit->prix_unitaire @endphp
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Pas d'Operations pour la boutiques {{ $boutique->nom }}</td>
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
@endsection
