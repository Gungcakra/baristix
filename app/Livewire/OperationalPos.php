<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('layouts.admin')]  
class OperationalPos extends Component
{
    public function render()
    {
        return view('livewire.pages.admin.operational.pos.index',[
            'product-categories' => ProductCategory::all(),
        ]);
    }
}
