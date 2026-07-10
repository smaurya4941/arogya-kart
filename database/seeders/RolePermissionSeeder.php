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
            Permission::firstOrCreate(['name' => $permission]);
        }

        // create roles and assign created permissions (idempotent so the seeder
        // can be re-run during deploys without unique-constraint failures)

        // Cashier
        $roleCashier = Role::firstOrCreate(['name' => 'Cashier']);
        $roleCashier->syncPermissions(['create sale', 'view sale']);

        // Pharmacist
        $rolePharmacist = Role::firstOrCreate(['name' => 'Pharmacist']);
        $rolePharmacist->syncPermissions(['view medicines', 'create medicines', 'edit medicines', 'create sale', 'view sale', 'return sale']);

        // Pharmacy Owner
        $roleOwner = Role::firstOrCreate(['name' => 'Pharmacy Owner']);
        $roleOwner->syncPermissions(Permission::all());

        // Staff
        $roleStaff = Role::firstOrCreate(['name' => 'Staff']);
        $roleStaff->syncPermissions(['view medicines', 'view sale']);

        // Super Admin — platform owner; granted everything explicitly.
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $roleSuperAdmin->syncPermissions(Permission::all());
    }
}
