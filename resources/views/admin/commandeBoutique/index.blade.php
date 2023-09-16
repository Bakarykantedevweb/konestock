@extends('layouts.admin')
@section('content')
    <h1>Magasin : {{ $boutique->nom }}</h1>
    <div class="mb-3">
        <a href="{{ url('admin/boutique/' . $boutique->nom) }}" class="btn btn-dark">
            Retour
        </a>
        @if (Auth::user()->role_as == '1')
            <a href="{{ url('admin/commande/' . $boutique->nom . '/create') }}" class="btn btn-dark">
                Ajouter une commande
            </a>
        @endif
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
                        <th class="border-top-0">Telephone</th>
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
                            <td>{{ $commande->telephone }}</td>
                            <td>{{ $commande->date }}</td>
                            <td>
                                @if (Auth::user()->role_as == '1')
                                    <a href=""
                                        class="btn btn-dark">
                                        Modifier
                                    </a>
                                @endif
                                <a href="{{ url('admin/commande/' . $boutique->nom . '/facture/' . $commande->numero) }}"
                                class="btn btn-info">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Pas de Commande pour la boutique {{ $boutique->nom }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
