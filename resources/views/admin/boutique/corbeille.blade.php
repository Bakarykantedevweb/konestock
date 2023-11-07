@extends('layouts.admin')
@section('content')
    <h1>Corbeilles {{ $boutique->nom }}</h1>
    @include('layouts.partials.message')
    @include('layouts.partials.error')
    <div class="mb-3">
        <a href="{{ url('admin/magasin/corbeille') }}" class="btn btn-dark">Retour</a>
    </div>
    <div class="white-box">
        <h3 class="box-title">Listes Produits</h3>
        <div class="table-responsive">
            <table class="table text-nowrap">
                <thead>
                    <tr>
                        <th class="border-top-0">Code</th>
                        <th class="border-top-0">Nom Produit</th>
                        <th class="border-top-0">Nombre Piece</th>
                        <th class="border-top-0">Prix Unitaire</th>
                        <th class="border-top-0">Total</th>
                        <th class="border-top-0">Status</th>
                        @if (Auth::user()->role_as == '1')
                            <th class="border-top-0" colspan="1">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPrice = 0;
                    @endphp
                    @forelse ($produits as $produit)
                        <tr>
                            <td>{{ $produit->code }}</td>
                            <td>{{ $produit->nom_produit }}</td>
                            <td>{{ $produit->nombre_carton }}</td>
                            <td>{{ $produit->prix_unitaire }}</td>
                            <td>{{ number_format($produit->nombre_carton * $produit->prix_unitaire) }}</td>
                            <td>
                                @if ($produit->nombre_carton != 0)
                                    <span class="text-success"><i class="fas fa-check"></i></span>
                                @else
                                    <span class="text-danger"><i class="fas fa-window-close"></i></span>
                                @endif
                            </td>
                            @if (Auth::user()->role_as == '1')
                                <td>
                                    <a href="{{ url('admin/boutique/corbeille/'.$produit->id.'/annuler/'.$boutique->nom) }}"
                                        class="btn btn-danger" onclick="return confirm('Etes-vous sur de vouloir supprimer le produit')">
                                        Annuler
                                    </a>
                                </td>
                            @endif
                        </tr>
                        @php
                            $totalPrice += $produit->nombre_carton * $produit->prix_unitaire;
                        @endphp
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Pas de Produit</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-md-8">
                {{-- {{ $produits->links() }} --}}
            </div>
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
    {{-- <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script> --}}
@endsection
