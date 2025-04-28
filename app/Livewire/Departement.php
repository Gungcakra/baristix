<?php

namespace App\Livewire;

use App\Models\Departement as ModelsDepartement;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Departement extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $departementId, $name, $salary, $idToDelete, $search = '';
    protected $listeners = ['deleteDepartementConfirmed'];
    public function mount()
    {
        $userPermissions = Auth::user()->roles->flatMap(function ($role) {
            return $role->permissions->pluck('name');
        });

        if (!$userPermissions->contains('masterdata-departement')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function openModal()
    {
        $this->dispatch('show-modal');
    }
    public function closeModal()
    {
        $this->departementId = null;
        $this->reset(['name', 'salary']);
        $this->dispatch('hide-modal');
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
                'salary' => 'required|numeric',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('error', collect($e->errors())->flatten()->first());
            return;
        }

        ModelsDepartement::updateOrCreate(
            ['id' => $this->departementId],
            [
                'name' => $this->name,
                'salary' => $this->salary,
            ]
        );

        $this->dispatch('success', 'Departement created successfully.');
        $this->closeModal();
       
    }
    public function delete($id)
    {
        $this->idToDelete = $id;
        $this->dispatch('confirm-delete', 'Are you sure you want to delete this departement?');
    }
    public function deleteDepartementConfirmed()
    {
        ModelsDepartement::destroy($this->idToDelete);
        $this->dispatch('delete-success', 'Departement deleted successfully.');
    }
    public function edit($id)
    {
        $departement = ModelsDepartement::find($id);
        $this->departementId = $departement->id;
        $this->name = $departement->name;
        $this->salary = $departement->salary;
        $this->openModal();
    }
    public function update()
    {
        try{
            $this->validate([
                'name' => 'required',
                'salary' => 'required|numeric',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('error', collect($e->errors())->flatten()->first());
            return;
        }

        ModelsDepartement::updateOrCreate(
            ['id' => $this->departementId],
            [
                'name' => $this->name,
                'salary' => $this->salary,
            ]
        );

        $this->dispatch('success', 'Departement updated successfully.');
        $this->closeModal();
    }
    public function render()
    {
        return view('livewire.pages.admin.masterdata.departement.index', [
            'data' => ModelsDepartement::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })->paginate(10),
        ]);
    }
}
