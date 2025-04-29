<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.admin')]
class Bank extends Component
{
    use WithPagination;
    public $bankId, $name, $balance, $search = '';
    protected $paginationTheme = 'bootstrap';   

    protected $listeners = ['deleteBank'];
    public function mount()
    {
        $userPermissions = Auth::user()->roles->flatMap(function ($role) {
            return $role->permissions->pluck('name');
        });

        if (!$userPermissions->contains('masterdata-bank')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function openModal()
    {
        $this->dispatch('show-modal');
    }
    public function closeModal()
    {
        $this->reset(['name', 'balance', 'bankId']);
        $this->dispatch('hide-modal');
        $this->bankId = null;
    }
    public function create()
    {
        $this->openModal();
    }
    public function store()
    {
        try{
            $this->validate([
                'name' => 'required',
                'balance' => 'required|numeric',
            ]);
            \App\Models\Bank::updateOrCreate(
                ['id' => $this->bankId],
                [
                    'name' => $this->name,
                    'balance' => $this->balance,
                ]
            );
            $this->dispatch('success', 'Bank saved successfully');
            $this->closeModal();
        }catch(\Exception $e){
            $this->dispatch('error', 'Failed to save data: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        $bank = \App\Models\Bank::find($id);
        if ($bank) {
            $this->bankId = $bank->id;
            $this->name = $bank->name;
            $this->balance = $bank->balance;
            $this->openModal();
        } else {
            $this->dispatch('error', 'Bank not found');
        }
    }
    public function update()
    {
        try{
            $this->validate([
                'name' => 'required',
                'balance' => 'required|numeric',
            ]);
            \App\Models\Bank::updateOrCreate(
                ['id' => $this->bankId],
                [
                    'name' => $this->name,
                    'balance' => $this->balance,
                ]
            );
            $this->dispatch('success', 'Bank updated successfully');
            $this->closeModal();
            
        }catch(\Exception $e){
            $this->dispatch('error', 'Failed to update data: ' . $e->getMessage());
        }
    }
    public function delete($id)
    {
        $this->bankId = $id;
        $this->dispatch('confirm-delete', 'Are you sure you want to delete this bank?');
    }
    public function deleteBank()
    {
        try{
            \App\Models\Bank::find($this->bankId)->delete();
            $this->dispatch('delete-success', 'Bank deleted successfully');
        }catch(\Exception $e){
            $this->dispatch('error', 'Failed to delete data: ' . $e->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.pages.admin.masterdata.bank.index',[
            'data' => \App\Models\Bank::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })->paginate(10),
        ]);
    }
}
