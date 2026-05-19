<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Master Category
            'master.category.view',
            'master.category.create',
            'master.category.edit',
            'master.category.delete',

            // Master Unit
            'master.unit.view',
            'master.unit.create',
            'master.unit.edit',
            'master.unit.delete',

            // Master Product
            'master.product.view',
            'master.product.create',
            'master.product.edit',
            'master.product.delete',

            // Master Package
            'master.package.view',
            'master.package.create',
            'master.package.edit',
            'master.package.delete',

            // Product Movement
            'product-movement.view',
            'product-movement.create',
            'product-movement.edit',
            'product-movement.delete',
            'product-movement.adjustment',

            // Transaction
            'transaction.view',
            'transaction.create',
            'transaction.delete',
            'transaction.mark-success',
            'transaction.mark-failed',
            'transaction.export',

            // Cashier
            'cashier.view',
            'cashier.checkout',
            'cashier.mark-success',
            'cashier.mark-failed',

            // User Management - User
            'user-management.user.view',
            'user-management.user.create',
            'user-management.user.edit',
            'user-management.user.delete',

            // User Management - Role
            'user-management.role.view',
            'user-management.role.create',
            'user-management.role.edit',
            'user-management.role.delete',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        $superadmin = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'superadmin']);
        $superadmin->syncPermissions($permissions);
    }
}
