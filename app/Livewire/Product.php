<?php

namespace App\Livewire;

use App\Models\Product as ModelsProduct;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.admin')]
class Product extends Component
{
    use WithFileUploads;
    public $product, $productCategory, $name, $price, $file_path, $productId, $search = '', $photo, $photoPath;
    protected $listeners = ['deleteProduct'];
    public function mount()
    {
        $userPermissions = Auth::user()->roles->flatMap(function ($role) {
            return $role->permissions->pluck('name');
        });

        if (!$userPermissions->contains('masterdata-product')) {
            abort(403, 'Unauthorized action.');
        }
        $this->product = ModelsProduct::with('productCategory')->get();
    }
    public function openModal()
    {
        $this->dispatch('show-modal');
    }
    public function closeModal()
    {
        $this->reset(['name', 'price', 'file_path', 'productCategory', 'productId']);
        $this->dispatch('hide-modal');
        $this->productId = null;
    }
    public function create()
    {
        $this->openModal();
    }

    public function updatedPhoto()
    {
        $this->photoPath = $this->photo->store('products', 'public');
        $this->dispatch('file-uploaded', 'Image uploaded successfully');
    }

    public function removePhoto()
    {

        if ($this->photoPath && Storage::disk('public')->exists($this->photoPath)) {
            Storage::disk('public')->delete($this->photoPath);
            $this->photoPath = null;
            $this->dispatch('file-removed', 'Image removed successfully');
        } else {
            $this->dispatch('error', 'Image not found.');
        }
    }
    public function store()
    {
        try {
            $this->validate([
                'name' => 'required',
                'productCategory' => 'required|exists:product_categories,id',
                'price' => 'required|numeric',
                'photo' => 'required|image|max:2048',
            ]);


            ModelsProduct::create([
                'name' => $this->name,
                'product_category_id' => $this->productCategory,
                'price' => $this->price,
                'file_path' => $this->photoPath,
            ]);


            $this->dispatch('success', 'Product saved successfully.');
            $this->reset(['photo', 'photoPath']);
            $this->closeModal();
        } catch (\Exception $e) {
            $this->dispatch('error', 'Failed to save product: ' . $e->getMessage());
            return;
        }
    }


    public function edit($id)
    {
        $this->productId = $id;
        $product = ModelsProduct::find($id);
        if ($product) {
            $this->name = $product->name;
            $this->price = $product->price;
            $this->file_path = $product->file_path;
            $this->productCategory = $product->product_category_id;
            $this->openModal();
        } else {
            $this->dispatch('error', 'Product not found.');
        }
    }

    public function update()
    {
        try {
            $this->validate([
                'name' => 'required',
                'productCategory' => 'required|exists:product_categories,id',
                'price' => 'required|numeric',
            ]);

            $product = ModelsProduct::find($this->productId);
            if ($this->photoPath) {
            $delete = Storage::disk('public')->delete($product->file_path);
            if ($delete) {
                $this->dispatch('file-removed', 'Old image deleted successfully');
                    $this->file_path = $this->photoPath;
                } 
            }
            $product->update([
                'name' => $this->name,
                'product_category_id' => $this->productCategory,
                'price' => $this->price,
                'file_path' => $this->file_path,
            ]);
            $this->reset(['photo', 'photoPath']);
            $this->dispatch('success', 'Product updated successfully.');
            $this->closeModal();
        } catch (\Exception $e) {
            $this->dispatch('error', 'Failed to update product: ' . $e->getMessage());
            return;
        }
    }
    public function delete($id)
    {
        $this->productId = $id;
        $this->dispatch('confirm-delete', 'Are you sure you want to delete this product?');
    }
    public function deleteProduct()
    {
        try {
            $product = ModelsProduct::find($this->productId);
            if ($product) {
                if ($product->file_path && Storage::disk('public')->exists($product->file_path)) {
                    Storage::disk('public')->delete($product->file_path);
                }
                $product->delete();
                $this->reset(['photo', 'photoPath']);
                $this->productId = null;
                $this->dispatch('delete-success', 'Product deleted successfully.');
            } else {
                $this->dispatch('error', 'Product not found.');
            }
        } catch (\Exception $e) {
            $this->dispatch('error', 'Failed to delete product: ' . $e->getMessage());
            return;
        }
    }

    public function render()
    {
        return view('livewire.pages.admin.masterdata.product.index', [
            'data' => ModelsProduct::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })->paginate(10),
            'productCategories' => ProductCategory::all(),
        ]);
    }
}
