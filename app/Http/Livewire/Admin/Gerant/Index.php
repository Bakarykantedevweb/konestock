<?php

namespace App\Http\Livewire\Admin\Gerant;

use App\Models\Gerant;
use Livewire\Component;

class Index extends Component
{
    public $gerants, $prenom, $nom, $telephone, $gerant_id;
    protected function rules()
    {
        return [
            'nom' => 'required|string|',
            'prenom' => 'required|string|',
            'telephone' => 'required|string|',
        ];
    }

    public function updated($fields)
    {
        $this->validateOnly($fields);
    }

    public function saveGerant()
    {
        $validatedData = $this->validate();
        try {
            $gerant = new Gerant();
            $gerant->prenom = $validatedData['prenom'];
            $gerant->nom = $validatedData['nom'];
            $gerant->telephone = $validatedData['telephone'];
            $gerant->save();
            session()->flash('message', 'Gerant Added Successfully');
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        } catch (\Throwable $e) {
            session()->flash('error', $e);
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        }
    }

    public function editgerant(int $gerant_id)
    {
        $gerant = Gerant::find($gerant_id);
        if ($gerant) {
            $this->gerant_id = $gerant_id;
            $this->nom = $gerant->nom;
            $this->prenom = $gerant->prenom;
            $this->telephone = $gerant->telephone;
        }
    }

    public function updateGerant()
    {
        $validatedData = $this->validate();
        try {
            $gerant = Gerant::find($this->gerant_id);
            $gerant->prenom = $validatedData['prenom'];
            $gerant->nom = $validatedData['nom'];
            $gerant->telephone = $validatedData['telephone'];
            $gerant->save();
            session()->flash('message', 'Gerant Updated Successfully');
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        } catch (\Throwable $e) {
            session()->flash('error', $e);
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        }
    }

    public function deletegerant(int $gerant_id)
    {
        try {
            Gerant::where('id', $gerant_id)->delete();
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
        $this->prenom = NULL;
        $this->telephone = NULL;
    }

    public function render()
    {
        $this->gerants = Gerant::get();
        return view('livewire.admin.gerant.index');
    }
}
