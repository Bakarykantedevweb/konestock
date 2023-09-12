<!-- Modal -->
<div wire:ignore.self class="modal fade" id="addmagasin" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                <button type="button" class="btn-close" wire:click="closeModal" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="saveMagasin">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="">Nom</label>
                        <input type="text" class="form-control" wire:model="nom">
                        @error('nom')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Gerant</label>
                        <select wire:model="gerant_id" class="form-control">
                            <option value="">--</option>
                            @foreach ($gerants as $gerant)
                                <option value="{{ $gerant->id }}">{{ $gerant->prenom.' '.$gerant->nom }}</option></option>
                            @endforeach
                        </select>
                        @error('gerant_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="closeModal" class="btn btn-danger"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-dark">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div wire:ignore.self class="modal fade" id="editmagasin" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                <button type="button" class="btn-close" wire:click="closeModal" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="updateMagasin">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="">Nom</label>
                        <input type="text" class="form-control" wire:model="nom">
                        @error('nom')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Gerant</label>
                        <select wire:model="gerant_id" class="form-control">
                            <option value="">--</option>
                            @foreach ($gerants as $gerant)
                                <option value="{{ $gerant->id }}">{{ $gerant->prenom.' '.$gerant->nom }}</option></option>
                            @endforeach
                        </select>
                        @error('gerant_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="closeModal" class="btn btn-danger"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-dark">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
