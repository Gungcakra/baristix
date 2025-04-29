<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class OperationalPos extends Component
{
    public $selectedCategory = 1, $productCategoryId, $productAdd = [];

    public function mount()
    {
        $userPermissions = Auth::user()->roles->flatMap(function ($role) {
            return $role->permissions->pluck('name');
        });

        if (!$userPermissions->contains('operational-pos')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function setCategory($id)
    {
        $this->selectedCategory = $id;
    }

    public function addProduct($id)
    {
        $productData = Product::find($id);
        if ($productData) {
          
                foreach ($this->productAdd as &$prod) {
                    if ($prod['id'] === $productData->id) {
                        $prod['quantity'] = isset($prod['quantity']) ? $prod['quantity'] + 1 : 2;
                        $prod['price'] = $productData->price * $prod['quantity'];
                        
                        return;
                    }
                }
                $this->productAdd[] = [
                    'id' => $productData->id,
                    'name' => $productData->name,
                    'price' => $productData->price,
                    'file_path' => $productData->file_path,
                    'quantity' => 1,
                ];
                
            } 
           
        }

    public function addQty($id)
    {
        foreach ($this->productAdd  as &$prod) {
            if ($prod['id'] === $id) {
                $prod['quantity'] += 1;
                $prod['price'] = $prod['quantity'] * Product::find($id)->price;
            }
        }
    }
    public function minQty($id)
    {
        foreach ($this->productAdd as $key => &$prod) {
            if ($prod['id'] === $id) {
                if ($prod['quantity'] > 1) {
                    $prod['quantity'] -= 1;
                    $prod['price'] = $prod['quantity'] * Product::find($id)->price;
                }else{
                    unset($this->productAdd[$key]);
                }
            }
        }
    }
    public function render()
    {
        return view('livewire.pages.admin.operational.pos.index', [
            'product' => Product::when($this->selectedCategory, function ($query) {
                $query->where('product_category_id', $this->selectedCategory);
            })->get(),
            'productCategory' => ProductCategory::all(),
        ]);
    }
}
