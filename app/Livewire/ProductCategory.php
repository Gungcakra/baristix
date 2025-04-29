<?php

namespace App\Livewire;

use App\Models\ProductCategory as ModelsProductCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.admin')]
class ProductCategory extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $productCategoryId, $name, $search = '';
    protected $listeners = ['deleteProductCategory'];
    public function mount()
    {
        $userPermissions = Auth::user()->roles->flatMap(function ($role) {
            return $role->permissions->pluck('name');
        });
    
        if (!$userPermissions->contains('masterdata-product-category')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function openModal()
    {
        $this->dispatch('show-modal');
    }

    public function closeModal()
    {
        $this->reset(['name', 'productCategoryId']);
        $this->dispatch('hide-modal');
        $this->productCategoryId = null;
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
            ]);
            ModelsProductCategory::updateOrCreate(
                ['id' => $this->productCategoryId],
                [
                    'name' => $this->name,
                ]
            );
            $this->dispatch('success', 'Product Category saved successfully');
        }catch(\Exception $e){
            $this->dispatch('error', 'Failed to save data: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $this->productCategoryId = $id;
        $this->dispatch('confirm-delete', 'Are you sure you want to delete this product category?');
    }
    public function deleteProductCategory()
    {
        try{
            ModelsProductCategory::find($this->productCategoryId)->delete();
            $this->dispatch('delete-success', 'Product Category deleted successfully');
        }catch(\Exception $e){
            $this->dispatch('error', 'Failed to delete data: ' . $e->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.pages.admin.masterdata.productcategory.index',[
            'data' => ModelsProductCategory::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })->paginate(10),
        ]);
    }
}
