<?php

namespace Database\Seeders;

use App\Models\Company;
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
                'email' => 'admin@example.com',
            ],
            [
                'role' => 'Gudang',
                'name' => 'Petugas Gudang',
                'email' => 'gudang@example.com',
            ],
            [
                'role' => 'Akuntan',
                'name' => 'Akuntan',
                'email' => 'akuntan@example.com',
            ],
        ];

        foreach ($accounts as $account) {
            $user = User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'role_id' => Role::query()->where('name', $account['role'])->valueOrFail('id'),
                    'name' => $account['name'],
                    'email_verified_at' => now(),
                    'password' => 'password',
                ],
            );

            $user->companies()->syncWithoutDetaching(Company::query()->pluck('id'));
        }
    }
}
