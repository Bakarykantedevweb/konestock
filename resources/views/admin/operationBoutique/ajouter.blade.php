@extends('layouts.admin')
@section('content')
    <h1>{{ $magasin->nom }} et {{ $nomBoutique }}</h1>
    @include('layouts.partials.error')
    <div class="mb-3">
        <a href="{{ url('admin/operationBoutique/' . $magasin->nom.'/boutique/'.$nomBoutique) }}" class="btn btn-dark">
            Retour
        </a>
    </div>
    <div class="row">
        <div class="col-lg-12 col-xlg-9 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ url('admin/operationBoutique/' . $magasin->nom . '/boutique/'.$nomBoutique.'/create') }}"
                        class="form-horizontal form-material">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="col-md-12 p-0">Produits</label>
                            <div class="col-md-12 border-bottom p-0">
                                <select name="produit_id" style="width: 100%;" multiple="multiple" class="boutique form-control">
                                    <option value="">---</option>
                                    @foreach ($produits as $produit)
                                        <option value="{{ $produit->id }}">{{$produit->nom_produit. ' Total ' . $produit->piece_totale}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label class="col-md-12 p-0">Nombre de Piece</label>
                            <div class="col-md-12 border-bottom p-0">
                                <input type="number" name="nom_piece" class="form-control p-0 border-0">
                            </div>
                        </div>
                        {{-- <div class="form-group mb-4">
                            <label class="col-md-12 p-0">Prix Unitaire</label>
                            <div class="col-md-12 border-bottom p-0">
                                <input type="text" name="prix_unitaire" class="form-control p-0 border-0">
                            </div>
                        </div> --}}
                        <div class="form-group mb-4">
                            <label class="col-md-12 p-0">Date</label>
                            <div class="col-md-12 border-bottom p-0">
                                <input type="date" value="<?php echo date("Y-m-d"); ?>" name="date" class="form-control p-0 border-0">
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
