<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions = [
            'view medicines',
            'create medicines',
            'edit medicines',
            'delete medicines',
            
            'create sale',
            'view sale',
            'return sale',
            
            'create purchase',
            'edit purchase',
            
            'view reports',
            
            'manage staff'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // create roles and assign created permissions

        // Cashier
        $roleCashier = Role::create(['name' => 'Cashier']);
        $roleCashier->givePermissionTo(['create sale', 'view sale']);

        // Pharmacist
        $rolePharmacist = Role::create(['name' => 'Pharmacist']);
        $rolePharmacist->givePermissionTo(['view medicines', 'create medicines', 'edit medicines', 'create sale', 'view sale', 'return sale']);

        // Pharmacy Owner
        $roleOwner = Role::create(['name' => 'Pharmacy Owner']);
        $roleOwner->givePermissionTo(Permission::all());

        // Staff
        $roleStaff = Role::create(['name' => 'Staff']);
        $roleStaff->givePermissionTo(['view medicines', 'view sale']);

        // Super Admin (Gets all permissions via Gate::before in AuthServiceProvider usually, but we can assign here or just use name)
        $roleSuperAdmin = Role::create(['name' => 'Super Admin']);
        // Super admin gets all by default in app logic
    }
}
