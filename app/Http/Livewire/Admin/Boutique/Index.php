<?php

namespace App\Http\Livewire\Admin\Boutique;

use App\Models\Gerant;
use Livewire\Component;
use App\Models\Boutique;

class Index extends Component
{
    public $boutiques, $gerants;

    public $nom, $gerant_id, $boutique_id;
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

    public function saveBoutique()
    {
        $validatedData = $this->validate();
        try {
            $gerant = new Boutique();
            $gerant->gerant_id = $validatedData['gerant_id'];
            $gerant->nom = $validatedData['nom'];
            $gerant->save();
            session()->flash('message', 'Boutique Added Successfully');
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        } catch (\Throwable $e) {
            session()->flash('error', $e);
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        }
    }

    public function editboutique(int $boutique_id)
    {
        $gerant = Boutique::find($boutique_id);
        if ($gerant) {
            $this->boutique_id = $boutique_id;
            $this->nom = $gerant->nom;
            $this->gerant_id = $gerant->gerant_id;
        }
    }

    public function updateBoutique()
    {
        $validatedData = $this->validate();
        try {
            $gerant = Boutique::find($this->boutique_id);
            $gerant->gerant_id = $validatedData['gerant_id'];
            $gerant->nom = $validatedData['nom'];
            $gerant->save();
            session()->flash('message', 'Boutique Updated Successfully');
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        } catch (\Throwable $e) {
            session()->flash('error', $e);
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        }
    }

    public function deleteboutique(int $boutique_id)
    {
        try {
            Boutique::where('id', $boutique_id)->delete();
            session()->flash('message', 'Boutique Deleted Successfully');
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
        $this->boutiques = Boutique::get();
        $this->gerants = Gerant::get();
        return view('livewire.admin.boutique.index');
    }
}
