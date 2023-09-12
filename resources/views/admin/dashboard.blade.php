@extends('layouts.admin')
@section('content')
    <div class="row justify-content-center">
        @include('layouts.partials.error')
        <h1>Magasins</h1>
        @forelse ($magasins as $magasin)
            <div class="col-lg-4 col-md-12">
                <div class="white-box analytics-info">
                    <a href="{{ url('admin/magasin/'.$magasin->nom) }}" ><h3 class="box-title">{{ $magasin->nom }}</h3></a>
                    <ul class="list-inline two-part d-flex align-items-center mb-0">
                        <li>
                            <div>
                                {{ $magasin->gerant->prenom.' '.$magasin->gerant->nom }}
                            </div>
                        </li>
                        <li class="ms-auto"><span class="counter text-success">{{ $magasin->nombre_de_produits }}</span></li>
                    </ul>
                </div>
            </div>
        @empty
            <h3>Pas de Magasins</h3>
        @endforelse
    </div>
    <div class="row justify-content-center">
        <h1>Boutiques</h1>
        @forelse ($boutiques as $boutique)
            <div class="col-lg-4 col-md-12">
                <div class="white-box analytics-info">
                    <a href="{{ url('admin/boutique/'.$boutique->nom) }}" ><h3 class="box-title">{{ $boutique->nom }}</h3></a>
                    <ul class="list-inline two-part d-flex align-items-center mb-0">
                        <li>
                            <div>
                                {{ $boutique->gerant->prenom.' '.$boutique->gerant->nom }}
                            </div>
                        </li>
                        <li class="ms-auto"><span class="counter text-success">{{ $boutique->nombre_de_produits }}</span></li>
                    </ul>
                </div>
            </div>
        @empty
            <h3>Pas de Boutiques</h3>
        @endforelse
    </div>
@endsection
