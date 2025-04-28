<?php

use App\Livewire\EmployeeManagement;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Departement;
use App\Livewire\MenuManagement;
use App\Livewire\Product;
use App\Livewire\ProductCategory;
use App\Livewire\RolesPermissions;
use App\Livewire\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});
Route::get('/login', Login::class)->name('login');
Route::get('/logout', [Login::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/user', User::class)->name('user');
    Route::get('/employee', EmployeeManagement::class)->name('employee');
    Route::get('/menu', MenuManagement::class)->name('menu');
    Route::get('/roles',RolesPermissions::class)->name('role');
    Route::get('/departement', Departement::class)->name('departement');
    Route::get('/product', Product::class)->name('product');
    Route::get('/product-category', action: ProductCategory::class)->name('product-category');
    Route::get('/test', MenuManagement::class)->name('test');
});
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});