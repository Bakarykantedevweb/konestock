@extends('layouts.admin')
@section('content')
    <h1>{{ $magasin->nom }} et {{ $magasinA }}</h1>
    @include('layouts.partials.error')
    <div class="mb-3">
        <a href="{{ url('admin/operation/' . $magasin->nom . '/gerant/' . $gerant->prenom . '/index/'.$magasinA) }}" class="btn btn-dark">
            Retour
        </a>
    </div>
    <div class="row">
        <div class="col-lg-12 col-xlg-9 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ url('admin/operation/' . $magasin->nom . '/gerant/' . $gerant->prenom . '/create/'.$magasinA) }}"
                        class="form-horizontal form-material">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="col-md-12 p-0">Date</label>
                            <div class="col-md-12 border-bottom p-0">
                                <input type="date" value="<?php echo date("Y-m-d"); ?>" name="date" class="form-control p-0 border-0">
                            </div>
                        </div>
                        {{-- <div class="form-group mb-4">
                            <label class="col-md-12 p-0">Produits</label>
                            <div class="col-md-12 border-bottom p-0">
                                <select name="produit_id" style="width: 100%;" multiple="multiple" class="operation_magasin form-control">
                                    <option value="">---</option>
                                    @foreach ($produits as $produit)
                                        <option value="{{ $produit->id }}">{{ $produit->nom_produit. ' Total ' . $produit->nombre_carton}}
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
                        </div> --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive m-t-15">
                                    <table id="commande" style="width:100%" class="table table-striped custom-table">
                                        <thead>
                                            <tr>
                                                <th>produits</th>
                                                <th class="text-center">Select</th>
                                                <th class="text-center">Quantite</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($produits as $produit)
                                                <tr>
                                                    <td>{{ $produit->nom_produit . ' Total ' . $produit->nombre_carton }}
                                                    </td>
                                                    <td class="text-center">
                                                        <input name="produit_id[]" value="{{ $produit->id }}"
                                                            type="checkbox">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="number" required name="nom_piece[]" min="1"
                                                            max="{{ $produit->nombre_carton }}"class="form-control">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
