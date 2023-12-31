@extends('layouts.admin')
@section('content')
    <h1 class="text-center">Corbeilles</h1>
    <div class="row justify-content">
        @include('layouts.partials.error')
        @include('layouts.partials.message')
        <h3>Magasins</h3>
        @forelse ($magasins as $magasin)
            <div class="col-lg-4 col-md-12">
                <div class="white-box analytics-info">
                    <a href="{{ url('admin/magasin/corbeille/'.$magasin->nom) }}" ><h3 class="box-title">{{ $magasin->nom }}</h3></a>
                    <ul class="list-inline two-part d-flex align-items-center mb-0">
                        <li>
                            <div>
                                {{ $magasin->gerant->prenom.' '.$magasin->gerant->nom }}
                            </div>
                        </li>
                        <li class="ms-auto"><span class="counter text-success">{{$magasin->count_produit}}</span></li>
                    </ul>
                </div>
            </div>
        @empty
            <h3>Pas de Magasins</h3>
        @endforelse
    </div>
    <div class="row justify-content">
        <h3>Boutiques</h3>
        @forelse ($boutiques as $boutique)

            <div class="col-lg-4 col-md-12">
                <div class="white-box analytics-info">
                    <a href="{{ url('admin/boutique/corbeille/'.$boutique->nom) }}" ><h3 class="box-title">{{ $boutique->nom }}</h3></a>
                    <ul class="list-inline two-part d-flex align-items-center mb-0">
                        <li>
                            <div>
                                {{ $boutique->gerant->prenom.' '.$boutique->gerant->nom }}
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        @empty
            <h3>Pas de Boutiques</h3>
        @endforelse
    </div>
@endsection
