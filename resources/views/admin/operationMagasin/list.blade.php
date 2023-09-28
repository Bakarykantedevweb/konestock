@extends('layouts.admin')
@section('content')
    <h1>Magasin : {{ $magasin->nom }}</h1>
    @include('layouts.partials.message')
    @include('layouts.partials.error')
    <div class="mb-3">
        <a href="{{ url('admin/magasin/' . $magasin->nom.'/gerant/'.$gerant->prenom) }}" class="btn btn-dark">
            Retour
        </a>
    </div>
    <div class="row justify-content">
        @forelse ($magasins as $magasin)
            <div class="col-lg-4 col-md-12">
                <div class="white-box analytics-info">
                    <a
                        href="{{ url('admin/operation/' . $nom . '/gerant/' . $gerant->prenom . '/index/'.$magasin->nom) }}">
                        <h3 class="box-title">{{ $magasin->nom }}</h3>
                    </a>
                    <ul class="list-inline two-part d-flex align-items-center mb-0">
                        <li>
                            <div>
                                {{ $magasin->gerant->prenom . ' ' . $magasin->gerant->nom }}
                            </div>
                        </li>
                        <li class="ms-auto"><span class="counter text-success">{{ $magasin->count_produit }}</span></li>
                    </ul>
                </div>
            </div>
        @empty
            <h3>Pas de Magasins</h3>
        @endforelse
    </div>
@endsection
