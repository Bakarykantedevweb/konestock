@extends('layouts.admin')
@section('content')
    <div class="row justify-content-center">
        <h1>Historiques de la boutique : {{ $boutique->nom }}</h1>
        <div class="mb-3">
            <a href="{{ url('admin/boutique/' . $boutique->nom) }}" class="btn btn-dark">
                Retour
            </a>
        </div>
        @forelse ($magasins as $magasin)
            <div class="col-lg-4 col-md-12">
                <div class="white-box analytics-info">
                    <a href="{{ url('admin/boutique/' . $boutique->nom . '/historiques/' . $magasin->nom) }}">
                        <h3 class="box-title">{{ $magasin->nom }}</h3>
                    </a>
                    <ul class="list-inline two-part d-flex align-items-center mb-0">
                        <li>
                            <div>
                                {{ $magasin->gerant->prenom . ' ' . $magasin->gerant->nom }}
                            </div>
                        </li>
                        {{-- <li class="ms-auto"><span class="counter text-success">{{ $magasin->nombre_de_produits }}</span></li> --}}
                    </ul>
                </div>
            </div>
        @empty
            <h3>Pas de Magasins</h3>
        @endforelse
    </div>
@endsection
