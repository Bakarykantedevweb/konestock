<?php

namespace App\Http\Livewire\Admin\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Index extends Component
{
    public $users,$name,$email,$role_as,$user_id;
    protected function rules()
    {
        return [
            'name' => 'required|string|',
            'email' => 'required|email|',
            'role_as' => 'required|integer|',
        ];
    }

    public function updated($fields)
    {
        $this->validateOnly($fields);
    }

    public function saveUser()
    {
        $validatedData = $this->validate();
        try {
            $user = new User();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->role_as = $validatedData['role_as'];
            $user->password = Hash::make('password');
            $user->save();
            session()->flash('message', 'User Added Successfully');
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        } catch (\Throwable $e) {
            session()->flash('error', $e);
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        }
    }

    public function edituser(int $user_id)
    {
        $User = User::find($user_id);
        if ($User) {
            $this->user_id = $user_id;
            $this->name = $User->name;
            $this->email = $User->email;
            $this->role_as = $User->role_as;
        }
    }

    public function updateUser()
    {
        $validatedData = $this->validate();
        try {
            $user = User::find($this->user_id);
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->role_as = $validatedData['role_as'];
            $user->save();
            session()->flash('message', 'User Updated Successfully');
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        } catch (\Throwable $e) {
            session()->flash('error', $e);
            $this->dispatchBrowserEvent('close-modal');
            $this->resetInput();
        }
    }

    public function deleteuser(int $user_id)
    {
        try {
            User::where('id', $user_id)->delete();
            session()->flash('message', 'User Deleted Successfully');
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
        $this->name = NULL;
        $this->email = NULL;
        $this->role_as = NULL;
    }
    public function render()
    {
        $this->users = User::get();
        return view('livewire.admin.user.index');
    }
}
