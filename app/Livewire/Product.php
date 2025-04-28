<?php

namespace App\Livewire;

use App\Models\Product as ModelsProduct;
use App\Models\ProductCategory;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
#[Layout('layouts.admin')]
class Product extends Component
{
    use WithFileUploads;
    public $product, $productCategory, $name, $price, $file_path, $productId, $search = '';
    protected $listeners = ['deleteProductConfirm', 'deleteProduct'];
    public function mount()
    {
        $this->product = ModelsProduct::with('productCategory')->get();
    }
    public function openModal()
    {
        $this->dispatch('show-modal');
    }
    public function closeModal()
    {
        $this->reset(['name', 'price', 'file_path','productCategory', 'productId']);
        $this->dispatch('hide-modal');
        $this->productId = null;
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
                'productCategory' => 'required|exists:product_categories,id',
                'price' => 'required|numeric',
                'file_path' => 'required|image|max:2048',
            ]);
         
            if ($this->file_path) {
                $this->file_path = $this->file_path->store('products', 'public');
            }
            ModelsProduct::create([
                'name' => $this->name,
                'product_category_id' => $this->productCategory,
                'price' => $this->price,
                'file_path' => $this->file_path,
            ]);

            
            $this->dispatch('success', 'Product saved successfully.');
        }  catch(\Exception $e){
            $this->dispatch('error', 'Failed to save product: ' . $e->getMessage());
            return;
        }
    }
    
    public function update()
    {
        try{
            $this->validate([
                'name' => 'required',
                'productCategory' => 'required|exists:product_categories,id',
                'price' => 'required|numeric',
                'file_path' => 'image|max:2048',
            ]);
         
            $product = ModelsProduct::find($this->productId);
            if ($this->file_path) {
                if ($product && $product->file_path && Storage::disk('public')->exists($product->file_path)) {
                    Storage::disk('public')->delete($product->file_path);
                }
                $this->file_path = $this->file_path->store('products', 'public');
            } else {
                $this->file_path = $product->file_path;
            }
            $product->update([
                'name' => $this->name,
                'product_category_id' => $this->productCategory,
                'price' => $this->price,
                'file_path' => $this->file_path,
            ]);

            
            $this->dispatch('success', 'Product updated successfully.');
        }  catch(\Exception $e){
            $this->dispatch('error', 'Failed to update product: ' . $e->getMessage());
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
