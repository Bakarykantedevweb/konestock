<div>
    @include('livewire.admin.magasin.modal')
    <div class="row">
        @include('layouts.partials.message')
        @include('layouts.partials.error')
        <div class="col-sm-12">
            @if (Auth::user()->role_as == '1')
                <div class="mb-3">
                    <a type="button" class="btn btn-dark " data-bs-toggle="modal" data-bs-target="#addmagasin">
                        Ajouter un Magasin
                    </a>
                </div>
            @endif
            <div class="white-box">

                <h3 class="box-title">Listes des Magasins</h3>
                <!-- Button trigger modal -->
                <div class="table-responsive">
                    <table class="table text-nowrap">
                        <thead>
                            <tr>
                                <th class="border-top-0">#</th>
                                <th class="border-top-0">Nom</th>
                                <th class="border-top-0">Gerant</th>
                                @if (Auth::user()->role_as == '1')
                                    <th class="border-top-0">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @forelse ($magasins as $items)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $items->nom }}</td>
                                    <td>{{ $items->gerant->prenom . ' ' . $items->gerant->nom }}</td>
                                    @if (Auth::user()->role_as == '1')
                                        <td>
                                            <a href="#" wire:click="editmagasin({{ $items->id }})"
                                                data-bs-toggle="modal" data-bs-target="#editmagasin"
                                                class="btn btn-dark btn-sm">Modifier</a>
                                            <a href="#" wire:click="deletemagasin({{ $items->id }})"
                                                class="btn btn-danger btn-sm">Supprimer</a>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Pas de magasins</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
