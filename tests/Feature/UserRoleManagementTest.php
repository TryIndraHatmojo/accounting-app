<?php

namespace Tests\Feature;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserRoleManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_root_route_redirects_to_admin_panel(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/admin');
    }

    public function test_database_seeder_creates_roles_and_accounts(): void
    {
        $this->seed();

        foreach (['Admin', 'Gudang', 'Akuntan'] as $roleName) {
            $this->assertDatabaseHas(Role::class, ['name' => $roleName]);
        }

        $accounts = [
            'admin@accounting.test' => 'Admin',
            'gudang@accounting.test' => 'Gudang',
            'akuntan@accounting.test' => 'Akuntan',
        ];

        foreach ($accounts as $email => $roleName) {
            $user = User::query()->where('email', $email)->firstOrFail();

            $this->assertSame($roleName, $user->role->name);
            $this->assertNotNull($user->email_verified_at);
            $this->assertTrue(password_verify('password', $user->password));
        }
    }

    public function test_admin_can_create_a_role_from_filament(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateRole::class)
            ->fillForm(['name' => 'Manajer Ekspor'])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas(Role::class, ['name' => 'Manajer Ekspor']);
    }

    public function test_admin_can_create_a_user_with_a_role_from_filament(): void
    {
        $admin = User::factory()->create();
        $role = Role::factory()->create(['name' => 'Supervisor Gudang']);

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Budi Santoso',
                'email' => 'budi@accounting.test',
                'role_id' => $role->id,
                'password' => 'rahasia123',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $user = User::query()->where('email', 'budi@accounting.test')->firstOrFail();

        $this->assertTrue($user->role->is($role));
        $this->assertTrue(password_verify('rahasia123', $user->password));
    }
}
