<?php

namespace App\Http\Livewire\Admin\Fournisseur;

use Livewire\Component;
use App\Models\Fournisseur;

class Index extends Component
{
    public $fournisseurs;

    public $nom, $Fournisseur_id;
    protected function rules()
    {
        return [
            'nom' => 'required|string|',
        ];
    }

    public function updated($fields)
    {
        $this->validateOnly($fields);
    }

    public function saveFournisseur()
    {
        $validatedData = $this->validate();
        try {
            $gerant = new Fournisseur();
            $gerant->nom = $validatedData['nom'];
            $gerant->save();
            session()->flash('message', 'Fournisseur Added Successfully');
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        } catch (\Throwable $e) {
            session()->flash('error', $e);
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        }
    }

    public function editFournisseur(int $Fournisseur_id)
    {
        $gerant = Fournisseur::find($Fournisseur_id);
        if ($gerant) {
            $this->Fournisseur_id = $Fournisseur_id;
            $this->nom = $gerant->nom;
        }
    }

    public function updateFournisseur()
    {
        $validatedData = $this->validate();
        try {
            $gerant = Fournisseur::find($this->Fournisseur_id);
            $gerant->nom = $validatedData['nom'];
            $gerant->save();
            session()->flash('message', 'Fournisseur Updated Successfully');
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        } catch (\Throwable $e) {
            session()->flash('error', $e);
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        }
    }

    public function deleteFournisseur(int $Fournisseur_id)
    {
        try {
            Fournisseur::where('id', $Fournisseur_id)->delete();
            session()->flash('message', 'Fournisseur Deleted Successfully');
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
    }

    public function render()
    {
        $this->fournisseurs = Fournisseur::get();
        return view('livewire.admin.Fournisseur.index');
    }
}
