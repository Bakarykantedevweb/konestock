<?php

namespace App\Http\Livewire\Admin\Magasin;

use App\Models\Gerant;
use App\Models\Magasin;
use Livewire\Component;

class Index extends Component
{
    public $magasins, $gerants;

    public $nom, $gerant_id, $magasin_id;
    protected function rules()
    {
        return [
            'nom' => 'required|string|',
            'gerant_id' => 'required|integer|',
        ];
    }

    public function updated($fields)
    {
        $this->validateOnly($fields);
    }

    public function saveMagasin()
    {
        $validatedData = $this->validate();
        try {
            $gerant = new Magasin();
            $gerant->gerant_id = $validatedData['gerant_id'];
            $gerant->nom = $validatedData['nom'];
            $gerant->save();
            session()->flash('message', 'Magasin Added Successfully');
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        } catch (\Throwable $e) {
            session()->flash('error', $e);
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        }
    }

    public function editmagasin(int $magasin_id)
    {
        $gerant = Magasin::find($magasin_id);
        if ($gerant) {
            $this->magasin_id = $magasin_id;
            $this->nom = $gerant->nom;
            $this->gerant_id = $gerant->gerant_id;
        }
    }

    public function updateMagasin()
    {
        $validatedData = $this->validate();
        try {
            $gerant = Magasin::find($this->magasin_id);
            $gerant->gerant_id = $validatedData['gerant_id'];
            $gerant->nom = $validatedData['nom'];
            $gerant->save();
            session()->flash('message', 'Magasin Updated Successfully');
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        } catch (\Throwable $e) {
            session()->flash('error', $e);
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        }
    }

    public function deletemagasin(int $magasin_id)
    {
        try {
            Magasin::where('id', $magasin_id)->delete();
            session()->flash('message', 'Gerant Deleted Successfully');
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        } catch (\Throwable $e) {
            session()->flash('error', $e);
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        }
    }


    public function closeModal()
    {
        $this->resetInput();
    }

    public function resetInput()
    {
        $this->nom = NULL;
        $this->gerant_id = NULL;
    }
    public function render()
    {
        $this->magasins = Magasin::get();
        $this->gerants = Gerant::get();
        return view('livewire.admin.magasin.index');
    }
}
