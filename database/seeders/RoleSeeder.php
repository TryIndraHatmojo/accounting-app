<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Admin', 'Gudang', 'Akuntan'] as $roleName) {
            Role::query()->firstOrCreate(['name' => $roleName]);
        }
    }
}
