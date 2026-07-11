<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'role' => 'Admin',
                'name' => 'Administrator',
                'email' => 'admin@accounting.test',
            ],
            [
                'role' => 'Gudang',
                'name' => 'Petugas Gudang',
                'email' => 'gudang@accounting.test',
            ],
            [
                'role' => 'Akuntan',
                'name' => 'Akuntan',
                'email' => 'akuntan@accounting.test',
            ],
        ];

        foreach ($accounts as $account) {
            User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'role_id' => Role::query()->where('name', $account['role'])->valueOrFail('id'),
                    'name' => $account['name'],
                    'email_verified_at' => now(),
                    'password' => 'password',
                ],
            );
        }
    }
}
