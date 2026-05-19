<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RolePermissionSeeder::class,
        ]);

        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@catzonesia.com',
        ]);

        $user->assignRole('superadmin');

        $this->call([
            MUnitSeeder::class,
            MCategorySeeder::class,
            MProductSeeder::class,
            MPackageSeeder::class,
            ProductCategorySeeder::class,
            ProductUnitConverterSeeder::class,
            PackageProductSeeder::class,
            TransactionSeeder::class,
            TransactionDetailSeeder::class,
            TransactionGroomingSeeder::class,
            TransactionLogSeeder::class,
            ProductMovementSeeder::class,
        ]);
    }
}
