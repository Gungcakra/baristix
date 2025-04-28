<?php

namespace App\Livewire;

use App\Events\DataUpdate;
use App\Models\Departement;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class EmployeeManagement extends Component
{
    use WithPagination;
     protected $paginationTheme = 'bootstrap';
    public $employeeId, $name, $selectedDepartement, $phone, $address, $idToDelete;
    public $isModalOpen = false;
    protected $listeners = ['deleteEmployee'];

    public $search = '';
    #[On('echo:data-refresh,.table-employee')]

    public function mount()
    {
        $userPermissions = Auth::user()->roles->flatMap(function ($role) {
            return $role->permissions->pluck('name');
        });
    
        if (!$userPermissions->contains('masterdata-employee')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function render()
    {

        return view('livewire.pages.admin.masterdata.employee.index', [
            'data' => Employee::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })->paginate(10),
            'departements' => Departement::all(),

        ]);
    }

    public function create()
    {
        $this->openModal();
    }
    public function openModal()
    {

        $this->isModalOpen = true;
        $this->dispatch('show-modal');

    }
    public function closeModal()
    {
        $this->reset(['employeeId', 'name', 'selectedDepartement', 'phone', 'address']);
        $this->isModalOpen = false;
        $this->employeeId = null;
        $this->dispatch(event: 'hide-modal');
     

    }
    public function store()
    {
        try{
            $this->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'address' => 'required|string|max:255',
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('error', collect($e->errors())->flatten()->first());
            return;
        }


        Employee::create([
            'departement_id' => $this->selectedDepartement,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);
        $this->dispatch('success', 'Employee added successfully.');
        DataUpdate::dispatch('table-employee');
        $this->closeModal();
   
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $this->employeeId = $employee->id;
        $this->fill($employee->only(['name', 'phone', 'address']));
        $this->selectedDepartement = $employee->departement_id;
        $this->openModal();
    }



    public function update()
    {

        try{

            $this->validate([
                'name' => 'required|string|max:255',
                'selectedDepartement' => 'required',
                'phone' => 'required|string|max:15',
                'address' => 'required|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('error', collect($e->errors())->flatten()->first());
            return;
        }
        $employee = Employee::findOrFail($this->employeeId);
        $employee->update([
            'name' => $this->name,
            'departement_id' => $this->selectedDepartement,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);
        $this->dispatch('success', 'Employee updated successfully.', );
        DataUpdate::dispatch('table-employee');
        $this->closeModal();
    }
    public function submitForm()
    {
        if ($this->employeeId) {
            $this->update();
        } else {
            $this->store();
        }
    }
    public function delete($id)
    {
       $this->dispatch('confirm-delete', "Are you sure you want to delete this employee?");
       $this->idToDelete = $id;
       
    }

    public function deleteEmployee()
    {
      if($this->idToDelete){
        Employee::findOrFail($this->idToDelete)->delete();
        $this->dispatch('delete-success', 'Employee deleted successfully.');
      }
    }

    public function handleSelectedDepartement($value)
    {
        $this->selectedDepartement = $value;
    }

}
