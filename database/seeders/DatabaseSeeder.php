<?php

namespace Database\Seeders;

use App\Models\Departement;
use App\Models\Employee;
use App\Models\Menu;
use App\Models\ProductCategory;
use App\Models\SubMenu;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Departement::insert([
            ['name' => 'Manager', 'salary' => 5000000],
            ['name' => 'Cashier', 'salary' => 1500000],
            ['name' => 'Cook', 'salary' => 1800000],
            ['name' => 'Waiter', 'salary' => 1300000],
        ]);
        Employee::insert([
            ['name' => 'Budi Santoso', 'departement_id' => 1, 'phone' => '081234567890', 'address' => 'Jl. Merdeka No. 1'],
            ['name' => 'Siti Aminah', 'departement_id' => 2, 'phone' => '081234567891', 'address' => 'Jl. Sudirman No. 2'],
            ['name' => 'Agus Pratama', 'departement_id' => 2, 'phone' => '081234567892', 'address' => 'Jl. Gatot Subroto No. 3'],
            ['name' => 'Dewi Lestari', 'departement_id' => 2, 'phone' => '081234567893', 'address' => 'Jl. Diponegoro No. 4'],
            ['name' => 'Rina Kusuma', 'departement_id' => 2, 'phone' => '081234567894', 'address' => 'Jl. Ahmad Yani No. 5'],
            ['name' => 'Andi Wijaya', 'departement_id' => 3, 'phone' => '081234567895', 'address' => 'Jl. Kartini No. 6'],
            ['name' => 'Eka Saputra', 'departement_id' => 3, 'phone' => '081234567896', 'address' => 'Jl. RA Kartini No. 7'],
            ['name' => 'Fajar Hidayat', 'departement_id' => 3, 'phone' => '081234567897', 'address' => 'Jl. Imam Bonjol No. 8'],
            ['name' => 'Gita Permata', 'departement_id' => 3, 'phone' => '081234567898', 'address' => 'Jl. HOS Cokroaminoto No. 9'],
            ['name' => 'Hendra Gunawan', 'departement_id' => 3, 'phone' => '081234567899', 'address' => 'Jl. Teuku Umar No. 10'],
            ['name' => 'Indah Sari', 'departement_id' => 3, 'phone' => '081234567900', 'address' => 'Jl. Pattimura No. 11'],
            ['name' => 'Joko Susilo', 'departement_id' => 3, 'phone' => '081234567901', 'address' => 'Jl. WR Supratman No. 12'],
            ['name' => 'Kiki Ananda', 'departement_id' => 3, 'phone' => '081234567902', 'address' => 'Jl. Sisingamangaraja No. 13'],
            ['name' => 'Lina Hartati', 'departement_id' => 3, 'phone' => '081234567903', 'address' => 'Jl. Dr. Sutomo No. 14'],
            ['name' => 'Maya Puspita', 'departement_id' => 3, 'phone' => '081234567904', 'address' => 'Jl. Dr. Wahidin No. 15'],
            ['name' => 'Nina Anggraini', 'departement_id' => 4, 'phone' => '081234567905', 'address' => 'Jl. Pemuda No. 16'],
            ['name' => 'Oka Prasetyo', 'departement_id' => 4, 'phone' => '081234567906', 'address' => 'Jl. Veteran No. 17'],
            ['name' => 'Putri Ayu', 'departement_id' => 4, 'phone' => '081234567907', 'address' => 'Jl. Diponegoro No. 18'],
            ['name' => 'Rama Wijaya', 'departement_id' => 4, 'phone' => '081234567908', 'address' => 'Jl. Gajah Mada No. 19'],
            ['name' => 'Sari Dewi', 'departement_id' => 4, 'phone' => '081234567909', 'address' => 'Jl. Pahlawan No. 20'],
        ]);
    
        ProductCategory::insert([
            ['name' => 'Food'],
            ['name' => 'Drink'],
            ['name' => 'Snack'],
        ]);

        $developer = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $employee = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

        // Define permissions
        $permissions = [
            'dashboard',
            'masterdata-user',
            'masterdata-employee',
            'masterdata-menu',
            'masterdata-departement',
            'masterdata-role',
            'masterdata-product',
            'masterdata-product-category',
            'operational-pos',

                    
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $developer->syncPermissions([
            'dashboard',
            'masterdata-user',
            'masterdata-employee',
            'masterdata-menu',
            'masterdata-departement',
            'masterdata-role',
            'masterdata-product',
            'masterdata-product-category',
            'operational-pos',
        ]);

        $admin->syncPermissions([
            'dashboard',
            'masterdata-user',
            'masterdata-employee',
            'masterdata-role',
            'masterdata-menu',
            'masterdata-departement',
            'masterdata-product',
            'masterdata-product-category',
            'operational-pos',
        ]);

        $employee->syncPermissions([
            'dashboard'
        ]);

        // Create a developer user

        $developerUser = User::factory()->create([
            'name' => 'developer',
            'email' => 'dev@me',
            'password' => bcrypt('guarajadisini')
        ]);
        $developerUser->assignRole('developer');

        $admin =  User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123')
        ]);
        $admin->assignRole('admin');

        

        
        // Menu Dashboard
        $dashboard = Menu::create([
            'name' => 'Dashboard',
            'icon' => 'fa-solid fa-house',
            'route' => 'dashboard',
            'order' => 1,
        ]);

        SubMenu::create([
            'menu_id' => $dashboard->id,
            'name' => 'Home',
            'route' => 'dashboard',
            'order' => 1,
            'permission_id' => Permission::where('name', 'dashboard')->first()->id

        ]);

         // Menu Master Data
         $masterData = Menu::create([
            'name' => 'Master Data',
            'icon' => 'fa-solid fa-database',
            'route' => null,
            'order' => 2
        ]);

        Submenu::create([
            'menu_id' => $masterData->id,
            'name' => 'User',
            'route' => 'user',
            'order' => 0,
            'permission_id' => Permission::where('name', 'masterdata-user')->first()->id
        ]);

        Submenu::create([
            'menu_id' => $masterData->id,
            'name' => 'Role',
            'route' => 'role',
            'order' => 1,
            'permission_id' => Permission::where('name', 'masterdata-role')->first()->id
        ]);

        Submenu::create([
            'menu_id' => $masterData->id,
            'name' => 'Departement',
            'route' => 'departement',
            'order' => 2,
            'permission_id' => Permission::where('name', 'masterdata-departement')->first()->id
        ]);
        Submenu::create([
            'menu_id' => $masterData->id,
            'name' => 'Employee',
            'route' => 'employee',
            'order' => 3,
            'permission_id' => Permission::where('name', 'masterdata-employee')->first()->id
        ]);

        Submenu::create([
            'menu_id' => $masterData->id,
            'name' => 'Menu',
            'route' => 'menu',
            'order' => 4,
            'permission_id' => Permission::where('name', 'masterdata-menu')->first()->id
        ]);
        Submenu::create([
            'menu_id' => $masterData->id,
            'name' => 'Product',
            'route' => 'product',
            'order' => 5,
            'permission_id' => Permission::where('name', 'masterdata-product')->first()->id
        ]);
        Submenu::create([
            'menu_id' => $masterData->id,
            'name' => 'Product Category',
            'route' => 'product-category',
            'order' => 6,
            'permission_id' => Permission::where('name', 'masterdata-product-category')->first()->id
        ]);
       

        // Menu Operational
        $operational = Menu::create([
            'name' => 'Operational',
            'icon' => 'fa-solid fa-clipboard-list',
            'route' => null,
            'order' => 3
        ]);
        
        Submenu::create([
            'menu_id' => $operational->id,
            'name' => 'POS',
            'route' => 'operational-pos',
            'order' => 0,
            'permission_id' => Permission::where('name', 'operational-pos')->first()->id
        ]);

       

    }
}
