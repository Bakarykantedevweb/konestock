@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="mb-3">
            <a href="{{ url('admin/commandeMagasin/' . $magasin->nom) }}" class="btn btn-dark">
                Retour
            </a>
        </div>
        <div class="white-box">
            <div class="row">
                <div class="col">
                    <h1>Facture</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <strong>N° : {{ $commande->numero }}</strong>
                    <address>
                        Madou Kone<br>
                        Mali<br>
                        Bamako, 123456<br>
                        Téléphone : (+223) 70-06-07-62<br>
                        Email : kantebakary742@gmail.com
                    </address>
                </div>
                <div class="col-md-6">
                    <strong>À :</strong>
                    <address>
                        {{ $commande->prenom . ' ' . $commande->nom }}<br>
                        Mali<br>
                        Bamako, 987456<br>
                        Téléphone du client : (+223) {{ $commande->telephone }}<br>
                        Date : {{ $commande->date }}
                    </address>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table text-nowrap">
                            <thead>
                                <tr>
                                    <th class="border-top-0">Date</th>
                                    <th class="border-top-0">Produit</th>
                                    <th class="border-top-0">Nombre Carton</th>
                                    <th class="border-top-0">Nombre Piece</th>
                                    <th class="border-top-0">Piece restante</th>
                                    <th class="border-top-0">Quantite demande</th>
                                    <th class="border-top-0">Prix unitaire</th>
                                    <th class="border-top-0">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalPrice = 0;
                                @endphp
                                @foreach ($commandeProduits as $produit)
                                    <tr>
                                        <td>{{ $commande->date }}</td>
                                        <td>{{ $produit->nom_produit }}</td>
                                        <td>{{ ($produit->pivot->quantite - ($produit->pivot->quantite % $produit->nombre_piece)) / $produit->nombre_piece }}
                                        </td>
                                        <td>{{ $produit->nombre_piece }}</td>
                                        <td>{{ $produit->pivot->quantite % $produit->nombre_piece }}</td>
                                        <td>{{ $produit->pivot->quantite }}</td>
                                        <td>{{ $produit->prix_unitaire }}</td>
                                        <td>{{ $produit->pivot->quantite * $produit->prix_unitaire }}</td>
                                    </tr>
                                    @php
                                        $totalPrice += $produit->pivot->quantite * $produit->prix_unitaire;
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
    </div>
@endsection
