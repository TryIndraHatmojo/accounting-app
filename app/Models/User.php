<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

#[Fillable(['role_id', 'name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements HasDefaultTenant, HasTenants
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)->withTimestamps();
    }

    /**
     * @return Collection<int, Company>
     */
    public function getTenants(Panel $panel): Collection
    {
        return $this->companies()->where('is_active', true)->orderBy('name')->get();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $tenant instanceof Company
            && $tenant->is_active
            && $this->companies()->whereKey($tenant)->exists();
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        $selectedCompanyId = session('selected_company_id');

        if (! $selectedCompanyId) {
            return null;
        }

        return $this->companies()
            ->whereKey($selectedCompanyId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
