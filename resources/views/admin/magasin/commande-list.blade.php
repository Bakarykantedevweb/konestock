@extends('layouts.admin')
@section('content')
    <h1>Magasin : {{ $magasin->nom }}</h1>
    @include('layouts.partials.error')
    <div class="mb-3">
        <a href="{{ url('admin/magasin/' . $magasin->nom) }}" class="btn btn-dark">
            Retour
        </a>
        <a href="{{ url('admin/magasin/' . $magasin->nom . '/commande') }}" class="btn btn-dark">
            Ajouter une commande
        </a>
    </div>
    @include('layouts.partials.message')
    @include('layouts.partials.error')
    <div class="white-box">
        <h3 class="box-title">Listes des commandes</h3>
        <!-- Button trigger modal -->
        <div class="table-responsive">
            <table class="table text-nowrap">
                <thead>
                    <tr>
                        <th class="border-top-0">Numero</th>
                        <th class="border-top-0">Nom</th>
                        <th class="border-top-0">Prenom</th>
                        <th class="border-top-0">Date</th>
                        <th class="border-top-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($commandes as $commande)
                        <tr>
                            <td>{{ $commande->numero }}</td>
                            <td>{{ $commande->nom }}</td>
                            <td>{{ $commande->prenom }}</td>
                            <td>{{ $commande->date }}</td>
                            <td>
                                <a href="{{ url('admin/magasin/' . $magasin->nom . '/commande-list/' . $commande->numero . '/facture') }}"
                                    target="_blank" class="btn btn-dark">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Pas de Commande pour le magasin {{ $magasin->nom }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- <div class="row">
            <div class="col-md-8"></div>
            <div class="col-md-4 mt-3">
                <div class="shadow-sm bg-white p-3">
                    <h4>Total:
                        <span class="float-end">{{ number_format($totalPrice); }} F</span>
                    </h4>
                    <hr>
                </div>
            </div>
        </div> --}}
    </div>
@endsection
