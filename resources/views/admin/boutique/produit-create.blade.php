@extends('layouts.admin')
@section('content')
    <h1>{{ $boutique->nom }}</h1>
    @include('layouts.partials.error')
    <div class="mb-3">
        <a href="{{ url('admin/boutique/' . $boutique->nom) }}" class="btn btn-dark">
            Retour
        </a>
    </div>
    <div class="row">
        <div class="col-lg-12 col-xlg-9 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ url('admin/boutique/' . $boutique->nom . '/create') }}"
                        class="form-horizontal form-material">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="col-md-12 p-0">Nom Produit</label>
                                    <div class="col-md-12 border-bottom p-0">
                                        <input type="text" name="nom_produit" placeholder="Nom Produit"
                                            class="form-control p-0 border-0">
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="col-md-12 p-0">Nombre Piece</label>
                                    <div class="col-md-12 border-bottom p-0">
                                        <input type="number" name="nom_piece" placeholder="Nombre Piece"
                                            class="form-control p-0 border-0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="col-md-12 p-0">Nombre Carton</label>
                                    <div class="col-md-12 border-bottom p-0">
                                        <input type="number" name="nom_carton" placeholder="Nombre Carton"
                                            class="form-control p-0 border-0">
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="col-md-12 p-0">Prix Unitaire</label>
                                    <div class="col-md-12 border-bottom p-0">
                                        <input type="number" name="prix_unitaire" placeholder="Prix Unitaire"
                                            class="form-control p-0 border-0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-4">
                                <label class="col-md-12 p-0">Fournisseurs</label>
                                <div class="col-md-12 border-bottom p-0">
                                    <select name="fournisseur_id" class="form-control">
                                        <option value="">...</option>
                                        @foreach ($fournisseurs as $fournisseur)
                                            <option value="{{ $fournisseur->id }}">{{ $fournisseur->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-dark">Enregistrer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Column -->
    </div>
@endsection
