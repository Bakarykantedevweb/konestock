<div>
    @include('livewire.admin.fournisseur.modal')
    <div class="row">
        @include('layouts.partials.message')
        @include('layouts.partials.error')
        <div class="col-sm-12">
            <div class="mb-3">
                @if (Auth::user()->role_as == '1')
                    <a type="button" class="btn btn-dark " data-bs-toggle="modal" data-bs-target="#addFournisseur">
                        Ajouter un Fournisseur
                    </a>
                @endif

            </div>
            <div class="white-box">

                <h3 class="box-title">Listes des Fournisseurs</h3>
                <!-- Button trigger modal -->
                <div class="table-responsive">
                    <table class="table text-nowrap">
                        <thead>
                            <tr>
                                <th class="border-top-0">#</th>
                                <th class="border-top-0">Nom</th>
                                @if (Auth::user()->role_as == '1')
                                    <th class="border-top-0">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @forelse ($fournisseurs as $items)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $items->nom }}</td>
                                    @if (Auth::user()->role_as == '1')
                                        <td>
                                            <a href="#" wire:click="editFournisseur({{ $items->id }})"
                                                data-bs-toggle="modal" data-bs-target="#editFournisseur"
                                                class="btn btn-dark btn-sm">Modifier</a>
                                            <a href="#" wire:click="deleteFournisseur({{ $items->id }})"
                                                class="btn btn-danger btn-sm">Supprimer</a>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Pas de Fournisseurs</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
